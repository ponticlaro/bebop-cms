<?php

namespace Ponticlaro\Bebop\Cms\Helpers;

use Ponticlaro\Bebop\Cms\Patterns\Factory;

class ShortcodeFactory extends Factory {

  /**
   * Holds the class that manufacturables must extend
   *
   */
  protected static $manufacturable_class = 'Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainerAbstract';

  /**
   * List of manufacturable classes
   * 
   * @var array
   */
  protected static $manufacturable = array(
    'image'   => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image',
    'video'   => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Video',
    'vimeo'   => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Vimeo',
    'youtube' => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Youtube',
  );
}