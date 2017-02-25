<?php

namespace Ponticlaro\Bebop\Cms\Presets\Shortcodes;

use Ponticlaro\Bebop\Mvc\View;

class Vimeo extends \Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainer {

  /**
   * Shortcode ID
   * 
   * @var string
   */
  protected $id = 'vimeo';

  /**
   * Shortcode default attributes
   * 
   * @var string
   */
  protected $default_attrs = [
    'id'      => null,
    'caption' => null,
    'width'   => '960',
    'height'  => '540'
  ];

  /**
   * Instantiates this class
   * 
   */
  public function __construct()
  {
    // Set template path
    $this->setTemplatePath(dirname(__FILE__) .'/templates/video.php');
  }

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
    if ($attrs->get('id')) {
      
      // Set video 
      $attrs->set('source', 'vimeo');

      // Making sure 'url' doesn't exist
      $attrs->remove('url');

      (new View())->render($this->getTemplatePath(), $attrs->getAll());
    }
  }
}