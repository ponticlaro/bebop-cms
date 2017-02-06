<?php

namespace Ponticlaro\Bebop\Cms;

use Ponticlaro\Bebop\Common\Collection;
use Ponticlaro\Bebop\Common\Patterns\TrackableObjectInterface;
use Ponticlaro\Bebop\Common\Utils;

class Taxonomy implements TrackableObjectInterface {

  /**
   * Configuration 
   * 
   * @var Ponticlaro\Bebop\Common\Collection
   */
  protected $config;

  /**
   * Labels
   * 
   * @var Ponticlaro\Bebop\Common\Collection
   */
  protected $labels;

  /**
   * Capabilities
   * 
   * @var Ponticlaro\Bebop\Common\Collection
   */
  protected $capabilities;

  /**
   * Rewrite configuration
   * 
   * @var Ponticlaro\Bebop\Common\Collection
   */
  protected $rewrite_config;

  /**
   * Post types
   * 
   * @var Ponticlaro\Bebop\Common\Collection
   */
  protected $post_types;

  /**
   * Map with alias for existing methods
   * 
   * @var array
   */
  protected static $methods_alias = array(
    'setPublic'          => 'makePublic',
    'setShowInNavMenus'  => 'showInNavMenus',
    'setShowTagcloud'    => 'showTagcloud',
    'setShowAdminColumn' => 'showAdminColumn',
    'setMetaBoxCb'       => 'setMetaboxCallback'
  );

  /**
   * {@inheritdoc}
   */
  public function getObjectID()
  {
    return $this->getId();
  }

  /**
   * {@inheritdoc}
   */
  public function getObjectType()
  {
    return 'taxonomy';
  }

  /**
   * Instantiates a new taxonomy
   * 
   * @param string $name       String or array with singular name first and plural name in second
   * @param mixed  $post_types Single (or array containing a mix of) post-type name or PostType class instance
   */
  public function __construct($name, $types = null)
  {
    $this->config = new Collection(array(
      'hierarchical'      => true,
      'show_ui'           => true,
      'show_admin_column' => true,
      'query_var'         => true
    ));

     // Instantiate labels object
    $this->labels = new Collection();

    // Instantiate capabilities object
    $this->capabilities = new Collection();

    // Instantiate rewrite configuration object
    $this->rewrite_config = new Collection();

     // Instantiate types object
    $this->post_types = new Collection();

    // Set taxonomy name
    call_user_func_array(array($this, '__setName'), is_array($name) ? $name : array($name));

    // Set taxonomy id
    $this->setId(is_array($name) ? $name[0] : $name);

    // Set Post types
    if (!is_null($types)) {
      
      if (is_array($types)) {
          
        $this->setPostTypes($types);
      }

      else {

        $this->addPostType($types);
      }
    }

    // Set default labels based on singular and plural names
    $this->__setDefaultLabels();

    // Hook into init action to register taxonomy
    add_action("init", array($this, '__register'));
  }

  /**
   * Sets taxonomy ID
   * 
   * @param  string                         $id
   * @return \Ponticlaro\Bebop\Cms\Taxonomy     Taxonomy instance
   */
  public function setId($id)
  {
    if (is_string($id))
      $this->config->set('id', Utils::slugify($id));

    return $this;
  }

  /**
   * Returns taxonomy ID
   * 
   * @return string $id
   */
  public function getId()
  {
    return $this->config->get('id');
  }

  /**
   * Sets taxonomy labels 
   * 
   * @param  array                          $labels Associative array with post type labels
   * @return \Ponticlaro\Bebop\Cms\Taxonomy         Taxonomy instance
   */
  public function setLabels(array $labels = array())
  {
    foreach ($labels as $key => $value) {
      $this->setLabel($key, $value);
    }

    return $this;
  }

  /**
   * Sets a single taxonomy label
   * 
   * @param  string                         $key   Label key
   * @param  string                         $value Label value
   * @return \Ponticlaro\Bebop\Cms\Taxonomy        Taxonomy instance
   */
  public function setLabel($key, $value)
  {
    if (!is_string($key) || !is_string($value))
      throw new \Exception('Taxonomy label $key and $value arguments must be strings.');

    $this->labels->set($key, $value);

    return $this;
  }

