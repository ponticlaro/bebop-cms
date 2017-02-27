<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\ScriptsLoader\Css;
use \Ponticlaro\Bebop\Common\EventEmitter;

class StyleConfigItem extends ScriptConfigItem {

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
        
        // Get dependencies
        $deps = $this->get('deps') ?: [];

        // Enqueue dependencies
        if ($deps) {
          foreach ($deps as $dep_handle) {

            // Publish event
            $this->getEventEmitter()->publish("cms.config.styles.$dep_handle", [
              'action' => 'enqueue_as_dependency',
              'hooks'  => [
                $hook_name
              ]
            ]);
          }
        }

        // Register script
        $hook->register(
          $this->get('handle'),
          $this->get('src'),
          $deps,
          $this->get('version') ?: null,
          $this->get('in_footer') ?: null
        );
      }

      else {

        $hook->$action($this->get('handle'));
      }
    }

    return $this;
  }
} 