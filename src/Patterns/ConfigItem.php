<?php

namespace Ponticlaro\Bebop\Cms\Patterns;

use Ponticlaro\Bebop\Common\Collection;
use Ponticlaro\Bebop\Common\Utils;

abstract class ConfigItem extends Collection implements ConfigItemInterface {

  /**
   * {@inheritDoc}
   */
  abstract public static function getIdKey();

  /**
   * {@inheritDoc}
   */
  public function getUniqueId()
  {
    // Check if config have an '_id' property
    if ($id = $this->get('_id'))
      return static::__getNormalizedId($id);

    // If we have no '_id', return preset ID
    return $this->getId();
  }

  /**
   * {@inheritDoc}
   */
  public function getId()
  {
    // Check if config have a value on its identifier property
    $id = $this->get(static::getIdKey());

    // Return ID if we have one
    if ($id)
      return static::__getNormalizedId($id);

    // Return null
    return null;
  }

  /**
   * {@inheritDoc}
   */
  public function getPresetId()
  {
    // Return preset id if we have one
    if ($id = $this->get('preset'))
      return static::__getNormalizedId($id);

    // Return null
    return null;
  }

  /**
   * {@inheritDoc}
   */
  public function getRequirements()
  {
    return $this->get('requires') ?: [];
  }

  /**
   * {@inheritDoc}
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
   * {@inheritDoc}
   */
  abstract public function isValid();

  /**
   * {@inheritDoc}
   */
  abstract public function build();

  /**
   * Normalizes ID string
   * 
   * @param  string $source_string ID string to clean
   * @return string                Clean string
   */
  protected static function __getNormalizedId($raw_id)
  {
    // Slugify
    $id = Utils::slugify($raw_id);

    // Replace dots with underscores
    $id = str_replace('.', '_', $id);

    // Return ID
    return $id;
  }
} 