  /**
   * Returns all labels
   * 
   * @return array
   */
  public function getLabels()
  {
    return $this->labels->getAll();
  }

  /**
   * Returns a single label by its key
   * 
   * @param  string $key Label key
   * @return string      Label value
   */
  public function getLabel($key)
  {
    return $this->labels->get($key);
  }

  /**
   * Replaces capabilities
   * 
   * @param  array                          $capabilities Associative array with capabilities
   * @return \Ponticlaro\Bebop\Cms\Taxonomy               Taxonomy instance
   */
  public function replaceCapabilities(array $capabilities = array())
  {
    $this->capabilities->clear();
    $this->setCapabilities($capabilities);

    return $this;
  }

  /**
   * Sets capabilities
   * 
   * @param  array                          $capabilities Associative array with capabilities
   * @return \Ponticlaro\Bebop\Cms\Taxonomy               Taxonomy instance
   */
  public function setCapabilities(array $capabilities = array())
  {
    foreach ($capabilities as $key => $name) {
      $this->addCapability($key, $name);
    }

    return $this;
  }

  /**
   * Adds a single capability
   * 
   * @param string $key  Capability key
   * @param string $name Capability name
   */
  public function addCapability($key, $name)
  {
    if (!is_string($key))
      throw new \Exception('Taxonomy capability key must be a string.');

    if (!is_string($name))
      throw new \Exception('Taxonomy capability name must be a string.');

    $this->capabilities->set($key, $name);

    return $this;
  }

  /**
   * Removes capabilities
   * 
   * @param  array                          $capabilities Indexed array with capabilities
   * @return \Ponticlaro\Bebop\Cms\Taxonomy               Taxonomy instance
   */
  public function removeCapabilities(array $capabilities = array())
  {
    foreach ($capabilities as $key) {
      $this->removeCapability($key);
    }

    return $this;
  }

  /**
   * Removes a single capability
   * 
   * @param  string                         $key Capability key
   * @return \Ponticlaro\Bebop\Cms\Taxonomy      Taxonomy instance
   */
  public function removeCapability($key)
  {
    if (!is_string($key))
      throw new \Exception('Taxonomy capability key must be a string.');

    $this->capabilities->remove($key);

    return $this;
  }

  /**
   * Returns all capabilities
   * 
   * @return array
   */
  public function getCapabilities()
  {
    return $this->capabilities->getAll();
  }

  /**
   * Sets post types
   * 
   * @param  array                          $post_types Indexed array with post types
   * @return \Ponticlaro\Bebop\Cms\Taxonomy             Taxonomy instance
   */
  public function setPostTypes(array $post_types = array())
  {
    $this->post_types->clear();
    $this->addPostTypes($post_types);

    return $this;
  }

  /**
   * Adds post types on top of existing ones
   * 
   * @param  array                          $post_types Indexed array with post types
   * @return \Ponticlaro\Bebop\Cms\Taxonomy             Taxonomy instance
   */
  public function addPostTypes(array $post_types = array())
  {
    foreach ($post_types as $post_type) {
      $this->addPostType($post_type);
    }

    return $this;   
  }

  /**
   * Adds single post_type
   * 
   * @param  string                         $post_type
   * @return \Ponticlaro\Bebop\Cms\Taxonomy            Taxonomy instance
   */
  public function addPostType($post_type)
  {
    if (is_a($post_type, 'Ponticlaro\Bebop\Cms\PostType'))
      $post_type = $post_type->getId();

    if (!is_string($post_type))
      throw new \Exception('Taxonomy post type must be either a string or a \Ponticlaro\Bebop\PostType instance.');          

    $post_type = Utils::slugify($post_type);

    if (!$this->post_types->hasValue($post_type))
      $this->post_types->push($post_type);

    return $this;
  }

  /**
   * Removes post types
   * 
   * @param  array                          $taxonomies Indexed array with post types
   * @return \Ponticlaro\Bebop\Cms\Taxonomy             Taxonomy instance
   */
  public function removePostTypes(array $post_types = array())
  {
    foreach ($post_types as $post_type) {
      $this->removePostType($post_type);
    }

    return $this;   
  }

