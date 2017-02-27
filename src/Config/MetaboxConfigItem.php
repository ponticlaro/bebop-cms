<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\Cms\Metabox;
use \Ponticlaro\Bebop\Cms\Patterns\ConfigItem;

class MetaboxConfigItem extends ConfigItem {

  /**
   * Configuration proprety of the ID
   */
  const IDENTIFIER = 'title';

  /**
   * Checks if configuration is valid
   * 
   * @return boolean True if valid, false otherwise
   */
  public function isValid()
  {
    $valid = true;
    $title = $this->get('title');
    $types = $this->get('types');

    // 'title' must be a string or array
    if (!$title || !is_string($title))
      $valid = false;

    // 'types' must be a string or array
    if (!$types || (!is_string($types) && !is_array($types)))
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
    new Metabox($this->getAll());

    return $this;
  }
} 