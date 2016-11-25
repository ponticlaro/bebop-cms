<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\Cms\AdminPage;
use \Ponticlaro\Bebop\Cms\Patterns\ConfigItem;

class AdminPageConfigItem extends ConfigItem {
  
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

    // Presets may have incomplete configurations
    if ($preset)
      return $valid;

    // 'title' must be a string
    if (!$title || !is_string($title))
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
    new AdminPage($this->config->getAll());

    return $this;
  }
} 