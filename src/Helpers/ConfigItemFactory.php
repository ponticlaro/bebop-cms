<?php

namespace Ponticlaro\Bebop\Cms\Helpers;

class ConfigItemFactory {

  /**
   * Holds the class that manufacturables must extend
   */
  const CONFIG_CONTAINER_CLASS = 'Ponticlaro\Bebop\Cms\Patterns\ConfigItem';

  /**
   * List of manufacturable classes
   * 
   * @var array
   */
  protected static $manufacturable = array(
    'admin_pages' => 'Ponticlaro\Bebop\Cms\Config\AdminPageConfigItem',
    'image_sizes' => 'Ponticlaro\Bebop\Cms\Config\ImageSizeConfigItem',
    'metaboxes'   => 'Ponticlaro\Bebop\Cms\Config\MetaboxConfigItem',
    'paths'       => 'Ponticlaro\Bebop\Cms\Config\PathConfigItem',
    'scripts'     => 'Ponticlaro\Bebop\Cms\Config\ScriptConfigItem',
    'shortcodes'  => 'Ponticlaro\Bebop\Cms\Config\ShortcodeConfigItem',
    'styles'      => 'Ponticlaro\Bebop\Cms\Config\StyleConfigItem',
    'taxonomies'  => 'Ponticlaro\Bebop\Cms\Config\TaxonomyConfigItem',
    'types'       => 'Ponticlaro\Bebop\Cms\Config\TypeConfigItem',
    'urls'        => 'Ponticlaro\Bebop\Cms\Config\UrlConfigItem',
  );

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
   * @param string $id    Object type ID
   * @param string $class Full namespace for a class
   */
  public static function set($id, $class)
  {
     static::$manufacturable[strtolower($id)] = $class;
  }

  /**
   * Removes a new manufacturable class
   * 
   * @param string $id  Object type ID
   */
  public static function remove($id)
  {   
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
    $id = strtolower($id);

    return is_string($id) && isset(static::$manufacturable[$id]) ? true : false;
  }

  /**
   * Returns the id to manufacture another instance of the passed object, if any
   * 
   * @param  object $instance Arg instance
   * @return string           Arg ID 
   */
  public static function getInstanceId($instance)
  {
    if (is_object($instance) && is_a($instance, static::CONFIG_CONTAINER_CLASS)) {

      $class = get_class($instance);
      $id    = array_search($class, static::$manufacturable);

      return $id ?: null;
    }

     return null;
  }

  /**
   * Creates instance of target class
   * 
   * @param  string] $type Class ID
   * @param  array   $args Class arguments
   * @return object        Class instance
   */
  public static function create($id, array $args = array())
  {
    $id = strtolower($id);

    // Check if target is in the allowed list
    if (array_key_exists($id, static::$manufacturable)) {

      $class_name = static::$manufacturable[$id];

      return call_user_func(array(__CLASS__, "__createInstance"), $class_name, $args);
    }

    // Return null if target object is not manufacturable
    return null;
  }

  /**
   * Creates and instance of the target class
   * 
   * @param  string $class_name Target class
   * @param  array  $args       Arguments to pass to target class
   * @return mixed              Class instance or false
   */
  private static function __createInstance($class_name, array $args = array())
  {
    // Get an instance of the target class
    $obj = call_user_func_array(
      array(
        new \ReflectionClass($class_name), 
        'newInstance'
      ),
      [$args]
    );
        
    // Return object
    return is_a($obj, static::CONFIG_CONTAINER_CLASS) ? $obj : null;
  }
}