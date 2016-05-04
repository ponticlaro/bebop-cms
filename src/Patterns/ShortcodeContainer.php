<?php

namespace Ponticlaro\Bebop\Cms\Patterns;

use Ponticlaro\Bebop\Common\Collection;
use Ponticlaro\Bebop\Cms\Shortcode;
use Ponticlaro\Bebop\Cms\Helpers\ShortcodeConfig;

abstract class ShortcodeContainerAbstract {

  /**
   * Contains all shortcode configs
   * 
   * @var object Ponticlaro\Bebop\Common\Collection
   */
  protected static $shortcode_configs;

  /**
   * Instantiates this class
   * 
   */
  public function __construct()
  {
    $this->shortcode_configs = new Collection();
  }

  /**
   * Sets a single shortcode configuration
   * 
   * @param string   $id            Shortcode config ID
   * @param callable $callable      Shortcode callable
   * @param array    $default_attrs Shortcode default attributes
   */
  final public function setShortcode($id, callable $callable, array $default_attrs = [])
  {
    $this->shortcode_configs->set($id, new ShortcodeConfig($id, $callable, $default_attrs));

    return $this;
  }

  /**
   * Returns all defined shortcode configs
   * 
   * @return array List of shortcode configs
   */
  final public function getShortcode($id)
  {
    return is_string($id) ? $this->shortcode_configs->get($id) : null;
  }

  /**
   * Returns all defined shortcode configs
   * 
   * @return array List of shortcode configs
   */
  final public function getAllShortcodes()
  {
    return $this->shortcode_configs->getAll();
  }

  /**
   * Registers a single shortcode config
   * 
   * @param  string $id Shortcode config ID
   * @return object     This class instance
   */
  final public function register($id)
  {
    $config = $this->shortcode_configs->get($id);

    if ($config) {

      $shortcode = new Shortcode($config->getId(), $config->getCallable());
      $shortcode->setDefaultAttrs($config->getDefaultAttrs());
    }

    return $this;
  }

  /**
   * Registers all shortcode configs
   * 
   * @return object This class instance
   */
  final public function registerAll()
  {
    foreach ($this->shortcode_configs->getAll() as $config) {
      
      $shortcode = new Shortcode($config->getId(), $config->getCallable());
      $shortcode->setDefaultAttrs($config->getDefaultAttrs());
    }

    return $this;
  }
}