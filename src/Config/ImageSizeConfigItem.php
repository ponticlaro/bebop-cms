<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\Cms\Patterns\ConfigItem;

class ImageSizeConfigItem extends ConfigItem {

  /**
   * {@inheritDoc}
   */
  public static function getIdKey()
  {
    return 'name';
  }

  /**
   * Checks if configuration is valid
   * 
   * @return boolean True if valid, false otherwise
   */
  public function isValid()
  {
    $valid  = true;
    $config = $this->getAll();

    // 'name' must be a string
    if (!isset($config['name']) || !$config['name'] || !is_string($config['name']))
      $valid = false;

    // Either 'width' or 'height' must exist
    if (!isset($config['width']) && !isset($config['height']))
      $valid = false;

    // If 'width' exists, it must be a string or an integer
    if (isset($config['width']) && !is_integer($config['width']) && !is_string($config['width']))
      $valid = false;

    // If 'height' exists, it must be a string or an integer
    if (isset($config['height']) && !is_integer($config['height']) && !is_string($config['height']))
      $valid = false;

    // If 'crop' exists, it must be a boolean or an array
    if (isset($config['crop'])) {

      if (!is_bool($config['crop']) && !is_array($config['crop']))
        $valid = false;
    }

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
      $this->get('name'),
      $this->get('width'),
      $this->get('height'),
      $this->get('crop') ?: false // Defaults to false
    );

    return $this;
  }
} 