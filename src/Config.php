<?php 

namespace Ponticlaro\Bebop\Cms;

use Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory;
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
  protected static $config_section_map = [
    'admin_pages' => [
      'identifier' => 'title',
      'process'    => 'processAdminPage',
      'build'      => 'buildAdminPage'
    ],
    'image_sizes' => [
      'identifier' => 'name',
      'process'    => 'processImageSize',
      'build'      => 'buildImageSize'
    ],
    'metaboxes' => [
      'identifier' => 'title',
      'process'    => 'processMetabox',
      'build'      => 'buildMetabox'
    ],
    'paths' => [
      'identifier' => 'id',
      'process'    => 'processPath',
      'build'      => 'buildPath'
    ],
    'scripts' => [
      'identifier' => 'handle',
      'process'    => 'processScriptAction',
      'build'      => 'buildScript'
    ],
    'shortcodes' => [
      'identifier' => 'id',
      'process'    => 'processShortcode',
      'build'      => 'buildShortcode'
    ],
    'styles' => [
      'identifier' => 'handle',
      'process'    => 'processStyleAction',
      'build'      => 'buildStyle'
    ],
    'taxonomies' => [
      'identifier' => 'name',
      'process'    => 'processTaxonomy',
      'build'      => 'buildTaxonomy'
    ],
    'types' => [
      'identifier' => 'name',
      'process'    => 'processType',
      'build'      => 'buildType'
    ],
    'urls' => [
      'identifier' => 'id',
      'process'    => 'processUrl',
      'build'      => 'buildUrl'
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
        if (isset(static::$config_section_map[$section]) && isset(static::$config_section_map[$section]['process']))
          call_user_func_array([$this, static::$config_section_map[$section]['process']], [$index, $config, $hook, $env]);
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

          // Making sure 'register' actions are the first to be processed
          if (in_array($section, ['scripts', 'styles'])) {

            $config = [
              'register'   => isset($config['register']) ? $config['register'] : [],
              'enqueue'    => isset($config['enqueue']) ? $config['enqueue'] : [],
              'deregister' => isset($config['deregister']) ? $config['deregister'] : [],
              'dequeue'    => isset($config['dequeue']) ? $config['dequeue'] : []
            ];
          }

          if (isset(static::$config_section_map[$section]) && isset(static::$config_section_map[$section]['build']))
            call_user_func_array([$this, static::$config_section_map[$section]['build']], [$id, $config]);
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
    if (!$this->isConfigItemValid($hook, 'admin_pages', $index, $config))
      return $this;

    // Get id & path
    $id   = static::getConfigId('admin_pages', $config);
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
    new AdminPage($config);
  }

  /**
   * Processes a single image size configuration
   *
   * @param  array  $index  Configuration array index
   * @param  array  $config Configuration array
   * @param  string $hook   Hook ID
   * @param  string $env    Environment ID
   * @return void
   */
  protected function processImageSize($index, array $config, $hook = 'build', $env = 'all')
  {
    // Get preset, if we're dealing with one
    if (isset($config['preset']) && $config['preset'])
      $config = $this->getPreset('image_sizes', $config['preset'], $config, $env);
    
    // Add 'crop' as false by default
    if (!isset($config['crop']))
      $config['crop'] = false;

    // Check if item is valid
    if (!$this->isConfigItemValid($hook, 'image_sizes', $index, $config))
      return $this;

    // Get id & path
    $id   = static::getConfigId('image_sizes', $config);
    $path = "$hook.$env.image_sizes.$id";

    // Upsert item
    $this->upsertConfigItem($path, $config);
  }

  /**
   * Builds a single image size
   * 
   * @param  string $id     Image size configuration ID
   * @param  array  $config Image size configuration array
   * @return void
   */
  protected function buildImageSize($id, array $config)
  {
    // Merge current environment config with main config
    if ($current_env_config = $this->config->get("build.$this->current_env.image_sizes.$id"))
      $config = array_replace_recursive($config, $current_env_config);

    // Register custom image size
    add_image_size($config['name'], $config['width'], $config['height'], $config['crop']);
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
    if (!$this->isConfigItemValid($hook, 'metaboxes', $index, $config))
      return $this;

    // Get id & path
    $id   = static::getConfigId('metaboxes', $config);
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
    if (!$this->isConfigItemValid($hook, 'paths', $index, $path))
      return $this;

    // Get id
    $id = static::getConfigId('paths', [
      'id' => $id
    ]);

    // Upsert item
    $this->upsertConfigItem("$hook.$env.paths.$id", $path);
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
        if (!$this->isConfigItemValid($hook, 'scripts', $action, $script))
          return $this;

        // Get script ID
        $script_id = static::getConfigId('scripts', $script);

        // Collect dependencies
        if (isset($script['deps']) && is_array($script['deps']))
          $this->collectScriptDependencies('scripts', $script['handle'], $script['deps']);

        // Upsert item
        $this->upsertConfigItem("$hook.$env.scripts.$script_id.$action", $script);
      }
    }

    else {

      foreach ($config as $script_hook_name => $script_hook_config) {
        foreach ($script_hook_config as $script_handle) {

          // Get script ID
          $script_id = static::getConfigId('scripts', [
            'handle' => $script_handle
          ]);

          // Collect dependencies enqueue hooks
          $this->collectScriptDependencyHook('scripts', $script_handle, $script_hook_name);

          // Set script config action path
          $path = "$hook.$env.scripts.$script_id.$action";

          // Create array if it doesn't exist
          if (!$this->config->hasKey($path))
            $this->config->set($path, []);

          // Add new hook to list
          if (!$this->config->hasValue($script_hook_name, $path))
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

    // Check if script have enqueue hooks as a dependency
    if ($enqueue_hooks_as_dep = $this->getScriptEnqueueHooksAsDependency('scripts', $handle)) {
      foreach ($enqueue_hooks_as_dep as $hook) {
        $config['enqueue'][] = $hook;
      }
    }

    // Handle register and enqueue
    if (isset($config['enqueue']) && $config['enqueue'] && 
        isset($config['register']) && $config['register']) {

      foreach ($config['enqueue'] as $script_hook_name) {
        
        $js->getHook($script_hook_name)
           ->register(
              $config['register']['handle'],
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
   * Processes shortcode groups
   * 
   * @param  string $index        Configuration array index
   * @param  string $shortcode_id Configuration array
   * @param  string $hook         Hook ID
   * @param  string $env          Environment ID
   * @return void
   */
  protected function processShortcode($index, array $config, $hook = 'build', $env = 'all')
  {
    // Get preset, if we're dealing with one
    if (isset($config['preset']) && $config['preset'])
      $config = $this->getPreset('shortcodes', $config['preset'], $config, $env);
    
    // Check if item is valid
    if (!$this->isConfigItemValid($hook, 'shortcodes', $index, $config))
      return $this;

    // Upsert item
    $this->upsertConfigItem("$hook.$env.shortcodes.". $config['id'], $config);
  }

  /**
   * Registers shortcodes within shortcode groups
   * 
   * @param  string $group  Shortcode Group name
   * @param  mixed  $config Shortcode Group configuration
   * @return void
   */
  protected function buildShortcode($id, array $config)
  {
    // Merge current environment config with main config
    if ($current_env_config = $this->config->get("build.$this->current_env.shortcodes.$id"))
      $config = array_replace_recursive($config, $current_env_config);
    
    if (ShortcodeFactory::canManufacture($id)) {
      
      $shortcode = ShortcodeFactory::create($id);

      if ($shortcode)
        $shortcode->register();
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
        if (!$this->isConfigItemValid($hook, 'styles', $action, $script))
          return $this;

        // Get script ID
        $script_id = static::getConfigId('styles', $script);

        // Collect dependencies
        if (isset($script['deps']) && is_array($script['deps']))
          $this->collectScriptDependencies('styles', $script['handle'], $script['deps']);

        // Upsert item
        $this->upsertConfigItem("$hook.$env.styles.$script_id.$action", $script);
      }
    }

    else {

      foreach ($config as $script_hook_name => $script_hook_config) {
        foreach ($script_hook_config as $script_handle) {

          // Get script ID
          $script_id = static::getConfigId('styles', [
            'handle' => $script_handle
          ]);

          // Collect dependencies enqueue hooks
          $this->collectScriptDependencyHook('styles', $script_handle, $script_hook_name);

          // Get script config action path
          $path = "$hook.$env.styles.$script_id.$action";

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

    // Check if script have enqueue hooks as a dependency
    if ($enqueue_hooks_as_dep = $this->getScriptEnqueueHooksAsDependency('css', $handle)) {
      foreach ($enqueue_hooks_as_dep as $hook) {
        $config['enqueue'][] = $hook;
      }
    }

    // Handle register and enqueue
    if (isset($config['enqueue']) && $config['enqueue'] && 
        isset($config['register']) && $config['register']) {

      foreach ($config['enqueue'] as $script_hook_name) {
        
        $css->getHook($script_hook_name)
            ->register(
              $config['register']['handle'],
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
    if (!$this->isConfigItemValid($hook, 'taxonomies', $index, $config))
      return $this;

    // Get id & path
    $id   = static::getConfigId('taxonomies', $config);
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
    if (!$this->isConfigItemValid($hook, 'types', $index, $config))
      return $this;

    // Get id
    $id   = static::getConfigId('types', $config);
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
    if (!$this->isConfigItemValid($hook, 'urls', $index, $url))
      return $this;

    // Get id
    $id = static::getConfigId('urls', [
      'id' => $id
    ]);

    // Upsert item
    $this->upsertConfigItem("$hook.$env.urls.$id", $url);
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
   * Returns safe config ID from its configuration array
   * 
   * @param  string $section Config section
   * @param  string $config Config
   * @return string         Config ID
   */
  protected static function getConfigId($section, array $config)
  {
    // Check if config have a 'preset' property
    $preset = isset($config['preset']) && $config['preset'] ? $config['preset'] : null;

    // Check if config have an '_id' property
    $id = isset($config['_id']) && $config['_id'] ? $config['_id'] : null;

    // Check if config have a value on its identifier property
    if (!$id) {

      // Get identifier property
      $identifier = isset(static::$config_section_map[$section]) ? static::$config_section_map[$section]['identifier'] : null;
      
      // Get raw id
      $id = $identifier && isset($config[$identifier]) ? $config[$identifier] : null;
    }

    // Set 'id' as 'preset', if we have one
    if (!$id && $preset)
      $id = $preset;

    // Return if there is no ID
    if (!$id)
      return null;

    // Making sure types and taxonomies get their IDs from the singular name
    if (is_array($id))
        $id = reset($id);

    // Slugify ID
    $id = Utils::slugify($id);

    // Making sure scripts and styles IDs do not have dots in them
    if ($id && in_array($section, ['scripts', 'styles']))
      $id = str_replace('.', '_', $id);

    return $id;
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
      if (isset($this->resolve_deps[$type]) && $this->resolve_deps[$type]['main'][$handle]) {
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
    if (is_string($type) && is_string($handle) && $this->resolve_deps[$type]['deps'][$handle])
        return $this->resolve_deps[$type]['deps'][$handle];

    return [];
  }

  /**
   * Validates configuration item
   * 
   * @param  string  $section Configuration section
   * @param  string  $index   Configuration array index
   * @param  array   $config  Configuration array
   * @return boolean          True if valid, false otherwise
   */
  protected function isConfigItemValid($hook, $section, $index, $config = [])
  {
    // Always return valid for presets
    if ($hook == 'presets')
      return true;

    switch ($section) {

      case 'admin_pages':
        return !isset($config['title']) || !$config['title'] || !is_string($config['title']) ? false : true;
        break;

      case 'image_sizes':

        $valid = true;

        if (!isset($config['name']) && $config['name'])
          $valid = false;

        if (!isset($config['width']))
          $valid = false;

        if (!isset($config['height']))
          $valid = false;

        if (!is_bool($config['crop']) && !is_array($config['crop']))
          $valid = false;

        return $valid;
        break;

      case 'metaboxes':
        return !isset($config['title']) || !$config['title'] || !isset($config['types']) || !$config['types'] ? false : true;
        break;

      case 'shortcodes':
        return isset($config['id']) && is_string($config['id']) ? true : false;
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
}