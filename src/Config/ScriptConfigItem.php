<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\ScriptsLoader\Js;
use \Ponticlaro\Bebop\Cms\Patterns\ConfigItem;

class ScriptConfigItem extends ConfigItem {

  /**
   * Checks if configuration is valid
   * 
   * @return boolean True if valid, false otherwise
   */
  public function isValid()
  {
    $valid  = true;
    $handle = $this->config->get('handle');
    $src    = $this->config->get('src');
    $hooks  = $this->config->get('hooks');

    // 'handle' must be a string
    if (!$handle || !is_string($handle))
      $valid = false;

    // 'src' must be a string
    if (!$src || !is_string($src))
      $valid = false;

    // 'hooks' must be an array
    if (!$hooks || !is_array($hooks))
      $valid = false;

    return $valid;
  }
  
  /**
   * Builds configuration item
   * 
   * @return object Current object
   */
  public function build()
  {
    // Get JS manager
    $js = JS::getInstance();

    // Get script hooks
    $hooks = $this->config->get('hooks') ?: [];

    // Handle each hook
    foreach ($hooks as $hook_name => $actions) {
      
      // Handle actions for each hook
      if ($hook = $js->getHook($hook_name)) {

        // Get 'register' action key
        $register_action_key = array_search('register', $actions);

        // We must handle the 'register' action in first place
        if ($register_action_key !== false) {

          // Register script
          $hook->$action(
            $this->config->get('handle'),
            $this->config->get('src'),
            $this->config->get('deps') ?: [],
            $this->config->get('version') ?: null,
            $this->config->get('media') ?: null
          );

          // Remove 'register' action
          unset($actions[$register_action_key]);
        }

        // Handle remaining actions
        foreach ($actions as $action) {
          $hook->$action($this->config->get('handle'));
        }
      }
    }
  }

  /**
   * Adds a single action to an hook
   * 
   * @param string $hook   Scripts hook name
   * @param string $action Script action name
   */
  public function addActionToHook($hook, $action)
  {
    if (is_string($hook) && is_string($action)) {
      
      if (!$this->config->hasKey('hooks'))
        $this->config->set('hooks', []);

      if (!$this->config->hasValue($action, "hooks.$hook"))
        $this->config->push('hooks.$hook', []);
    }
  }
} 