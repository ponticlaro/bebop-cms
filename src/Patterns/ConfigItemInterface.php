<?php

namespace Ponticlaro\Bebop\Cms\Patterns;

interface ConfigItemInterface {

  /**
   * Instantiates configuration item
   * 
   * @param array $config Configuration array
   */
  public function __construct(array $config = []);

  /**
   * Returns the key name containing the ID
   * 
   * @return string Key name
   */
  public static function getIdKey();

  /**
   * Returns unique configuration item ID
   * 
   * @return string Configuration item ID
   */
  public function getUniqueId();

  /**
   * Returns configuration item ID
   * 
   * @return string Configuration item preset ID
   */
  public function getId();

  /**
   * Returns configuration item preset ID, if any
   * 
   * @return string Configuration item preset ID
   */
  public function getPresetId();

  /**
   * Returns configuration item requirements aray
   * 
   * @return array Configuration item requirements array
   */
  public function getRequirements();

  /**
   * Sets the value for a single configuration key
   * 
   * @param string $key   Configuration key
   * @param mixed  $value Configuration value
   */
  public function set($key, $value);

  /**
   * Returns the value for the target configuration key
   * 
   * @param  string $key   Configuration key
   * @return mixed  $value Configuration value
   */
  public function get($key);

  /**
   * Removes the target configuration key
   * 
   * @param  string $key Configuration key
   * @return object      Current class object
   */
  public function remove($key);

  /**
   * Returns full configuration array
   * 
   * @return array Full configuration array
   */
  public function getAll();

  /**
   * Merges current configuration item with another one
   * 
   * @param  ConfigItemInterface $config_item Configuration object we're going to merge with
   * @return object                           Current class object
   */
  public function merge(ConfigItemInterface $config_item);
  
  /**
   * Checks if configuration is valid
   * 
   * @return boolean True if valid, false otherwise
   */
  public function isValid();

  /**
   * Builds configuration item
   * 
   * @return object 
   */
  public function build();
} 