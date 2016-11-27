<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\Cms\PostType;
use \Ponticlaro\Bebop\Cms\Patterns\ConfigItem;

class TypeConfigItem extends ConfigItem {
  
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
    $valid = true;
    $name  = $this->config->get('name');

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
    $type = new PostType($this->config->get('name'));
    $type->applyRawArgs($this->config->getAll());

    return $this;
  }
} 