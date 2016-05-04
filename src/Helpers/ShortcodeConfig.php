<?php

namespace Ponticlaro\Bebop\Cms\Helpers;

class ShortcodeConfig {

  /**
   * Shortcode ID
   * 
   * @var string
   */
  protected $id;

  /**
   * Shortcode callable
   * 
   * @var string
   */
  protected $callable;

  /**
   * Shortcode default attributes
   * 
   * @var string
   */
  protected $default_attrs = [];

  /**
   * Instantiates this class
   * 
   * @param string $id            Shortcode ID
   * @param string $callable      Shortcode callable
   * @param array  $default_attrs Shortcode default attributes
   */
  public function __construct($id, $callable, array $default_attrs = [])
  {
    $this->shortcodes = new Collection();
  }

  /**
   * Returns shortcode ID
   * 
   * @return string 
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Returns shortcode callable
   * 
   * @return string 
   */
  public function getCallable()
  {
    return $this->callable;
  }

  /**
   * Returns shortcode default attributes
   * 
   * @return array 
   */
  public function getDefaultAttrs()
  {
    return $this->default_attrs;
  }
}