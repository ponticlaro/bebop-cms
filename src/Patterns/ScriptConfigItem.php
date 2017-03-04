<?php

namespace Ponticlaro\Bebop\Cms\Patterns;

use Ponticlaro\Bebop\Common\EventEmitter;
use Ponticlaro\Bebop\Common\EventMessage;
use Ponticlaro\Bebop\Common\Patterns\EventConsumerTrait;
use Ponticlaro\Bebop\Common\Patterns\EventConsumerInterface;
use Ponticlaro\Bebop\Common\Patterns\EventEmitterInterface;
use Ponticlaro\Bebop\Common\Patterns\EventMessageInterface;
use Ponticlaro\Bebop\Cms\Patterns\ConfigItem;
use Ponticlaro\Bebop\ScriptsLoader\Js;
use Ponticlaro\Bebop\ScriptsLoader\Patterns\ScriptsHook;

abstract class ScriptConfigItem extends ConfigItem implements EventConsumerInterface {

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
   * Registers, deregisters, enqueues and dequeues script
   * 
   * @param  object $hook_name JS Hook instance
   * @return object            Current object
   */
  abstract protected function handleAction($hook_name, $action);

  /**
   * Makes sure script dependencies are enqueued
   * 
   * @param  string $hook_name JS Hook name
   * @return void
   */
  abstract protected function ensureDependenciesAreEnqueued($hook_name);

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