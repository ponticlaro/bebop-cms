<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory;
use \Ponticlaro\Bebop\Cms\Patterns\ConfigItem;

class ShortcodeConfigItem extends ConfigItem {
  
  /**
   * Checks if configuration is valid
   * 
   * @return boolean True if valid, false otherwise
   */
  public function isValid()
  {
    $valid = true;
    $id    = $this->config->get('id');

    // 'id' must be a string
    if (!$id || !is_string($id))
      $valid = false;

    return $valid;
  }
  
  /**
   * Builds configuration item
   * 
   * @return object Current object
   */
  public function build()
  {
    $id    = $this->getId();
    $class = $this->config->get('class');

    // Replace default class
    if ($class && class_exists($class))
      ShortcodeFactory::set($id, $class);

    // Check if shortcode is available
    if (ShortcodeFactory::canManufacture($id)) {
        
      // Get shortcode
      $shortcode = ShortcodeFactory::create($id);

      // Register shortcode
      if ($shortcode)
        $shortcode->register();
    }

    return $this;
  }
} 