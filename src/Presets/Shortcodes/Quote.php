<?php

namespace Ponticlaro\Bebop\Cms\Presets\Shortcodes;

class Quote extends \Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainer {

  /**
   * Shortcode ID
   * 
   * @var string
   */
  protected $id = 'quote';

  /**
   * Shortcode default attributes
   * 
   * @var string
   */
  protected $default_attrs = [];

  /**
   * Renders shortcode 
   * 
   * @param  object $attrs   Attributes collection  
   * @param  string $content Shortcode content
   * @param  string $tag     Shortcode tag
   * @return void
   */
  public function render($attrs, $content = null, $tag)
  {

  }
}