<?php

namespace Ponticlaro\Bebop\Cms\AdminPage;

use Ponticlaro\Bebop\Common\Collection;
use Ponticlaro\Bebop\Common\Utils;
use Ponticlaro\Bebop\UI\Helpers\ModuleFactory;

class Tab {

  /**
   * Tab ID
   * 
   * @var string
   */
  protected $id;

  /**
   * Tab title
   * 
   * @var string
   */
  protected $title;

  /**
   * Tab function
   * 
   * @var string
   */
  protected $function;

  /**
   * Options names
   * 
   * @var Ponticlaro\Bebop\Common\Collection
   */
  protected $options;

  /**
   * List of sections to be output on the callback function
   * 
   * @var Ponticlaro\Bebop\Common\Collection
   */
  protected $sections;

  /**
   * Options names
   * 
   * @var Ponticlaro\Bebop\Common\Collection
   */
  protected $data;

  /**
   * Instantiates a new tab
   *
   * @param string $id
   * @param string $title    
   * @param mixed  $args Callable or array of args
   */
  public function __construct($id, $title, $args = null)
  {
    $this->options  = new Collection();
    $this->sections = new Collection();
    $this->data     = new Collection();

    $this->setId($id);
    $this->setTitle($title);

    if (is_callable($args)) {

      $this->setFunction($args);
    }

    elseif (is_array($args)) {

      $this->applyArgs($args);
    }

    // Register Settings
    add_action('admin_init', array($this, '__handleSettingsRegistration'));
  }

  /**
   * Applies a list of configuration values
   * 
   * @param  array $args Configuration array
   * @return void
   */
  public function applyArgs(array $args = [])
  {
    // Handle 'id'
    if (isset($args['id']) && $args['id']) {
      $this->setId($args['id']);
      unset($args['id']);
    }

    // Handle 'title'
    if (isset($args['title']) && $args['title']) {
      $this->setTitle($args['title']);
      unset($args['title']);
    }

    // Handle 'fn'
    if (isset($args['fn']) && $args['fn']) {
      $this->setFunction($args['fn']);
      unset($args['fn']);
    }

    // Handle 'options'
    if (isset($args['options']) && is_array($args['options'])) {
      $this->setOptions($args['options']);
      unset($args['options']);
    }

    // Handle 'sections'
    if (isset($args['sections']) && is_array($args['sections'])) {
      foreach ($args['sections'] as $section) {
        
        if (isset($section['ui']) && is_string($section['ui']) && $section['ui']) {
          
          $ui_id = $section['ui'];
          unset($section['ui']);

          $this->addSection($ui_id, $section);
        }
      }
      
      unset($args['sections']);
    }
  }

  /**
   * Sets tab ID
   * 
   * @param string $id
   */
  public function setId($id)
  {
    if (!is_string($id))
      throw new \Exception("AdminPage Tab id must be a string");
    
    $this->id = Utils::slugify($id, array('separator' => '-'));

    return $this;
  }

  /**
   * Returns tab ID
   * 
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Sets tab title
   * 
   * @param string $title
   */
  public function setTitle($title)
  {
    if (!is_string($title))
      throw new \Exception("AdminPage Tab title must be a string");
      
    $this->title = $title;

    return $this;
  }

  /**
   * Returns tab title
   * 
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * Sets tab function
   * 
   * @param callable $function
   */
  public function setFunction($function)
  {
    if (!is_callable($function))
      throw new \Exception('AdminPage Tab function must be callable');
        
    $this->function = $function;

    return $this;
  }

  public function getFunction()
  {
    return $this->function;
  }

  /**
   * Sets options name
   * 
   * @param array $options
   */
  public function setOptions(array $options)
  {
    foreach ($options as $option) {   
      $this->addOption($option);
    }

    return $this;
  }

  /**
   * Adds a single option name
   * 
   * @param string $id
   */
  public function addOption($option)
  {
    if (!is_string($option))
      throw new \Exception("AdminPage Tab option must be a string");

    $this->options->push($option);

    return $this;
  }

  /**
   * Returns all options
   * 
   * @return array
   */
  public function getOptions()
  {
    return $this->options->getAll();
  }

  /**
   * Adds a single content section
   * 
   * @param string $id   ID of a module in the UI ModuleFactory class
   * @param array  $args Arguments for the section
   */
  public function addSection($id, array $args)
  {
    if (!is_string($id))
      throw new \Exception('AdminPage Tab section $id must be a string.');

    if (!ModuleFactory::canManufacture($id))
      throw new \Exception("AdminPage Tab don't know how to handle a '$id' UI section.");
          
    $section = ModuleFactory::create($id, $args);
    $this->sections->push($section);

    return $this;
  }

  /**
   * Returns all sections
   * 
   * @return array List containing all sections
   */
  public function getAllSections()
  {
    return $this->sections->getAll();
  }

  /**
   * Calls to undefined functions
   * 
   * @param  string $name Function name
   * @param  array  $args Function arguments
   * @return object       This class instance
   */
  public function __call($name, array $args = [])
  {   
    // Quick method to add sections
    if (!ModuleFactory::canManufacture($name))
      throw new \Exception("AdminPage Tab don't know how to create undefined '$name' UI section.");

    $args    = isset($args[0]) && is_array($args[0]) ? $args[0] : [];
    $section = ModuleFactory::create($name, $args);

    $this->sections->push($section);

    return $this;
  }

  /**
   * Collects all field names within sections
   * 
   * @param  object $data       Data collection
   * @param  object $admin_page This metabox instance
   * @return void    
   */
  public function __collectSectionsFieldNames($data, $admin_page_tab)
  {
    foreach($this->sections->getAll() as $section) {
      $section->renderMainTemplate();
    }
  }

  /**
   * Registers grouped settings
   * 
   * @return void
   */
  public function __handleSettingsRegistration()
  {
    // Get sections & callable
    $sections = $this->getAllSections();
    $function = $this->getFunction();
    $names    = [];

    // Fetch control elements name attribute from function
    if ($function)
      $names += Utils::getControlNamesFromCallable($function, array($this->data, $this));

    // Fetch control elements name attribute from sections
    if ($sections)
      $names += Utils::getControlNamesFromCallable([$this, '__collectSectionsFieldNames'], array($this->data, $this));
    
    if ($names) {

      $this->options->pushList($names);

      foreach ($names as $name) {  
        register_setting($this->getId(), $name);
      }
    }

    // Reset sections so that we do not have duplicates
    $this->sections->clear()->pushList($sections);
  }

  /**
   * Sets data to be passed to the function
   * 
   * @return void
   */
  private function __setData()
  {
    $options = $this->options->getAll();

    if ($options) {
      foreach ($options as $option) {  
        $this->data->set($option, get_option($option));
      }
    }
  }

  /**
   * Renders tab content
   * 
   * @return void
   */
  public function render()
  {
    $this->__setData();

    // Execute callable
    if ($function = $this->getFunction())
        call_user_func_array($function, array($this->data, $this));

    // Render sections
    foreach($this->getAllSections() as $section) {
      $section->render($this->data->getAll());
    }
  }
}