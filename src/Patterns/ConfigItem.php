<?php

namespace Ponticlaro\Bebop\Cms\Patterns;

use Ponticlaro\Bebop\Common\Collection;
use Ponticlaro\Bebop\Common\Utils;

abstract class ConfigItem extends Collection implements ConfigItemInterface {

  /**
   * Configuration proprety of the ID
   */
  const IDENTIFIER = 'id';

  /**
   * Checks if configuration is valid
   * 
   * @return boolean True if valid, false otherwise
   */
  abstract public function isValid();

  /**
   * Returns unique configuration item ID
   * 
   * @return string Configuration item ID
   */
  public function getUniqueId()
  {
    // Check if config have an '_id' property
    $id = $this->get('_id');

    // If we have no '_id', return preset ID
    return $id ? static::__getNormalizedId($id) : $this->getId();
  }

  /**
   * Returns configuration item ID
   * 
   * @return string Configuration item preset ID
   */
  public function getId()
  {
    // Check if config have a value on its identifier property
    $id = $this->get(static::IDENTIFIER);

    // Return if there is no ID
    if (!$id)
      return null;

    // Making sure types and taxonomies get their IDs from the singular name
    if (is_array($id))
        $id = reset($id);

    // Return ID
    return $id ? static::__getNormalizedId($id) : null;
  }

  /**
   * Returns configuration item preset ID, if any
   * 
   * @return string Configuration item preset ID
   */
  public function getPresetId()
  {
    $id = $this->get('preset');

    // Return preset ID
    return $id ? static::__getNormalizedId($id) : null;
  }

  /**
   * Returns configuration item requirements aray
   * 
   * @return array Configuration item requirements array
   */
  public function getRequirements()
  {
    return $this->get('requires') ?: [];
  }

  /**
   * Merges current configuration item with another one
   * 
   * @param  ConfigItemInterface $config_item Configuration object we're going to merge with
   * @return object                           Current class object
   */
  public function merge(ConfigItem $config_item)
  {
    $current_config = $this->getAll();
    $merging_config = $config_item->getAll();
    $new_config     = array_replace_recursive($current_config, $merging_config);

    $this->clear()->set($new_config);

    return $this;
  }

  /**
   * Normalizes ID string
   * 
   * @param  string $source_string ID string to clean
   * @return string                Clean string
   */
  private static function __getNormalizedId($raw_id)
  {
    // Slugify
    $id = Utils::slugify($raw_id);

    // Replace dots with underscores
    $id = str_replace('.', '_', $id);

    // Return ID
    return $id;
  }

  /**
   * Builds configuration item
   * 
   * @return object 
   */
  abstract public function build();
} 