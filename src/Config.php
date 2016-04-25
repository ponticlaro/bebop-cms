<?php 

namespace Ponticlaro\Bebop\Cms;

use Ponticlaro\Bebop\Common\Collection;
use Ponticlaro\Bebop\Common\EnvManager;
use Ponticlaro\Bebop\Common\PathManager;
use Ponticlaro\Bebop\Common\UrlManager;
use Ponticlaro\Bebop\Common\Utils;
use Ponticlaro\Bebop\ScriptsLoader\Css;
use Ponticlaro\Bebop\ScriptsLoader\Js;

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
   * Sections mapping to process & build methods for single config items
   * 
   * @var array
   */
  protected $config_section_map = [
    'admin_pages' => [
      'process' => 'processAdminPage',
      'build'   => 'buildAdminPage'
    ],
    'metaboxes' => [
      'process' => 'processMetabox',
      'build'   => 'buildMetabox'
    ],
    'paths' => [
      'process' => 'processPath',
      'build'   => 'buildPath'
    ],
    'scripts' => [
      'process' => 'processScriptAction',
      'build'   => 'buildScript'
    ],
    'styles' => [
      'process' => 'processStyleAction',
      'build'   => 'buildStyle'
    ],
    'taxonomies' => [
      'process' => 'processTaxonomy',
      'build'   => 'buildTaxonomy'
    ],
    'types' => [
      'process' => 'processType',
      'build'   => 'buildType'
    ],
    'urls' => [
      'process' => 'processUrl',
      'build'   => 'buildUrl'
    ]
  ];

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
    $this->hooks->push($hook, $config);

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
        if (isset($config['environments']) && $config['environments']) {
          foreach ($config['environments'] as $environment => $environment_config) {

            $this->processHookEnvConfig($hook, $environment, $environment_config);
          }
        }

        else {

            $this->processHookEnvConfig($hook, 'all', $config);
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
      foreach ($configs as $index => $config) {
        if (isset($this->config_section_map[$section]) && isset($this->config_section_map[$section]['process']))
          call_user_func_array([$this, $this->config_section_map[$section]['process']], [$index, $config, $hook, $env]);
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

    if ($this->config->count()) {
      foreach ($this->config->get('build.all') as $section => $configs) {
        foreach ($configs as $id => $config) {

          if (isset($this->config_section_map[$section]) && isset($this->config_section_map[$section]['build']))
            call_user_func_array([$this, $this->config_section_map[$section]['build']], [$id, $config]);
        }
      }
    }

    $this->already_built = true;

    return $this;
  }

  /**
   * Processes a single admin page configuration
   *
   * @param  array  $index  Configuration array index
   * @param  array  $config Configuration array
   * @param  string $hook   Hook ID
   * @param  string $env    Environment ID
   * @return void
   */
  protected function processAdminPage($index, array $config, $hook = 'build', $env = 'all')
  {
    // Get preset, if we're dealing with one
    if (isset($config['preset']) && $config['preset'])
      $config = $this->getPreset('admin_pages', $config['preset'], $config, $env);
    
    // Check if item is valid
    if (!$this->isConfigItemValid('admin_pages', $index, $config))
      return $this;

    // Get id & path
    $id   = Utils::slugify(isset($config['id']) ? $config['id'] : $config['title']);
    $path = "$hook.$env.admin_pages.$id";

    // Upsert item
    $this->upsertConfigItem($path, $config);
  }

  /**
   * Builds a single admin page
   * 
   * @param  string $id     AdminPage configuration ID
   * @param  array  $config AdminPage configuration array
   * @return void
   */
  protected function buildAdminPage($id, array $config)
  {
    // Merge current environment config with main config
    if ($current_env_config = $this->config->get("build.$this->current_env.admin_pages.$id"))
      $config = array_replace_recursive($config, $current_env_config);

    // Register custom admin page
    $title = $config['title'];
    unset($config['title']);

    new AdminPage($title, $config);
  }

  /**
   * Processes a single metabox configuration
   *
   * @param  array  $index  Configuration array index
   * @param  array  $config Configuration array
   * @param  string $hook   Hook ID
   * @param  string $env    Environment ID
   * @return void
   */
  protected function processMetabox($index, array $config, $hook = 'build', $env = 'all')
  {
    // Get preset, if we're dealing with one
    if (isset($config['preset']) && $config['preset'])
      $config = $this->getPreset('metaboxes', $config['preset'], $config, $env);
    
    // Check if item is valid
    if (!$this->isConfigItemValid('metaboxes', $index, $config))
      return $this;

    // Get id & path
    $id   = Utils::slugify(isset($config['id']) ? $config['id'] : $config['name']);
    $path = "$hook.$env.metaboxes.$id";

    // Upsert item
    $this->upsertConfigItem($path, $config);
  }

  /**
   * Builds a single metabox
   * 
   * @param  string $id     Metabox configuration ID
   * @param  array  $config Metabox configuration array
   * @return void
   */
  protected function buildMetabox($id, array $config)
  {
    // Merge current environment config with main config
    if ($current_env_config = $this->config->get("build.$this->current_env.metaboxes.$id"))
      $config = array_replace_recursive($config, $current_env_config);

    // Register custom metabox
    new Metabox($config);
  }

  /**
   * Processes a single path configuration
   *
   * @param  array  $id   Path ID
   * @param  array  $path Path
   * @param  string $hook Hook ID
   * @param  string $env  Environment ID
   * @return void
   */
  protected function processPath($id, $path, $hook = 'build', $env = 'all')
  {
    // Check if item is valid
    if (!$this->isConfigItemValid('paths', $index, $path))
      return $this;

    // Upsert item
    $this->upsertConfigItem("$hook.$env.paths.". Utils::slugify($id), $path);
  }

  /**
   * Builds a single path
   * 
   * @param  string $id   Path ID
   * @param  array  $path Path
   * @return void
   */
  protected function buildPath($id, $path)
  {
    // Merge current environment config with main config
    if ($current_env_path = $this->config->get("build.$this->current_env.paths.$id"))
      $path = $current_env_path;

    // Register path
    PathManager::getInstance()->set($id, $path);
  }

  /**
   * Processes a single script action
   *
   * @param  array  $action Script action
   * @param  array  $config Configuration array
   * @param  string $hook   Hook ID
   * @param  string $env    Environment ID
   * @return void
   */
  protected function processScriptAction($action, array $config, $hook = 'build', $env = 'all')
  {
    if ($action == 'register') {
      foreach ($config as $script) {

        // Get preset, if we're dealing with one
        if (isset($config['preset']) && $config['preset'])
          $config = $this->getPreset('scripts', $config['preset'], $config, $env);

        // Check if item is valid
        if (!$this->isConfigItemValid('scripts', $action, $script))
          return $this;

        // Upsert item
        $this->upsertConfigItem("$hook.$env.scripts.". $script['handle'] .".$action", $script);
      }
    }

    else {

      foreach ($config as $script_hook_name => $script_hook_config) {
        foreach ($script_hook_config as $script_handle) {

          $path = "$hook.$env.scripts.$script_handle.$action";

          // Create array if it doesn't exist
          if (!$this->config->hasKey($path))
            $this->config->set($path, []);

          // Add new hook to list
          $this->config->push($script_hook_name, $path);
        }
      }
    }
  }

  /**
   * Builds a single script
   * 
   * @param  string $id     Script handle
   * @param  array  $config Script configuration array
   * @return void
   */
  protected function buildScript($handle, array $config)
  {
    // Get JS manager
    $js = JS::getInstance();

    // Merge current environment config with main config
    if ($current_env_config = $this->config->get("build.$this->current_env.scripts.$handle"))
      $config = array_replace_recursive($config, $current_env_config);

    // Handle register and enqueue
    if (isset($config['enqueue']) && $config['enqueue'] && 
        isset($config['register']) && $config['register']) {

      foreach ($config['enqueue'] as $script_hook_name) {
        
        $js->getHook($script_hook_name)
           ->register(
              $handle,
              $config['register']['src'], 
              isset($config['register']['deps']) ? $config['register']['deps']: [], 
              isset($config['register']['version']) ? $config['register']['version']: null, 
              isset($config['register']['in_footer']) ? $config['register']['in_footer']: null
           )
           ->enqueue($handle);
      }
    }

    unset($config['enqueue']);
    unset($config['register']);

    // Handle deregister and dequeue
    foreach ($config as $action => $action_config) {
      foreach ($action_config as $script_hook_name) {
        
        $js->getHook($script_hook_name)->$action($handle);
      }
    }
  }

  /**
   * Processes a single style action
   *
   * @param  array  $action Style action
   * @param  array  $config Configuration array
   * @param  string $hook   Hook ID
   * @param  string $env    Environment ID
   * @return void
   */
  protected function processStyleAction($action, array $config, $hook = 'build', $env = 'all')
  {
    if ($action == 'register') {
      foreach ($config as $script) {

        // Get preset, if we're dealing with one
        if (isset($config['preset']) && $config['preset'])
          $config = $this->getPreset('styles', $config['preset'], $config, $env);

        // Check if item is valid
        if (!$this->isConfigItemValid('styles', $action, $script))
          return $this;

        // Upsert item
        $this->upsertConfigItem("$hook.$env.styles.". $script['handle'] .".$action", $script);
      }
    }

    else {

      foreach ($config as $script_hook_name => $script_hook_config) {
        foreach ($script_hook_config as $script_handle) {

          $path = "$hook.$env.styles.$script_handle.$action";

          // Create array if it doesn't exist
          if (!$this->config->hasKey($path))
            $this->config->set($path, []);

          // Add new hook to list
          $this->config->push($script_hook_name, $path);
        }
      }
    }
  }

  /**
   * Builds a single style
   * 
   * @param  string $id     Style handle
   * @param  array  $config Style configuration array
   * @return void
   */
  protected function buildStyle($handle, array $config)
  {
    // Get CSS manager
    $css = CSS::getInstance();

    // Merge current environment config with main config
    if ($current_env_config = $this->config->get("build.$this->current_env.styles.$handle"))
      $config = array_replace_recursive($config, $current_env_config);

    // Handle register and enqueue
    if (isset($config['enqueue']) && $config['enqueue'] && 
        isset($config['register']) && $config['register']) {

      foreach ($config['enqueue'] as $script_hook_name) {
        
        $css->getHook($script_hook_name)
            ->register(
              $handle,
              $config['register']['src'], 
              isset($config['register']['deps']) ? $config['register']['deps']: [], 
              isset($config['register']['version']) ? $config['register']['version']: null, 
              isset($config['register']['media']) ? $config['register']['media']: null
            )
            ->enqueue($handle);
      }
    }

    unset($config['enqueue']);
    unset($config['register']);

    // Handle deregister and dequeue
    foreach ($config as $action => $action_config) {
      foreach ($action_config as $script_hook_name) {
        
        $css->getHook($script_hook_name)->$action($handle);
      }
    }
  }

  /**
   * Processes a single taxonomy configuration
   *
   * @param  array  $index  Configuration array index
   * @param  array  $config Configuration array
   * @param  string $hook   Hook ID
   * @param  string $env    Environment ID
   * @return void
   */
  protected function processTaxonomy($index, array $config, $hook = 'build', $env = 'all')
  {
    // Get preset, if we're dealing with one
    if (isset($config['preset']) && $config['preset'])
      $config = $this->getPreset('taxonomies', $config['preset'], $config, $env);
    
    // Check if item is valid
    if (!$this->isConfigItemValid('taxonomies', $index, $config))
      return $this;

    // Get id & path
    $id   = Utils::slugify(is_array($config['name']) ? $config['name'][0] : $config['name']);
    $path = "$hook.$env.taxonomies.$id";

    // Upsert item
    $this->upsertConfigItem($path, $config);
  }

  /**
   * Builds a single taxonomy
   * 
   * @param  string $id     Taxonomy configuration ID
   * @param  array  $config Taxonomy configuration array
   * @return void
   */
  protected function buildTaxonomy($id, array $config)
  {
    // Merge current environment config with main config
    if ($current_env_config = $this->config->get("build.$this->current_env.taxonomies.$id"))
      $config = array_replace_recursive($config, $current_env_config);

    // Register custom post-type
    (new Taxonomy($config['name'], $config['types']))->applyRawArgs($config);
  }

  /**
   * Processes a single post-type configuration
   *
   * @param  array  $index  Configuration array index
   * @param  array  $config Configuration array
   * @param  string $hook   Hook ID
   * @param  string $env    Environment ID
   * @return void
   */
  protected function processType($index, array $config, $hook = 'build', $env = 'all')
  {
    // Get preset, if we're dealing with one
    if (isset($config['preset']) && $config['preset'])
      $config = $this->getPreset('types', $config['preset'], $config, $env);
    
    // Check if item is valid
    if (!$this->isConfigItemValid('types', $index, $config))
      return $this;

    // Get id
    $id   = Utils::slugify(is_array($config['name']) ? $config['name'][0] : $config['name']);
    $path = "$hook.$env.types.$id";

    // Upsert item
    $this->upsertConfigItem($path, $config);
  }

  /**
   * Builds a single post-type
   * 
   * @param  string $id     Post-type configuration ID
   * @param  array  $config Post-type configuration array
   * @return void
   */
  protected function buildType($id, array $config)
  {
    // Merge current environment config with main config
    if ($current_env_config = $this->config->get("build.$this->current_env.types.$id"))
      $config = array_replace_recursive($config, $current_env_config);

    // Register custom post-type
    (new PostType($config['name']))->applyRawArgs($config);
  }

  /**
   * Processes a single url configuration
   *
   * @param  array  $id     URL ID
   * @param  array  $url    URL
   * @param  string $hook   Hook ID
   * @param  string $env    Environment ID
   * @return void
   */
  protected function processUrl($id, $url, $hook = 'build', $env = 'all')
  {
    // Check if item is valid
    if (!$this->isConfigItemValid('urls', $index, $url))
      return $this;

    // Upsert item
    $this->upsertConfigItem("$hook.$env.urls.". Utils::slugify($id), $url);
  }

  /**
   * Builds a single url
   * 
   * @param  string $id     URL ID
   * @param  array  $config URL
   * @return void
   */
  protected function buildUrl($id, $url)
  {
    // Merge current environment config with main config
    if ($current_env_url = $this->config->get("build.$this->current_env.urls.$id"))
      $url = $current_env_url;

    // Register url
    UrlManager::getInstance()->set($id, $url);
  }

  /**
   * Validates configuration item
   * 
   * @param  string  $section Configuration section
   * @param  string  $index   Configuration array index
   * @param  array   $config  Configuration array
   * @return boolean          True if valid, false otherwise
   */
  protected function isConfigItemValid($section, $index, $config = [])
  {
    switch ($section) {

      case 'admin_pages':
        return !isset($config['title']) || !$config['title'] || !is_string($config['title']) ? false : true;
        break;

      case 'metaboxes':
        return !isset($config['name']) || !$config['name'] || !isset($config['types']) || !$config['types'] ? false : true;
        break;

      case 'scripts':
      case 'styles':

        switch ($index) {
          
          case 'register':
            return true;
            break;
          
          case 'deregister':
          case 'dequeue':
          case 'enqueue':
            return true;
            break;
        }
        
        break;

      case 'types':
        return !isset($config['name']) || !$config['name'] ? false : true;
        break;
    
      case 'taxonomies':
        return !isset($config['name']) || !$config['name'] || !isset($config['types']) || !$config['types'] ? false : true;
        break;

      case 'paths':
      case 'urls':
        return true;
        break;
    }
  }

  /**
   * Inserts or updates configuration item
   * 
   * @param  string $path   Path to configuration item
   * @param  mixed  $config Item Configuration
   * @return void
   */
  protected function upsertConfigItem($path, $config)
  {
    $prev_config = $this->config->get($path);

    if ($prev_config && is_array($prev_config))
        $config = array_replace_recursive($prev_config, $config);

    $this->config->set($path, $config);
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
    $preset_config = $this->config->get("presets.$env.$section.". Utils::slugify($id));

    // Return merged preset config with custom config 
    return $preset_config ? array_replace_recursive($preset_config, $config) : $config;
  }
}