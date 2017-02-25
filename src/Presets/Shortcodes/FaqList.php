<?php

namespace Ponticlaro\Bebop\Cms\Presets\Shortcodes;

class FaqList extends \Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainerAbstract {

  /**
   * Shortcode ID
   * 
   * @var string
   */
  protected $id = 'faq_list';

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