  /**
   * Removes single post types
   * 
   * @param  string                         $post_type
   * @return \Ponticlaro\Bebop\Cms\Taxonomy            Taxonomy instance
   */
  public function removePostType($post_type)
  {
    if (is_a($post_type, 'Ponticlaro\Bebop\Cms\PostType'))
      $post_type = $post_type->getId();

    if (!is_string($post_type))
      throw new \Exception('Taxonomy post type must be either a string or a \Ponticlaro\Bebop\Cms\PostType instance.');          

    $this->post_types->pop($post_type);

    return $this;
  }

  /**
   * Returns all taxonomies
   * 
   * @return array
   */
  public function getPostTypes()
  {
    return $this->post_types->getAll();
  }

 /**
   * Sets the value for 'rewrite'
   * 
   * @param  array                          $args
   * @return \Ponticlaro\Bebop\Cms\Taxonomy       Taxonomy instance
   */
  public function setRewrite(array $args = array())
  {
    foreach ($args as $key => $value) {
      switch ($key) {

        case 'slug':
          $this->setRewriteSlug($value);
          break;

        case 'with_front':
          $this->setRewriteWithFront($value);
          break;

        case 'hierarchical':
          $this->setRewriteHierarchical($value);
          break;

        case 'ep_mask':
          $this->setRewriteEpmask($value);
          break;
      }
    }

    return $this;
  }

  /**
   * Sets the value for 'rewrite[slug]'
   * 
   * @param  string                         $slug
   * @return \Ponticlaro\Bebop\Cms\Taxonomy       Taxonomy instance
   */
  public function setRewriteSlug($slug)
  {
    if (!is_string($slug))
      throw new \Exception('Taxonomy rewrite slug must be a string.');

    $this->rewrite_config->set('slug', $slug);

    return $this;
  }

  /**
   * Sets the value for 'rewrite[with_front]'
   * 
   * @param  boolean                        $enabled
   * @return \Ponticlaro\Bebop\Cms\Taxonomy          Taxonomy instance
   */
  public function setRewriteWithFront($enabled)
  {
    if (!is_bool($enabled))
      throw new \Exception('Taxonomy rewrite with_front must be a boolean.');

    $this->rewrite_config->set('with_front', $enabled);

    return $this;
  }

  /**
   * Sets the value for 'rewrite[hierarchical]'
   * 
   * @param  boolean                       $enabled
   * @return \Ponticlaro\Bebop\Cms\Taxonomy         Taxonomy instance
   */
  public function setRewriteHierarchical($enabled)
  {
    if (!is_bool($enabled))
      throw new \Exception('Taxonomy rewrite hierarchical must be a boolean.');

    $this->rewrite_config->set('hierarchical', $enabled);

    return $this;
  }

  /**
   * Sets the value for 'rewrite[ep_mask]'
   * 
   * @param  string                         $epmask
   * @return \Ponticlaro\Bebop\Cms\Taxonomy         Taxonomy instance
   */
  public function setRewriteEpmask($epmask)
  {
    if (!is_string($epmask))
      throw new \Exception('Taxonomy rewrite ep_mask must be a string.');

    $this->rewrite_config->set('ep_mask', $epmask);

    return $this;
  }

  /**
   * Returns rewrite configuration
   * 
   * @return array
   */
  public function getRewrite()
  {
    return $this->rewrite_config->getAll();
  }

  /**
   * Sets taxonomy 'public' value
   * 
   * @param  boolean                        $enabled True to enable, false otherwise
   * @return \Ponticlaro\Bebop\Cms\Taxonomy          Taxonomy instance
   */
  public function makePublic($enabled = true)
  {
    $this->config->set('public', $enabled);

    return $this;
  }

  /**
   * Checks if taxonomy is public
   * 
   * @return boolean 
   */
  public function isPublic()
  {
    return $this->config->get('public');
  }

 /**
   * Sets taxonomy 'hierarchical' value
   * 
   * @param  boolean                        $enabled True to enable, false otherwise
   * @return \Ponticlaro\Bebop\Cms\Taxonomy          Taxonomy instance
   */
  public function setHierarchical($enabled = true)
  {
    $this->config->set('hierarchical', $enabled);

    return $this;
  }

