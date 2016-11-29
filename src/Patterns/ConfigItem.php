<?php

namespace Ponticlaro\Bebop\Cms\Patterns;

use Ponticlaro\Bebop\Common\Collection;
use Ponticlaro\Bebop\Common\Utils;

abstract class ConfigItem implements ConfigItemInterface {

  /**
   * Configuration proprety of the ID
   */
  const IDENTIFIER = 'id';

  /**
   * Contains configuration data
   * 
   * @var object \Ponticlaro\Bebop\Common\Collection
   */
  protected $config;

  /**
   * Instantiates configuration item
   * 
   * @param array $config Configuration array
   */
  public function __construct(array $config = [])
  {
    $this->config = new Collection($config);
  }

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
    $id = $this->config->get('_id');

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
    $id = $this->config->get(static::IDENTIFIER);

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
    $id = $this->config->get('preset');

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
    return $this->config->get('requires') ?: [];
  }

  /**
   * Sets the value for a single configuration key
   * 
   * @param string $key   Configuration key
   * @param mixed  $value Configuration value
   */
  public function set($key, $value)
  {
    $this->config->set($key, $value);

    return $this;
  }

  /**
   * Returns the value for the target configuration key
   * 
   * @param  string $key   Configuration key
   * @return mixed  $value Configuration value
   */
  public function get($key)
  {
    return $this->config->get($key);
  }

  /**
   * Removes the target configuration key
   * 
   * @param  string $key Configuration key
   * @return object      Current class object
   */
  public function remove($key)
  {
    return $this->config->remove($key);
  }

  /**
   * Returns full configuration array
   * 
   * @return array Full configuration array
   */
  public function getAll()
  {
    return $this->config->getAll();
  }

  /**
   * Merges current configuration item with another one
   * 
   * @param  ConfigItemInterface $config_item Configuration object we're going to merge with
   * @return object                           Current class object
   */
  public function merge(ConfigItem $config_item)
  {
    $current_config = $this->config->getAll();
    $merging_config = $config_item->getAll();
    $new_config     = array_replace_recursive($current_config, $merging_config);

    $this->config->clear()->set($new_config);

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