<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\ScriptsLoader\Js;
use \Ponticlaro\Bebop\Common\EventEmitter;
use \Ponticlaro\Bebop\Common\Patterns\EventEmitterInterface;
use \Ponticlaro\Bebop\Common\Patterns\EventConsumerTrait;
use \Ponticlaro\Bebop\Common\Patterns\EventConsumerInterface;
use \Ponticlaro\Bebop\Cms\Patterns\ConfigItem;

class ScriptConfigItem extends ConfigItem implements EventConsumerInterface {

  /**
   * Inherit default EventConsumerTrait attributes and methods
   */
  use EventConsumerTrait;

  /**
   * Configuration proprety of the ID
   */
  const IDENTIFIER = 'handle';

  /**
   * Instantiates configuration item
   * 
   * @param array $config Configuration array
   */
  public function __construct(array $config = [])
  {
    parent::__construct($config);

    // Define events channel
    $channel = 'cms.config.scripts.'. $this->config->get('handle');

    // Subscribe for events targeting this script
    $event_emitter = EventEmitter::getInstance()
                                 ->subscribe($channel, [$this, 'consumeEvent']);

    // Set event emitter
    $this->setEventEmitter($event_emitter);
  }

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

    // 'src' must be a string, if it exists
    if ($src && !is_string($src))
      $valid = false;

    // 'hooks' must be an array, if it exists
    if ($hooks && !is_array($hooks))
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
    // Get script hooks
    $hooks = $this->config->get('hooks') ?: [];

    // Handle each hook
    foreach ($hooks as $hook_name => $actions) {
      foreach ($actions as $action) {
        $this->handleAction($hook_name, $action);
      }
    }
  }

  /**
   * Registers script
   * 
   * @param  object $hook_name JS Hook instance
   * @return object            Current object
   */
  protected function handleAction($hook_name, $action)
  {
    if($hook = JS::getInstance()->getHook($hook_name)) {

      if ($action == 'register') {
        
        // Get dependencies
        $deps = $this->config->get('deps') ?: [];

        // Enqueue dependencies
        if ($deps) {
          foreach ($deps as $dep_handle) {

            // Publish event
            $this->getEventEmitter()->publish("cms.config.scripts.$dep_handle", [
              'action' => 'enqueue_as_dependency',
              'hooks'  => [
                $hook_name
              ]
            ]);
          }
        }

        // Register script
        $hook->register(
          $this->config->get('handle'),
          $this->config->get('src'),
          $deps,
          $this->config->get('version') ?: null,
          $this->config->get('in_footer') ?: null
        );
      }

      else {

        $hook->$action($this->config->get('handle'));
      }
    }

    return $this;
  }

  /**
   * Consumes event
   * 
   * @param mixed $message Event message
   */
  public function consumeEvent($message)
  {
    if (is_array($message)) {
      
      $action = isset($message['action']) ? $message['action'] : null;

      // Enqueue this script as a dependency
      if ($action && $action == 'enqueue_as_dependency') {
        
        $hooks = isset($message['hooks']) ? $message['hooks'] : [];

        if ($hooks) {
          foreach ($hooks as $hook_name) {

            $this->handleAction($hook_name, 'register');
            $this->handleAction($hook_name, 'enqueue');
          }
        }
      }
    }

    return $this;
  }
} 