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
    $id    = $this->get('id');
    $class = $this->get('class');

    // 'id' must be a string
    if (!$id || !is_string($id))
      $valid = false;

    // 'class' must be a string
    if ($class && (!class_exists($class) || !is_subclass_of($class, 'Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainerAbstract')))
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
    $class = $this->get('class');

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