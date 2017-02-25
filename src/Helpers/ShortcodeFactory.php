<?php

namespace Ponticlaro\Bebop\Cms\Helpers;

use Ponticlaro\Bebop\Cms\Patterns\Factory;

class ShortcodeFactory extends Factory {

  /**
   * Holds the class that manufacturables must extend
   *
   */
  protected static $manufacturable_class = 'Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainer';

  /**
   * List of manufacturable classes
   * 
   * @var array
   */
  protected static $manufacturable = array(
    'facebook_post'   => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\FacebookPost',
    'faq_list'        => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\FaqList',
    'form'            => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Form',
    'formstack'       => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Formstack',
    'gallery'         => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Gallery',
    'google_calendar' => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\GoogleCalendar',
    'google_map'      => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\GoogleMap',
    'image'           => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image',
    'pardot_form'     => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\PardotForm',
    'quote'           => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Quote',
    'soundcloud'      => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Soundcloud',
    'tweet'           => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Tweet',
    'video'           => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Video',
    'vimeo'           => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Vimeo',
    'youtube'         => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Youtube',
  );
}