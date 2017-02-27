<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\Common\UrlManager;
use \Ponticlaro\Bebop\Cms\Patterns\ConfigItem;

class UrlConfigItem extends ConfigItem {
  
  /**
   * Checks if configuration is valid
   * 
   * @return boolean True if valid, false otherwise
   */
  public function isValid()
  {
    $valid = true;
    $id    = $this->get('id');
    $url   = $this->get('url');

    // 'id' must be a string
    if (!$id || !is_string($id))
      $valid = false;

    // 'url' must be a string
    if (!$url || !is_string($url))
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
    UrlManager::getInstance()->set($this->getId(), $this->get('url'));
  }
} 