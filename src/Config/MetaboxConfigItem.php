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
    $valid  = true;
    $preset = $this->config->get('preset');
    $title  = $this->config->get('title');
    $types  = $this->config->get('types');

    // Presets may have incomplete configurations
    if ($preset)
      return $valid;

    // 'title' must be a string or array
    if (!$title || (!is_string($title) && !is_array($title)))
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
    new Metabox($this->config->getAll());

    return $this;
  }
} 