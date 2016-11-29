<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\Cms\Config\ScriptConfigItem;
use \Ponticlaro\Bebop\Cms\Patterns\ConfigSection;

class ScriptsConfigSection extends ConfigSection {
  
  /**
   * List of items
   * 
   * @var array
   */
  protected $items = [];
  
  /**
   * Returns created configuration items
   * 
   * @return array List of created configuration items
   */
  public function getItems()
  {
    // Get full configuration
    $raw_actions = $this->config->getAll();

    // Making sure 'register' actions are the first to be processed
    $actions = [];

    if (isset($raw_actions['register']))
      $actions['register'] = $raw_actions['register'];

    if (isset($raw_actions['enqueue']))
      $actions['enqueue'] = $raw_actions['enqueue'];

    if (isset($raw_actions['deregister']))
      $actions['deregister'] = $raw_actions['deregister'];

    if (isset($raw_actions['dequeue']))
      $actions['dequeue'] = $raw_actions['dequeue'];

    // Handle actions
    foreach ($actions as $action => $config) {
      $this->handleAction($action, $config);
    }

    // Return scripts
    return $this->items;
  }

  /**
   * Handle a single configuration action
   * 
   * @param  string $action      Action name
   * @param  array  $config_data Action configuration data
   * @return object              Current class object
   */
  protected function handleAction($action, array $config_data)
  {
    if ($action == 'register') {
      
      foreach ($config_data as $config) {
        
        // Get handle
        $handle = isset($config['handle']) ? $config['handle'] : null;

        if ($handle) {

          // Get existing config        
          $prev_config = isset($this->items[$handle]) ? $this->items[$handle] : null;

          // Merge with previous config
          if ($prev_config)
            $config = array_replace_recursive($prev_config, $config);

          // Add config to items list
          $this->items[$handle] = $config;
        }
      }
    }

    else {

      foreach ($config_data as $hook => $handles) {
        foreach ($handles as $handle) {
  
          // Get existing config        
          $config = isset($this->items[$handle]) ? $this->items[$handle] : null;

          // Add fallback config
          if (!$config) {

            $config = [
              'handle' => $handle
            ];
          }

          // Add 'register' action to hook
          if ($action == 'enqueue')
            $config = static::addActionToHook($config, $hook, 'register');

          // Add action to hook
          $config = static::addActionToHook($config, $hook, $action);

          // Add config to items list
          $this->items[$handle] = $config;
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