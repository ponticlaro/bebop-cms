<?php

namespace Ponticlaro\Bebop\Cms\Patterns;

abstract class Factory implements FactoryInterface {

  /**
   * Holds the class that manufacturables must extend
   *
   * @var string
   */
  protected static $manufacturable_class = 'stdClass';

  /**
   * List of manufacturable classes
   * 
   * @var array
   */
  protected static $manufacturable = [];

  /**
   * Making sure class cannot get instantiated
   */
  protected function __construct() {}

  /**
   * Making sure class cannot get instantiated
   */
  protected function __clone() {}

  /**
   * Adds a new manufacturable class
   * 
   * @param  string $id         Object type ID
   * @param  string $class_name Full namespace for a class
   * @return void
   */
  public static function set($id, $class_name)
  {
    if (!is_string($id))
      throw new \Exception("Factory manufacturable id must be a string");

    if (!is_string($class_name))
      throw new \Exception("Factory manufacturable class must be a string");

    if (!is_subclass_of($class_name, static::getManufacturableParentClass()))
      throw new \Exception("Factory manufacturable class must extend '". static::getManufacturableParentClass() ."'");

    static::$manufacturable[strtolower($id)] = $class_name;
  }

  /**
   * Removes a new manufacturable class
   * 
   * @param  string $id  Object type ID
   * @return void
   */
  public static function remove($id)
  {   
    if (!is_string($id))
      throw new \Exception("Factory manufacturable id must be a string");

    $id = strtolower($id);

    if (isset(static::$manufacturable[$id])) 
      unset(static::$manufacturable[$id]);
  }

  /**
   * Checks if there is a manufacturable with target key
   * 
   * @param  string  $id Target key
   * @return boolean     True if key exists, false otherwise
   */
  public static function canManufacture($id)
  {
    if (!is_string($id))
      throw new \Exception("Factory manufacturable id must be a string");
      
    $id = strtolower($id);

    if (!isset(static::$manufacturable[$id]))
      return false;

    return true;
  }

  /**
   * Returns the id to manufacture another instance of the passed object, if any
   * 
   * @param  object $instance Arg instance
   * @return string           Arg ID 
   */
  public static function getInstanceId($instance)
  {
    if (is_object($instance) && is_a($instance, static::getManufacturableParentClass())) {

      $class = get_class($instance);
      $id    = array_search($class, static::$manufacturable);

      return $id ?: null;
    }

    return null;
  }

  /**
   * Creates instance of target class
   * 
   * @param  string $type Class ID
   * @param  array  $args Class arguments
   * @return object       Class instance
   */
  public static function create($id, array $args = array())
  {
    if (!is_string($id))
      throw new \Exception("Factory manufacturable id must be a string");

    $id = strtolower($id);

    // Check if target is in the allowed list
    if (!array_key_exists($id, static::$manufacturable))
      return null;

    $reflection = new \ReflectionClass(static::$manufacturable[$id]);

    return $reflection->newInstanceArgs([$args]);
  }

  /**
   * Sets manufacturable parent class
   * 
   * @param  string $class_name Class that should be extended by manufacturables
   * @return void
   */
  public static function setManufacturableParentClass($class_name)
  {
    if (!class_exists($class_name))
      throw new \Exception("Factory manufacturable class must be defined");
    
    static::$manufacturable_class = $class_name;
  }

  /**
   * Returns manufacturable parent class
   * 
   * @return string
   */
  public static function getManufacturableParentClass()
  {
    return static::$manufacturable_class;
  }
}