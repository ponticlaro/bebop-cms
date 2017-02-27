<?php

namespace Ponticlaro\Bebop\Cms\Config;

use \Ponticlaro\Bebop\Cms\Taxonomy;
use \Ponticlaro\Bebop\Cms\Patterns\ConfigItem;

class TaxonomyConfigItem extends ConfigItem {
  
  /**
   * {@inheritDoc}
   */
  public static function getIdKey()
  {
    return 'name';
  }

  /**
   * Returns configuration item ID
   * 
   * @return string Configuration item preset ID
   */
  public function getId()
  {
    // Check if config have a value on its identifier property
    $id = $this->get(static::getIdKey());

    // Return if there is no ID
    if (!$id)
      return null;

    // Making sure we get the ID from the singular name
    if (is_array($id))
        $id = reset($id);

    // Return ID
    return $id ? static::__getNormalizedId($id) : null;
  }

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

    // 'types' must be a string or an array
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
    $type = new Taxonomy($this->get('name'), $this->get('types'));
    $type->applyRawArgs($this->getAll());

    return $this;
  }
} 