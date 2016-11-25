<?php 

namespace Ponticlaro\Bebop\Cms;

use Ponticlaro\Bebop\Cms\Helpers\ConfigFactory;
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
   * Array used to resolve enqueue hooks for script/style dependencies
   * 
   * @var array
   */
  protected $resolve_deps = [];

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
      if (ConfigFactory::canManufacture(key($config))) {
        
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

      if (ConfigFactory::canManufacture($section)) {
        
        foreach ($configs as $index => $config) {

          var_dump($config);

          $config_obj = ConfigFactory::create($section, $config);
        
          // Merge with preset, if it exists
          $preset = $this->config->get("presets.$env.$section.". $config_obj->getId());

          if ($preset)
            $config_obj = $preset->merge($config_obj);

          if ($config_obj->isValid()) {

            // Define path for config item
            $path = "$hook.$env.$section.". $config_obj->getId();

            // // Check if we have a previous configuration
            // $prev_config_obj = $this->config->get($path);

            // // Merge previous configuration with new one
            // if ($prev_config_obj)
            //   $config_obj = $prev_config_obj->merge($config_obj);
            
            // Add config item
            $this->config->set($path, $config_obj);

            /////////////////////////////////////////////
            // TO DO: Collect config item requirements //
            /////////////////////////////////////////////
            $config_obj->getRequirements();
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
          $env_config = $this->config->get("presets.{$this->current_env}.$section.". $config_obj->getId());

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

  /**
   * Returns preset configuration merged with custom config
   * 
   * @param  string $section Configuration section ID
   * @param  string $id      Item ID
   * @param  array  $config  Item configuration
   * @param  string $env     Environment ID
   * @return array           Preset config
   */
  protected function getPreset($section, $id, array $config, $env = 'all')
  {
    // Get preset config
    $id = static::getConfigId($section, [
      '_id' => $id
    ]);
    $preset_config = $this->config->get("presets.$env.$section.$id");
    $config        = $preset_config ? array_replace_recursive($preset_config, $config) : $config;

    // Check if preset have a requirements configuration
    if (isset($config['requires']) && is_array($config['requires']) && $config['requires'])
      $this->processHookEnvConfig('build', 'all', $config['requires']);

    // Return merged preset config with custom config 
    return $config;
  }

  /**
   * Collects script dependencies
   * 
   * @param  string $type   Script type: CSS or JS
   * @param  string $handle Script handle
   * @param  array  $deps   Script dependencies
   * @return object         This class instance
   */
  protected function collectScriptDependencies($type, $handle, array $deps = [])
  {
    if (is_string($type) && is_string($handle) && $deps) {
      if (!isset($this->resolve_deps[$type])) {

        $this->resolve_deps[$type] = [
          'main' => [],
          'deps' => []
        ];
      }

      if (!isset($this->resolve_deps[$type]['main'][$handle]))
        $this->resolve_deps[$type]['main'][$handle] = [];

      foreach ($deps as $dep_handle) {
        $this->resolve_deps[$type]['main'][$handle][] = $this->getConfigId($type, [
          'handle' => $dep_handle
        ]);
      }
    }

    return $this;
  }

  /**
   * Collects a single enqueue hook for all depencencies of the target script
   * 
   * @param  string $type   Script type: CSS or JS
   * @param  string $handle Script handle
   * @param  string $hook   Enqueue hook to be added
   * @return object         This class instance
   */
  protected function collectScriptDependencyHook($type, $handle, $hook)
  {
    if (is_string($type) && is_string($handle) && is_string($hook)) {
      if (isset($this->resolve_deps[$type]) && isset($this->resolve_deps[$type]['main'][$handle])) {
        foreach ($this->resolve_deps[$type]['main'][$handle] as $dep_handle) {
          
          if (!isset($this->resolve_deps[$type]['deps'][$dep_handle]))
            $this->resolve_deps[$type]['deps'][$dep_handle] = [];

          if (!in_array($hook, $this->resolve_deps[$type]['deps'][$dep_handle])) {
            $this->resolve_deps[$type]['deps'][$dep_handle][] = $hook;
          }
        }
      }
    }

    return $this;
  }

  /**
   * Returns script enqueue hooks, when used as a script dependency
   * 
   * @param  string $handle Script handle
   * @return array          Script enqueue hooks
   */
  protected function getScriptEnqueueHooksAsDependency($type, $handle)
  {
    if (is_string($type) && is_string($handle) && isset($this->resolve_deps[$type]['deps'][$handle]))
        return $this->resolve_deps[$type]['deps'][$handle];

    return [];
  }
}