  /**
   * Checks if post type is hierarchical
   * 
   * @return boolean 
   */
  public function isHierarchical()
  {
    return $this->config->get('hierarchical');
  }

  /**
   * Sets query_var
   * 
   * @param  string                         $query_var
   * @return \Ponticlaro\Bebop\Cms\Taxonomy            Taxonomy instance
   */
  public function setQueryVar($query_var)
  {
    if (!is_bool($query_var) && !is_string($query_var))
      throw new \Exception('Taxonomy query_var must be a string or false.');

    $this->config->set('query_var', $query_var);

    return $this;
  }

  /**
   * Returns query_var
   * 
   * @return string
   */
  public function getQueryVar()
  {
    return $this->config->get('query_var');
  }

  /**
   * Sets the value for 'show_ui'
   * 
   * @param  bool                           $enabled
   * @return \Ponticlaro\Bebop\Cms\Taxonomy          Taxonomy instance
   */
  public function showUi($enabled)
  {
    if (!is_bool($enabled))
      throw new \Exception('Taxonomy show_ui must be a boolean.');

    $this->config->set('show_ui', $enabled);

    return $this;
  }

  /**
   * Sets the value for 'show_in_nav_menus'
   * 
   * @param  bool                           $enabled
   * @return \Ponticlaro\Bebop\Cms\Taxonomy          Taxonomy instance
   */
  public function showInNavMenus($enabled)
  {
    if (!is_bool($enabled))
      throw new \Exception('Taxonomy show_in_nav_menus must be a boolean.');

    $this->config->set('show_in_nav_menus', $enabled);

    return $this;
  }

  /**
   * Sets the value for 'show_tagcloud'
   * 
   * @param  bool                           $enabled
   * @return \Ponticlaro\Bebop\Cms\Taxonomy          Taxonomy instance
   */
  public function showTagcloud($enabled)
  {
    if (!is_bool($enabled))
      throw new \Exception('Taxonomy show_tagcloud must be a boolean.');

    $this->config->set('show_tagcloud', $enabled);

    return $this;
  }

  /**
   * Sets the value for 'show_admin_column'
   * 
   * @param  bool                           $enabled
   * @return \Ponticlaro\Bebop\Cms\Taxonomy          Taxonomy instance
   */
  public function showAdminColumn($enabled)
  {
    if (!is_bool($enabled))
      throw new \Exception('Taxonomy show_admin_column must be a boolean.');

    $this->config->set('show_admin_column', $enabled);

    return $this;
  }

  /**
   * Sets the value for 'meta_box_cb'
   * 
   * @param  callable                       $callback
   * @return \Ponticlaro\Bebop\Cms\Taxonomy           Taxonomy instance
   */
  public function setMetaboxCallback($callback)
  {
    if (!is_callable($callback) && !is_null($callback))
      throw new \Exception('Taxonomy meta_box_cb must be callable or null.');

    $this->config->set('meta_box_cb', $callback);

    return $this;
  }

  /**
   * Returns the value for 'meta_box_cb'
   * 
   * @return string
   */
  public function getMetaboxCallback()
  {
    return $this->config->get('meta_box_cb');
  }

  /**
   * Sets the value for 'update_count_callback'
   * 
   * @param  callable                       $callback
   * @return \Ponticlaro\Bebop\Cms\Taxonomy           Taxonomy instance
   */
  public function setUpdateCountCallback($callback)
  {
    if (!is_callable($callback) && !is_null($callback))
      throw new \Exception('Taxonomy update_count_callback must be callable or null.');

    $this->config->set('update_count_callback', $callback);

    return $this;
  }

  /**
   * Returns the value for 'update_count_callback'
   * 
   * @return string
   */
  public function getUpdateCountCallback()
  {
    return $this->config->get('update_count_callback');
  }

  /**
   * Sets the value for 'sort'
   * 
   * @param  bool                           $enabled
   * @return \Ponticlaro\Bebop\Cms\Taxonomy          Taxonomy instance
   */
  public function setSort($enabled)
  {
   if (!is_bool($enabled))
      throw new \Exception('Taxonomy sort must be a boolean.');

    $this->config->set('sort', $enabled);

    return $this;
  }

