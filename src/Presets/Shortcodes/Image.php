<?php

namespace Ponticlaro\Bebop\Cms\Presets\Shortcodes;

class Image extends \Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainerAbstract {

  /**
   * Shortcode ID
   * 
   * @var string
   */
  protected $id = 'image';

  /**
   * Shortcode default attributes
   * 
   * @var string
   */
  protected $default_attrs = [
    'id'      => null,
    'size'    => 'large',
    'url'     => null,
    'caption' => null,
    'alt'     => null
  ];

  /**
   * Instantiates this class
   * 
   */
  public function __construct()
  {
    // Set template path
    $this->setTemplatePath(dirname(__FILE__) .'/templates/image.php');
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
    if ($attrs->get('id') && $attrs->get('size')) {
      
      $attrs->set('url', null);
      $image_data = wp_get_attachment_image_src($attrs->get('id'), $attrs->get('size'));

      if ($image_data)
        $attrs->set('url', reset($image_data));
    }

    // Render if we have an URL
    if ($attrs->get('url')) {

      extract(array_intersect_key(
        $attrs->getAll(),
        array_flip([
          'url', 
          'alt', 
          'caption',
        ])
      ));

      include $this->getTemplatePath();
    }  
  }
}