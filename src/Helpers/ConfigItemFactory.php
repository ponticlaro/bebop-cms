<?php

namespace Ponticlaro\Bebop\Cms\Helpers;

use Ponticlaro\Bebop\Cms\Patterns\Factory;

class ConfigItemFactory extends Factory {

  /**
   * Holds the class that manufacturables must extend
   *
   */
  protected static $manufacturable_class = 'Ponticlaro\Bebop\Cms\Patterns\ConfigItem';

  /**
   * List of manufacturable classes
   * 
   * @var array
   */
  protected static $manufacturable = array(
    'admin_pages' => 'Ponticlaro\Bebop\Cms\Config\AdminPageConfigItem',
    'image_sizes' => 'Ponticlaro\Bebop\Cms\Config\ImageSizeConfigItem',
    'metaboxes'   => 'Ponticlaro\Bebop\Cms\Config\MetaboxConfigItem',
    'paths'       => 'Ponticlaro\Bebop\Cms\Config\PathConfigItem',
    'scripts'     => 'Ponticlaro\Bebop\Cms\Config\ScriptConfigItem',
    'shortcodes'  => 'Ponticlaro\Bebop\Cms\Config\ShortcodeConfigItem',
    'styles'      => 'Ponticlaro\Bebop\Cms\Config\StyleConfigItem',
    'taxonomies'  => 'Ponticlaro\Bebop\Cms\Config\TaxonomyConfigItem',
    'types'       => 'Ponticlaro\Bebop\Cms\Config\TypeConfigItem',
    'urls'        => 'Ponticlaro\Bebop\Cms\Config\UrlConfigItem',
  );
}