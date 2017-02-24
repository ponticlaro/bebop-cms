<?php

namespace Ponticlaro\Bebop\Cms\Patterns;

interface FactoryInterface {

  /**
   * Adds a new manufacturable class
   * 
   * @param  string $id         Object type ID
   * @param  string $class_name Full namespace for a class
   * @return void
   */
  public static function set($id, $class_name);

  /**
   * Removes a new manufacturable class
   * 
   * @param  string $id  Object type ID
   * @return void
   */
  public static function remove($id);

  /**
   * Checks if there is a manufacturable with target key
   * 
   * @param  string  $id Target key
   * @return boolean     True if key exists, false otherwise
   */
  public static function canManufacture($id);

  /**
   * Returns the id to manufacture another instance of the passed object, if any
   * 
   * @param  object $instance Arg instance
   * @return string           Arg ID 
   */
  public static function getInstanceId($instance);

  /**
   * Creates instance of target class
   * 
   * @param  string $type Class ID
   * @param  array  $args Class arguments
   * @return object       Class instance
   */
  public static function create($id, array $args = []);

  /**
   * Sets manufacturable parent class
   * 
   * @param  string $class_name Class that should be extended by manufacturables
   * @return void
   */
  public static function setManufacturableParentClass($class_name);

  /**
   * Returns manufacturable parent class
   * 
   * @return string
   */
  public static function getManufacturableParentClass();
}