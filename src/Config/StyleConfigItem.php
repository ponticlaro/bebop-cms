<?php

namespace Ponticlaro\Bebop\Cms\Config;

use Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem as ScriptConfigItemAbstract;
use Ponticlaro\Bebop\Common\EventEmitter;
use Ponticlaro\Bebop\Common\EventMessage;
use Ponticlaro\Bebop\ScriptsLoader\Css;
use Ponticlaro\Bebop\ScriptsLoader\Patterns\ScriptsHook;

class StyleConfigItem extends ScriptConfigItemAbstract {

  /**
   * Instantiates configuration item
   * 
   * @param array $config Configuration array
   */
  public function __construct(array $config = [])
  {
    parent::__construct($config);

    // Define events channel
    $channel = 'cms.config.styles.'. $this->get('handle');

    // Subscribe for events targeting this script
    $event_emitter = EventEmitter::getInstance()
                                 ->subscribe($channel, [$this, 'consumeEvent']);

    // Set event emitter
    $this->setEventEmitter($event_emitter);
  }

  /**
   * Registers script
   * 
   * @param  object $hook_name JS Hook instance
   * @return object            Current object
   */
  protected function handleAction($hook_name, $action)
  {
    if($hook = CSS::getInstance()->getHook($hook_name)) {

      if ($action == 'register') {

        // Enqueue dependencies
        $this->ensureDependenciesAreEnqueued($hook_name);

        // Register script
        $hook->register(
          $this->get('handle'),
          $this->get('src'),
          $this->get('deps') ?: [],
          $this->get('version') ?: null,
          $this->get('media') ?: null
        );
      
        return $this;
      }
      
      $hook->$action($this->get('handle'));
    }

    return $this;
  }

  /**
   * Makes sure script dependencies are enqueued
   * 
   * @param  string $hook_name CSS Hook name
   * @return void
   */
  protected function ensureDependenciesAreEnqueued($hook_name)
  {
    // Get dependencies
    $deps = $this->get('deps') ?: [];

    foreach ($deps as $dep_handle) {

      // Set event channel
      $channel = "cms.config.styles.$dep_handle";

      // Create message
      $message = new EventMessage('enqueue_as_dependency', [
        'hooks' => [$hook_name]
      ]);

      // Publish event
      $this->getEventEmitter()->publish($channel, $message);
    }
  }
} 