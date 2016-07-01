<?php

namespace Ponticlaro\Bebop\Cms\Presets\Shortcodes;

use Ponticlaro\Bebop\Mvc\View;

class Video extends \Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainerAbstract {

  /**
   * Shortcode ID
   * 
   * @var string
   */
  protected $id = 'video';

  /**
   * Shortcode default attributes
   * 
   * @var string
   */
  protected $default_attrs = [
    'id'      => null,
    'url'     => null,
    'caption' => null,
    'width'   => '960',
    'height'  => '540'
  ];

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
        
      $video = get_post($attrs->get('id'));

      if ($video && $video_source = get_post_meta($video->ID, 'video_source', true)) {

        // Reset 'id'
        $attrs->set('id', null);

        // Collect 'source'
        $attrs->set('source', $video_source);

        // Get source-specific data
        switch ($video_source) {

          case 'upload':
            
            $video_upload_id = get_post_meta($video->ID, 'video_upload', true);

            if ($video_upload_id)
              $attrs->set('url', wp_get_attachment_url($video_upload_id));
            break;

          case 'vimeo':
            
            $attrs->set('id', get_post_meta($video->ID, 'video_vimeo_id', true));
            break;

          case 'youtube':
            
            $attrs->set('id', get_post_meta($video->ID, 'video_youtube_id', true));
            break;
        }

        // Collect caption
        if (!$attrs->get('caption') && $video_caption = get_post_meta($video->ID, 'video_caption', true))
          $attrs->set('caption', $video_caption);
      }
    }

    elseif ($attrs->get('url')) {
      
      $attrs->set('source', 'remote');
    }

    if ($attrs->get('source') && ($attrs->get('id') || $attrs->get('url'))) {

      View::overrideViewsDir(dirname(__FILE__) .'/templates');
      (new View())->render('video', $attrs->getAll());
      View::restoreViewsDir();
    }
  }
}