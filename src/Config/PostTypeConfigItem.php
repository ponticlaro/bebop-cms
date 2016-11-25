<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\Cms\PostType;
use \Ponticlaro\Bebop\Cms\Patterns\ConfigItem;

class PostTypeConfigItem extends ConfigItem {
  
  /**
   * Configuration proprety of the ID
   */
  const IDENTIFIER = 'name';

  /**
   * Checks if configuration is valid
   * 
   * @return boolean True if valid, false otherwise
   */
  public function isValid()
  {
    $valid  = true;
    $preset = $this->config->get('preset');
    $name   = $this->config->get('name');

    // Presets may have incomplete configurations
    if ($preset)
      return $valid;

    // 'name' must be a string or an array
    if (!$name || (!is_string($name) && !is_array($name)))
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
    var_dump($this->config->getAll());

    $type = new PostType($this->config->get('name'));
    $type->applyRawArgs($this->config->getAll());

    return $this;
  }
} 