<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\Cms\Config\StyleConfigitem;
use \Ponticlaro\Bebop\Cms\Patterns\ConfigSection;

class StylesConfigSection extends ConfigSection {
  
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