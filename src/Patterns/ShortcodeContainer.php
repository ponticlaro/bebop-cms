<?php

namespace Ponticlaro\Bebop\Cms\Patterns;

use Ponticlaro\Bebop\Cms\Shortcode;

abstract class ShortcodeContainer {

  /**
   * Shortcode ID
   * 
   * @var string
   */
  protected $id;

  /**
   * Absolute path to shortcode template
   * 
   * @var string
   */
  protected $template_path;

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
   * Sets the absolute path to template
   *
   * @return object This class instance
   */
  final public function setTemplatePath($path)
  {
    if (!is_string($path))
      throw new \Exception("ShortcodeContainer template path must be a string");

    if (!is_readable($path))
      throw new \Exception("ShortcodeContainer template path must be readable");
    
    $this->template_path = $path;

    return $this;
  }

  /**
   * Returns absolute path to template
   * 
   * @return string Path to template
   */
  final public function getTemplatePath()
  {
    return $this->template_path;
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