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
    $valid  = true;
    $preset = $this->config->get('preset');
    $id     = $this->config->get('id');
    $url    = $this->config->get('url');

    // Presets may have incomplete configurations
    if ($preset)
      return $valid;

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
    UrlManager::getInstance()->set($this->getId(), $this->config->get('url'));
  }
} 