<?php 

namespace Ponticlaro\Bebop\Cms;

use Ponticlaro\Bebop\Cms\Helpers\ConfigItemFactory;
use Ponticlaro\Bebop\Common\Collection;
use Ponticlaro\Bebop\Common\EnvManager;
use Ponticlaro\Bebop\Common\PathManager;
use Ponticlaro\Bebop\Common\Utils;

class Config extends \Ponticlaro\Bebop\Common\Patterns\SingletonAbstract {

  /**
   * Hooks
   * This specifies the order in which configurations will be processed
   * 
   * @var Ponticlaro\Bebop\Common\Collection
   */ 
  protected $hooks;

  /**
   * Configuration
   * 
   * @var Ponticlaro\Bebop\Common\Collection
   */
  protected $config;

  /**
   * Holds the current environment key
   * 
   * @var
   */
  protected $current_env;

  /**
   * Flags if the configuration was already built or not
   * 
   * @var boolean
   */
  protected $already_built = false;

  /**
   * Instantiates this class
   * 
   */
  public function __construct()
  {
    // Set current environment key
    $this->current_env = EnvManager::getInstance()->getCurrentKey();

    // Get paths manager
    $paths = PathManager::getInstance();

    // Create hooks base structure 
    $this->hooks = new Collection([
      'presets' => [
        __DIR__ .'/presets.json',
        $paths->get('theme', 'bebop-presets.json')
      ],
      'build' => [
        $paths->get('theme', 'bebop.json')
      ]
    ]);

    // Create config collection 
    $this->config = new Collection();

    // Build configuration on the after_setup_theme hook
    add_action('after_setup_theme', [$this, 'build']);
  }

  /**
   * Adds configuration to target hook
   * 
   * @param string $hook   Hook ID
   * @param mixed  $config Configuration JSON file or array
   */
  protected function addToHook($hook, $config)
  {
    $this->hooks->push($config, $hook);

    return $this;
  }

  /**
   * Sets target hook
   * 
   * @param string $hook   Hook ID
   * @param mixed  $config Path to configuration JSON file or array
   */
  protected function setHook($hook, $config)
  {
    $this->hooks->set($hook, $config);

    return $this;
  }

  /**
   * Gets configuration for the target hook
   * 
   * @param  string $hook Hook ID
   * @return array        Path to configuration JSON file or array
   */
  protected function getHook($hook)
  {
    return $this->hooks->get($hook);
  }

  /**
   * Clears target hook
   * 
   * @param string $hook Hook ID
   */
  protected function clearHook($hook)
  {
    $this->hooks->set($hook, []);

    return $this;
  }

  /**
   * Runs existing hooks 
   * 
   * @return void
   */
  protected function runHooks()
  {
    foreach ($this->hooks->getAll() as $hook => $configs) {
      foreach ($configs as $config) {
        $this->processConfig($hook, $config);
      }
    }
  }

  /**
   * Processes a single JSON schema array
   * 
   * @param  string $hook   Either 'preset' or 'build' configuration
   * @param  mixed  $config Array or JSON containing valid JSON schema
   * @return object         This class instance
   */
  protected function processConfig($hook, $config)
  {
    // Get $config contents and decode JSON if it is a path to afile
    if (is_string($config) && file_exists($config) && is_readable($config))
      $config = json_decode(file_get_contents($config), true);  

    if (is_array($config)) {

      // Backward compatibility
      // https://github.com/ponticlaro/bebop-cms/issues/22
      if (isset($config['environments']) && $config['environments'])
        $config = $config['environments'];

      // Handle configuration without environment specific sections
      if (ConfigItemFactory::canManufacture(key($config))) {
        
        $this->processHookEnvConfig($hook, 'all', $config);
      }

      // Handle configuration with environment specific sections
      else {

        foreach ($config as $environment => $environment_config) {

          $this->processHookEnvConfig($hook, $environment, $environment_config);
        }
      }
    }

    return $this;
  }

  /**
   * Processes hook configuration for a target environment
   *  
   * @param  string $hook       Hook ID
   * @param  string $env        Environment ID
   * @param  mixed  $env_config Configuraton array
   * @return void
   */
  protected function processHookEnvConfig($hook, $env, array $env_config)
  {
    foreach ($env_config as $section => $configs) {

      // Handle configuration sections with specific implementations,
      // not based on a list of configuration items
      // if (ConfigSectionFactory::canManufacture($section)) {
      //   $configs = ConfigSectionFactory::create($section, $configs);
      // }

      if (ConfigItemFactory::canManufacture($section)) {
        
        foreach ($configs as $index => $config) {

          // Create configuration item
          $config_obj = ConfigItemFactory::create($section, $config);

          // Only for other non-preset configuration items
          if ($hook != 'presets') {

            // Get preset path
            $preset_path = "presets.$env.$section.". $config_obj->getPresetId();

            // Merge with preset, if it exists
            if ($preset = $this->config->get($preset_path)) {

              $config_obj = $preset->merge($config_obj);

              // Making sure we do not process 'preset' a second time
              $config_obj->remove('preset');
            }
          }

          // Handle configuration object, if:
          // - We're adding a preset; presets do not need to be valid
          // - We're adding a valid item to the build
          if ($hook == 'presets' || $config_obj->isValid()) {

            // Getting the correct configuration id
            $id = $hook == 'presets' ? $config_obj->getId() : $config_obj->getUniqueId();

            // Define path for config item
            $path = "$hook.$env.$section.$id";

            // Check if we have a previous configuration
            $prev_config_obj = $this->config->get($path);

            // Merge previous configuration with new one
            if ($prev_config_obj)
              $config_obj = $prev_config_obj->merge($config_obj);

            // Add config item
            $this->config->set($path, $config_obj);

            // Handle configuration item requirements
            if ($hook != 'presets' && $requirements_config = $config_obj->getRequirements())
              $this->processHookEnvConfig($hook, $env, $requirements_config);
          }
        }
      }
    }   
  }

  /**
   * Build configuration
   * 
   * @return object This class instance
   */
  public function build()
  {
    if ($this->already_built)
      return $this;

    $this->runHooks();

    if ($this->config->get('build.all')) {
      foreach ($this->config->get('build.all') as $section => $configs) {
        foreach ($configs as $id => $config_obj) {

          // Merge with current environment configuration, if it exists
          $env_config = $this->config->get("build.{$this->current_env}.$section.". $config_obj->getId());

          if ($env_config)
            $config_obj = $config_obj->merge($env_config);

          // Build config object
          $config_obj->build();
        }
      }
    }

    $this->already_built = true;

    return $this;
  }
}