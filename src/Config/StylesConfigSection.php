<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\Cms\Config\StyleConfigitem;
use \Ponticlaro\Bebop\Cms\Patterns\ConfigSection;

class StylesConfigSection extends ConfigSection {
  
  /**
   * List of styles
   * 
   * @var array
   */
  protected $styles = [];

  /**
   * Array used to resolve enqueue hooks dependencies
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

    // Return styles
    return $this->styles;
  }

  /**
   * Handle a single style action
   * 
   * @param  string $action Style action
   * @param  array  $config Style configuration
   * @return object         Current class object
   */
  protected function handleAction($action, array $config)
  {
    if ($action == 'register') {
      
      foreach ($config as $style_config) {
        
        // Get style handle
        $style_handle = isset($style_config['handle']) ? $style_config['handle'] : null;

        // Add style config to styles list
        if ($style_handle)
          $this->styles[$style_handle] = $style_config;
      }
    }

    else {

      foreach ($config as $hook => $handles) {
        foreach ($handles as $style_handle) {

          // Get existing style config        
          $style_config = isset($this->styles[$style_handle]) ? $this->styles[$style_handle] : null;

          // Add fallback config
          if (!$style_config) {

            $style_config = [
              'handle' => $style_handle
            ];
          }

          // Add action to style hook
          $style_config = static::addActionToHook($style_config, $hook, $action);

          // Add style config to styles list
          $this->styles[$style_handle] = $style_config;
        }
      }
    }

    return $this;
  }

  /**
   * Adds a single action to the target style hook
   * 
   * @param array  $config Style configuration array
   * @param string $hook   Target hook name
   * @param string $action Action to add
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