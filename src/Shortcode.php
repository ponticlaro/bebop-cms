<?php 

namespace Ponticlaro\Bebop\Cms;

use Ponticlaro\Bebop\Common\Collection;
use Ponticlaro\Bebop\Common\Patterns\TrackableObjectInterface;

class Shortcode implements TrackableObjectInterface {

  /**
    * Shortcode tag
    * 
    * @var string
    */
  protected $tag;

  /**
    * Shortcode function
    * 
    * @var callable
    */
  protected $function;

  /**
    * Shortcode attributes
    * 
    * @var Ponticlaro\Bebop\Common\Collection
    */
  protected $attributes;

  /**
    * Shortcode default attributes
    * 
    * @var Ponticlaro\Bebop\Common\Collection
    */
  protected $default_attributes;

  /**
   * {@inheritdoc}
   */
  public function getObjectID()
  {
    return $this->tag;
  }

  /**
   * {@inheritdoc}
   */
  public function getObjectType()
  {
    return 'shortcode';
  }

  /**
    * Instantiates a new shortcode
    * 
    * @param string   $tag      Shortcode tag
    * @param callable $function Shortcode function
    */
  public function __construct($tag, $function)
  {
    if (!is_string($tag))
      throw new \Exception("Shortcode tag must be a string");

    if (!is_callable($function))
      throw new \Exception("Shortcode function must be callable");
    
    $this->tag                = $tag;
    $this->function           = $function;
    $this->attributes         = new Collection;
    $this->default_attributes = new Collection;

    add_shortcode($tag, array($this, '__registerShortcode'));
  }

  /**
    * Registers Shortcode and its function
    * 
    * @param  array  $attrs   Attributes from user input
    * @param  string $content Content on shortcodes with opening and closing tags
    * @param  string $tag     Shortcode tag
    * @return void
    */
  public function __registerShortcode($attrs, $content = null, $tag)
  {
    // Clear attributes
    $this->attributes->clear();

    // Set default attributes
    $this->attributes->setList($this->default_attributes->getAll());

    // Remove quotes from attributes
    if ($attrs && is_array($attrs)) {

      foreach ($attrs as $key => $attr) {
        $attrs[$key] = static::__cleanAttrValue($attr);
      }

      $this->attributes->setList($attrs);
    }

    ob_start();

    // Execute shortcode function
    call_user_func_array($this->function, array($this->attributes, $content, $tag));
    
    $html = ob_get_contents();

    ob_end_clean();

    return $html;
  }

  /**
    * Sets a single default attribute
    * 
    * @param string                          $key   Attribute key
    * @param mixed                           $value Attribute value
    * @return Ponticlaro\Bebop\Cms\Shortcode        This class instance
    */
  public function setDefaultAttr($key, $value)
  {
    if (!is_string($key))
       throw new \Exception("Shortcode default attribute key must be a string");
    
    $this->default_attributes->set($key, $value);

    return $this;
  }

  /**
    * Sets several default atrributes
    * 
    * @param  array                          $attrs List of key/value pairs
    * @return Ponticlaro\Bebop\Cms\Shortcode        This class instance
    */
  public function setDefaultAttrs(array $attrs)
  {
    foreach ($attrs as $key => $value) {
      $this->setDefaultAttr($key, $value);
    }

    return $this;
  }

  /**
    * Cleans shortcode attribute value
    * 
    * @param  string $value Raw value
    * @return string        Clean value
    */
  protected static function __cleanAttrValue($value)
  {
    return str_replace(array('\'', '"', '&#8217;', '&#8221;', '&quot;'), '', $value);
  }
}