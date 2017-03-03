<?php

namespace Ponticlaro\Bebop\Cms\Config;

use Ponticlaro\Bebop\ScriptsLoader\Js;
use Ponticlaro\Bebop\Common\EventEmitter;
use Ponticlaro\Bebop\Common\EventMessage;
use Ponticlaro\Bebop\Common\Patterns\EventConsumerTrait;
use Ponticlaro\Bebop\Common\Patterns\EventConsumerInterface;
use Ponticlaro\Bebop\Common\Patterns\EventEmitterInterface;
use Ponticlaro\Bebop\Common\Patterns\EventMessageInterface;
use Ponticlaro\Bebop\Cms\Patterns\ConfigItem;

class ScriptConfigItem extends ConfigItem implements EventConsumerInterface {

  /**
   * Inherit default EventConsumerTrait attributes and methods
   */
  use EventConsumerTrait;

  /**
   * {@inheritDoc}
   */
  public static function getIdKey()
  {
    return 'handle';
  }

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
   * Checks if configuration is valid
   * 
   * @return boolean True if valid, false otherwise
   */
  public function isValid()
  {
    $valid  = true;
    $handle = $this->get('handle');
    $src    = $this->get('src');
    $hooks  = $this->get('hooks');

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
    $hooks = $this->get('hooks') ?: [];

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
        
        // Get handle
        $handle = $this->get('handle');

        // Get dependencies
        $deps = $this->get('deps') ?: [];

        // Enqueue dependencies
        if ($deps) {
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
        
        // Register script
        $hook->register(
          $handle,
          $this->get('src'),
          $deps,
          $this->get('version') ?: null,
          $this->get('in_footer') ?: null
        );

        // Handle asynchronous loading
        if ($this->get('async'))
          $hook->getFile($handle)->setAsync(true);

        // Handle defered loading
        if ($this->get('defer'))
          $hook->getFile($handle)->setDefer(true);
      }

      else {

        $hook->$action($this->get('handle'));
      }
    }

    return $this;
  }

  /**
   * Consumes event
   * 
   * @param mixed $message Event message
   */
  public function consumeEvent(EventMessageInterface $message)
  {
    if ('enqueue_as_dependency' == $message->getAction())
      $this->enqueueAsDependency($message);

    return $this;
  }

  /**
   * Enqueues this script as a dependency of another script
   * 
   * @param object $message Event message
   */
  protected function enqueueAsDependency(EventMessageInterface $message)
  {
    $data  = $message->getData();
    $hooks = isset($data['hooks']) ? $data['hooks'] : [];

    if ($hooks) {
      foreach ($hooks as $hook_name) {

        $this->handleAction($hook_name, 'register');
        $this->handleAction($hook_name, 'enqueue');
      }
    }
  }
} 