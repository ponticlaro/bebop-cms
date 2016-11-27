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
    $path   = $this->config->get('path');

    // 'handle' must be a string
    if (!$handle || !is_string($handle))
      $valid = false;

    // 'path' must be a string
    if (!$path || !is_string($path))
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
              isset($config['register']['in_footer']) ? $config['register']['in_footer']: true
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
} 