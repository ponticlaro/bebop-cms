<?php

namespace Ponticlaro\Bebop\Cms\Patterns;

use Ponticlaro\Bebop\Cms\Shortcode;

abstract class ShortcodeContainerAbstract {

  /**
   * Shortcode ID
   * 
   * @var string
   */
  protected $id;

  /**
   * Shortcode default attributes
   * 
   * @var string
   */
  protected $default_attrs = [];

  /**
   * Instantiates this class
   * 
   */
  public function __construct()
  {
    // Nothing to be done for now
  }

  /**
   * Registers shortcode
   * 
   * @return object This class instance
   */
  final public function register()
  {
    $shortcode = new Shortcode($this->id, [$this, 'render']);
    $shortcode->setDefaultAttrs($this->default_attrs);

    return $this;
  }

  /**
   * Renders shortcode 
   * 
   * @param  object $attrs   Attributes collection  
   * @param  string $content Shortcode content
   * @param  string $tag     Shortcode tag
   * @return void
   */
  abstract public function render($attrs, $content = null, $tag);
}