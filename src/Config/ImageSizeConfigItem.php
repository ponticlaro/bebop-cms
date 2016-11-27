<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\Cms\Patterns\ConfigItem;

class ImageSizeConfigItem extends ConfigItem {

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
    $config = $this->config->getAll();

    if (!isset($config['name']) || !$config['name'])
      $valid = false;

    if (!isset($config['width']) && !isset($config['height']))
      $valid = false;

    if (!is_bool($config['crop']) && !is_array($config['crop']))
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
    add_image_size(
      $this->config->get('name'),
      $this->config->get('width'),
      $this->config->get('height'),
      $this->config->get('crop') ?: false // Defaults to false
    );

    return $this;
  }
} 