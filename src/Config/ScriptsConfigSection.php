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
    // Get sorted configuration
    $actions = static::getSortedConfig($this->config->getAll());

    // Handle actions
    foreach ($actions as $action => $config) {
      $this->handleAction($action, $config);
    }

    // Return scripts
    return $this->items;
  }

  /**
   * Sorts actions so that register actions are handled first
   * 
   * @param  array  $config Actions configuration data
   * @return array          Sorted configuration data
   */
  protected static function getSortedConfig(array $config)
  {
    $sorted_config = [];

    if (isset($config['register']))
      $sorted_config['register'] = $config['register'];

    if (isset($config['enqueue']))
      $sorted_config['enqueue'] = $config['enqueue'];

    if (isset($config['deregister']))
      $sorted_config['deregister'] = $config['deregister'];

    if (isset($config['dequeue']))
      $sorted_config['dequeue'] = $config['dequeue'];

    return $sorted_config;
  }

  /**
   * Handle a single configuration action
   * 
   * @param  string $action Action name
   * @param  array  $config Action configuration data
   * @return object         Current class object
   */
  protected function handleAction($action, array $config)
  {
    // Handle registration
    if ($action == 'register') {
      
      foreach ($config as $item) {
        $this->handleItemRegistration($item);
      }

      return $this;
    }

    // Handle all other actions
    foreach ($config as $hook => $handles) {
      $this->handleActionOnHook($action, $hook, $handles);
    }

    return $this;
  }

  /**
   * Handle a single script registration config section
   * 
   * @param  array $config Configuration data
   * @return void
   */
  protected function handleItemRegistration(array $config = [])
  {
    // Get handle
    $handle = isset($config['handle']) ? $config['handle'] : null;

    if (!$handle)
      return;

    // Get existing config        
    $prev_config = isset($this->items[$handle]) ? $this->items[$handle] : null;

    // Merge with previous config
    if ($prev_config)
      $config = array_replace_recursive($prev_config, $config);

    // Add config to items list
    $this->items[$handle] = $config;
  }

  /**
   * Handle deregister, enqueue and dequeue actions on a hook
   * 
   * @param  array $config Configuration data
   * @return void
   */
  protected function handleActionOnHook($action, $hook, array $handles = [])
  { 
    if (!is_string($action))
      throw new \Exception("ScriptsSection action must be a string");

    if (!is_string($hook))
      throw new \Exception("ScriptSection hook must be a string");

    foreach ($handles as $handle) {

      // Get existing config; else set fallback config    
      $config = isset($this->items[$handle]) ? $this->items[$handle] : ['handle' => $handle];

      // Add 'register' action to hook
      if ($action == 'enqueue')
        $config = static::addActionToHook($config, $hook, 'register');

      // Add action to hook
      $config = static::addActionToHook($config, $hook, $action);

      // Add config to items list
      $this->items[$handle] = $config;
    }
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