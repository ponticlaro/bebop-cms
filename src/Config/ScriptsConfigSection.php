<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\Cms\Config\ScriptConfigitem;
use \Ponticlaro\Bebop\Cms\Patterns\ConfigSection;

class ScriptsConfigSection extends ConfigSection {
  
  /**
   * List of scripts
   * 
   * @var array
   */
  protected $scripts = [];

  /**
   * Array used to resolve enqueue hooks for dependencies
   * 
   * @var array
   */
  protected $resolve_deps = [];
  
  /**
   * Returns created configuration items
   * 
   * @return array List of created configuration items
   */
  public function getItems()
  {
    // Get full configuration
    $actions = $this->config->getAll();

    // Making sure 'register' actions are the first to be processed
    $actions = [
      'register'   => isset($actions['register']) ? $actions['register'] : [],
      'enqueue'    => isset($actions['enqueue']) ? $actions['enqueue'] : [],
      'deregister' => isset($actions['deregister']) ? $actions['deregister'] : [],
      'dequeue'    => isset($actions['dequeue']) ? $actions['dequeue'] : []
    ];

    // Handle actions
    foreach ($actions as $action => $config) {
      $this->handleAction($action, $config);
    }

    // Return scripts
    return $this->scripts;
  }

  /**
   * Handle a single script action
   * 
   * @param  string $action Script action
   * @param  array  $config Script configuration
   * @return object         Current class object
   */
  protected function handleAction($action, array $config)
  {
    if ($action == 'register') {
      
      foreach ($config as $script_config) {
        
        // Get script handle
        $script_handle = isset($script_config['handle']) ? $script_config['handle'] : null;

        // Add script config to scripts list
        if ($script_handle)
          $this->scripts[$script_handle] = $script_config;
      }
    }

    else {

      foreach ($config as $hook => $handles) {
        foreach ($handles as $script_handle) {
  
          // Get existing script config        
          $script_config = isset($this->scripts[$script_handle]) ? $this->scripts[$script_handle] : null;

          // Add fallback config
          if (!$script_config) {

            $script_config = [
              'handle' => $script_handle
            ];
          }

          // Add action to script hook
          $script_config = static::addActionToHook($script_config, $hook, $action);

          // Add script config to scripts list
          $this->scripts[$script_handle] = $script_config;
        }
      }
    }

    return $this;
  }

  /**
   * Adds a single action to the target script hook
   * 
   * @param array  $script_config Script configuration array
   * @param string $hook          Target hook name
   * @param string $action        Action to add
   */
  protected static function addActionToHook(array $config, $hook, $action)
  {
    // Making sure 'hooks' array exist
    if (!isset($config['hooks']))
      $config['hooks'] = [];

    // Making sure $hook exists as an array within 'hooks'
    if (!isset($config['hooks'][$hook]))
      $config['hooks'][$hook] = [];

    // Add $action to $hook
    if (!in_array($action, $config['hooks'][$hook]))
      $config['hooks'][$hook][] = $action;

    return $config;
  }
} 