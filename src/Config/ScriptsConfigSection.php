<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\Cms\Config\ScriptConfigitem;
use \Ponticlaro\Bebop\Cms\Patterns\ConfigSection;

class ScriptsConfigSection extends ConfigSection {
  
  /**
   * Array used to resolve enqueue hooks for script/style dependencies
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
    $config = $this->config->getAll();
    $items  = [];

    // Making sure 'register' actions are the first to be processed
    $config = [
      'register'   => isset($config['register']) ? $config['register'] : [],
      'enqueue'    => isset($config['enqueue']) ? $config['enqueue'] : [],
      'deregister' => isset($config['deregister']) ? $config['deregister'] : [],
      'dequeue'    => isset($config['dequeue']) ? $config['dequeue'] : []
    ];



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



    return $items;
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