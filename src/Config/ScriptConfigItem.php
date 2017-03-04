<?php

namespace Ponticlaro\Bebop\Cms\Config;

use Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem as ScriptConfigItemAbstract;
use Ponticlaro\Bebop\Common\EventEmitter;
use Ponticlaro\Bebop\Common\EventMessage;
use Ponticlaro\Bebop\ScriptsLoader\Js;
use Ponticlaro\Bebop\ScriptsLoader\Patterns\ScriptsHook;

class ScriptConfigItem extends ScriptConfigItemAbstract {

  /**
   * Instantiates configuration item
   * 
   * @param array $config Configuration array
   */
  public function __construct(array $config = [])
  {
    parent::__construct($config);

    // Get handle
    $handle = $this->get('handle');

    if ($handle && is_string($handle)) {

      // Define events channel
      $channel = "cms.config.scripts.$handle";

      // Subscribe for events targeting this script
      $event_emitter = EventEmitter::getInstance()->subscribe($channel, [$this, 'consumeEvent']);

      // Set event emitter
      $this->setEventEmitter($event_emitter);
    }
  }

  /**
   * Registers, deregisters, enqueues and dequeues script
   * 
   * @param  object $hook_name JS Hook instance
   * @return object            Current object
   */
  protected function handleAction($hook_name, $action)
  {
    if($hook = JS::getInstance()->getHook($hook_name)) {

      if ($action == 'register') {

        // Enqueue dependencies
        $this->ensureDependenciesAreEnqueued($hook_name);

        // Register script
        $hook->register(
          $this->get('handle'),
          $this->get('src'),
          $this->get('deps') ?: [],
          $this->get('version') ?: null,
          $this->get('in_footer') ?: null
        );

        // Handle asynchronous loading
        if ($this->get('async'))
          $this->setAsync($hook);

        // Handle defered loading
        if ($this->get('defer'))
          $this->setDefer($hook);
      
        return $this;
      }

      $handle = $this->get('handle');

      call_user_func_array([$hook, $action], [$handle]);
    }

    return $this;
  }

  /**
   * Makes sure script dependencies are enqueued
   * 
   * @param  string $hook_name JS Hook name
   * @return void
   */
  protected function ensureDependenciesAreEnqueued($hook_name)
  {
    // Get dependencies
    $deps = $this->get('deps') ?: [];

    foreach ($deps as $dep_handle) {

      // Set event channel
      $channel = "cms.config.scripts.$dep_handle";

      // Create message
      $message = new EventMessage('enqueue_as_dependency', [
        'hooks' => [$hook_name]
      ]);

      // Publish event
      $this->getEventEmitter()->publish($channel, $message);
    }
  }

  /**
   * Sets script to use async loading
   * 
   * @param  object $hook JS Hook instance
   * @return void
   */
  protected function setAsync(ScriptsHook $hook)
  {
    $hook->getFile($this->get('handle'))->setAsync(true);
  }

  /**
   * Sets script to use deferred loading
   * 
   * @param  object $hook JS Hook instance
   * @return void
   */
  protected function setDefer(ScriptsHook $hook)
  {
    $hook->getFile($this->get('handle'))->setDefer(true);
  }
} 