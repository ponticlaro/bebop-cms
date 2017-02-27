<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\Cms\AdminPage;
use \Ponticlaro\Bebop\Cms\Patterns\ConfigItem;

class AdminPageConfigItem extends ConfigItem {
  
  /**
   * {@inheritDoc}
   */
  public static function getIdKey()
  {
    return 'title';
  }

  /**
   * Checks if configuration is valid
   * 
   * @return boolean True if valid, false otherwise
   */
  public function isValid()
  {
    $valid = true;
    $title = $this->get('title');

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
    new AdminPage($this->getAll());

    return $this;
  }
} 