  /**
   * Returns the value for 'sort'
   * 
   * @return bool
   */
  public function getSort()
  {
    return $this->config->get('sort');
  }

  /**
   * Applies a register_taxonomy $args configuration array to this Taxonomy
   * 
   * @param  array $args Same $args used for register_taxonomy
   * @return void
   */
  public function applyRawArgs(array $args = array())
  {
    // Intercept labels
    if (isset($args['labels']) && is_array($args['labels'])) {

      $this->setLabels($args['labels']);
      unset($args['labels']);
    }

    // Intercept capabilities
    if (isset($args['capabilities']) && is_array($args['capabilities'])) {

      $this->setCapabilities($args['capabilities']);
      unset($args['capabilities']);
    }

    // Intercept rewrite
    if (isset($args['rewrite']) && is_array($args['rewrite'])) {

      $this->setRewrite($args['rewrite']);
      unset($args['rewrite']);
    }

    // Intercept post_types
    if (isset($args['post_types']) && is_array($args['post_types'])) {

      $this->setPostTypes($args['post_types']);
      unset($args['post_types']);
    }

    $this->config->set($args);
  }

  /**
   * Returns built configuration array
   * 
   * @return array
   */
  public function getFullConfig()
  {
    $config                 = $this->config->getAll();
    $config['labels']       = $this->getLabels();
    $config['capabilities'] = $this->getCapabilities();
    $config['rewrite']      = $this->getRewrite();

    return $config;
  }

  /**
   * Checks if the called method is an alias
   * and calls the existing method
   * 
   * @param  string $name Method name
   * @param  array  $args Method arguments
   * @return mixed        Returns current post type instance or method return value
   */
  public function __call($name, $args)
  {
    if (!isset(static::$methods_alias[$name]))
      throw new \Exception("Taxonomy::$name alias method doesn't exist");

    return call_user_func_array(array($this, static::$methods_alias[$name]), $args);
  }

  /**
   * Sets both the singular and plural names
   * 
   * @param  string                         $singular Singular name to be set
   * @param  string                         $plural   Plural name to be set
   * @return \Ponticlaro\Bebop\Cms\Taxonomy           Taxonomy instance
   */
  protected function __setName($singular, $plural = null)
  {
    $this->__setSingularName($singular);

    if (is_null($plural))
      $plural = $singular . 's';

    $this->__setPluralName($plural);

    return $this;
  }

  /**
   * Sets the singular name
   * 
   * @param  string                         $name Singular name to be set
   * @return \Ponticlaro\Bebop\Cms\Taxonomy       Taxonomy instance
   */
  protected function __setSingularName($name)
  {
    if (!is_string($name))
      throw new \Exception('Taxonomy singular name must be a string.');

    $this->config->set('singular_name', $name);

    return $this;
  }

  /**
   * Sets the plural name
   * 
   * @param  string                         $name Plural name to be set
   * @return \Ponticlaro\Bebop\Cms\Taxonomy       Taxonomy instance
   */
  protected function __setPluralName($name)
  {
    if (!is_string($name))
      throw new \Exception('Taxonomy plural name must be a string.');

    $this->config->set('plural_name', $name);

    if (!$this->config->get('label'))
      $this->config->set('label', $name);

    return $this;
  }

  /**
   * Sets default labels based on singular and plural names
   * 
   * @return void
   */
  private function __setDefaultLabels()
  {
    $singular = $this->config->get('singular_name');
    $plural   = $this->config->get('plural_name');
    $labels   = array(
      'name'              => $plural,
      'singular_name'     => $singular,
      'search_items'      => 'Search '. $plural,
      'all_items'         => 'All '. $plural,
      'parent_item'       => 'Parent '. $singular,
      'parent_item_colon' => 'Parent '. $singular .':',
      'edit_item'         => 'Edit '. $singular,
      'update_item'       => 'Update '. $singular,
      'add_new_item'      => 'Add New '. $singular,
      'new_item_name'     => 'New '. $singular .' Name',
      'menu_name'         => $plural,
    );

    $this->setLabels($labels);
  }

  /**
   * Register taxonomy 
   *
   * @return void
   */
  public function __register()
  {
    register_taxonomy($this->getId(), $this->getPostTypes(), $this->getFullConfig());
  }
}