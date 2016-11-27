<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\Common\PathManager;
use \Ponticlaro\Bebop\Cms\Patterns\ConfigItem;

class PathConfigItem extends ConfigItem {

  /**
   * Checks if configuration is valid
   * 
   * @return boolean True if valid, false otherwise
   */
  public function isValid()
  {
    $valid = true;
    $id    = $this->config->get('id');
    $path  = $this->config->get('path');

    // 'id' must be a string
    if (!$id || !is_string($id))
      $valid = false;

    // 'path' must be a string
    if (!$path || !is_string($path))
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
    PathManager::getInstance()->set($this->getId(), $this->config->get('path'));
  }
} 