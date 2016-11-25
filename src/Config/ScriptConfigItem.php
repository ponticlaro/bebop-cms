<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\ScriptsLoader\Js;
use \Ponticlaro\Bebop\Cms\Patterns\ConfigItem;

class ScriptConfigItem extends ConfigItem {

  public function isValid()
  {
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
  }

  public function build()
  {
    // Making sure 'register' actions are the first to be processed
    $config = [
      'register'   => isset($config['register']) ? $config['register'] : [],
      'enqueue'    => isset($config['enqueue']) ? $config['enqueue'] : [],
      'deregister' => isset($config['deregister']) ? $config['deregister'] : [],
      'dequeue'    => isset($config['dequeue']) ? $config['dequeue'] : []
    ];
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