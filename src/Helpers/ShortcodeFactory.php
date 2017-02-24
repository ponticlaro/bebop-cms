<?php

namespace Ponticlaro\Bebop\Cms\Helpers;

class ShortcodeFactory {

  /**
   * Holds the class that manufacturables must extend

   */
  const SHORTCODE_CONTAINER_CLASS = 'Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainerAbstract';

  /**
   * List of manufacturable classes
   * 
   * @var array
   */
  protected static $manufacturable = array(
    'facebook_post'   => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\FacebookPost',
    'faq_list'        => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\FaqList',
    'form'            => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Form',
    'formstack'       => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Formstack',
    'gallery'         => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Gallery',
    'google_calendar' => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\GoogleCalendar',
    'google_map'      => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\GoogleMap',
    'image'           => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image',
    'pardot_form'     => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\PardotForm',
    'quote'           => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Quote',
    'soundcloud'      => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Soundcloud',
    'tweet'           => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Tweet',
    'video'           => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Video',
    'vimeo'           => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Vimeo',
    'youtube'         => 'Ponticlaro\Bebop\Cms\Presets\Shortcodes\Youtube',
  );

  /**
   * Making sure class cannot get instantiated
   * 
   */
  protected function __construct() {}

  /**
   * Making sure class cannot get instantiated
   * 
   */
  protected function __clone() {}

  /**
   * Adds a new manufacturable class
   * 
   * @param  string $id    Object type ID
   * @param  string $class Full namespace for a class
   * @return void
   */
  public static function set($id, $class)
  {
    if (!is_string($id))
      throw new \Exception("ShortcodeFactory manufacturable id must be a string");

    if (!is_string($class))
      throw new \Exception("ShortcodeFactory manufacturable class must be a string");

    self::$manufacturable[strtolower($id)] = $class;
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
      throw new \Exception("ShortcodeFactory manufacturable id must be a string");

    $id = strtolower($id);

    if (isset(self::$manufacturable[$id])) 
      unset(self::$manufacturable[$id]);
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
      throw new \Exception("ShortcodeFactory manufacturable id must be a string");
      
    $id = strtolower($id);

    if (!isset(self::$manufacturable[$id]))
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
    if (is_object($instance) && is_a($instance, self::SHORTCODE_CONTAINER_CLASS)) {

      $class = get_class($instance);
      $id    = array_search($class, self::$manufacturable);

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
      throw new \Exception("ShortcodeFactory manufacturable id must be a string");

    $id = strtolower($id);

    // Check if target is in the allowed list
    if (!array_key_exists($id, self::$manufacturable))
      return null;

    $class_name = self::$manufacturable[$id];

    return call_user_func(array(__CLASS__, "__createInstance"), $class_name, $args);
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
    if (!is_subclass_of($class_name, self::SHORTCODE_CONTAINER_CLASS))
      return null;

    // Return an instance of the target class
    $reflection = new \ReflectionClass($class_name);

    return $reflection->newInstanceArgs([$args]);
  }
}