<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\Cms\Taxonomy;
use \Ponticlaro\Bebop\Cms\Patterns\ConfigItem;

class TaxonomyConfigItem extends ConfigItem {
  
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
    $name  = $this->get('name');
    $types = $this->get('types');

    // 'name' must be a string or an array
    if (!$name || (!is_string($name) && !is_array($name)))
      $valid = false;

    // 'type' must be a string or an array
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
    $type = new Taxonomy($this->get('name'), $this->get('types'));
    $type->applyRawArgs($this->getAll());

    return $this;
  }
} 