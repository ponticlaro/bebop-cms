<?php

namespace Ponticlaro\Bebop\Cms;

use Ponticlaro\Bebop\Cms\AdminPage\Tab;
use Ponticlaro\Bebop\Common\Collection;
use Ponticlaro\Bebop\Common\Utils;
use Ponticlaro\Bebop\UI\Helpers\ModuleFactory;

class AdminPage extends \Ponticlaro\Bebop\Common\Patterns\TrackableObjectAbstract  {
    
  /**
   * Required trackable object type
   * 
   * @var string
   */
  protected $__trackable_type = 'admin_page';

  /**
   * Required trackable object ID
   * 
   * @var string
   */
  protected $__trackable_id;

  /**
   * Configuration parameters
   * 
   * @var Ponticlaro\Bebop\Common\Collection
   */
  protected $config;

  /**
   * Options collection
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
   * Tabs collection
   * 
   * @var Ponticlaro\Bebop\Common\Collection
   */
  protected $tabs;

  /**
   * Data collection
   * 
   * @var Ponticlaro\Bebop\Common\Collection
   */
  protected $data;

  /**
   * Instantiates a new Admin page
   * 
   * @param string   $title Admin page tile. Used to create its access slug
   * @param callable $args  Function to call that will contain all the page logic or configuration arguments
   */
  public function __construct($title, $args = null)
  {
    // Set config object with default configuration
    $this->config = new Collection(array(
      'page_title' => '',
      'menu_title' => '',
      'capability' => 'manage_options',
      'menu_slug'  => '',
      'function'   => '',
      'icon_url'   => '',
      'position'   => null,
      'url'        => '',
      'parent'     => ''
    ));

    // Set options object
    $this->options = new Collection();

    // Set sections object
    $this->sections = new Collection();

    // Set tabs object
    $this->tabs = new Collection();

    // Set data object
    $this->data = new Collection();

    // Check if $title is in fact a configuration array
    if ($title && is_array($title)) {
        $args  = $title;
        $title = null;
    }

    // Set Page Title
    if ($title)
      $this->setPageTitle($title);

    if (is_callable($args)) {

      $this->setFunction($args);
    }

    elseif (is_array($args)) {

      $this->applyArgs($args);
    }

    // Register Settings
    add_action('admin_init', array($this, '__handleSettingsRegistration'));

    // Register Admin Page
    add_action('admin_menu', array($this, '__handlePageRegistration'));
  }

  /**
   * Applies a list of configuration values
   * 
   * @param  array $args Configuration array
   * @return void
   */
  public function applyArgs(array $args = [])
  {
    // Handle 'tabs'
    if (isset($args['tabs']) && is_array($args['tabs'])) {

      foreach ($args['tabs'] as $tab_config) {

        if (isset($tab_config['title']) && $tab_config['title']) {

          $tab_title = $tab_config['title'];
          unset($tab_config['title']);

          $this->addTab($tab_title, $tab_config);
        }
      }

      unset($args['tabs']);
    }

    // Handle 'id'
    if (isset($args['id']) && $args['id']) {
      $this->setId($args['id']);
      unset($args['id']);
    }

    // Handle 'title'
    if (isset($args['title']) && $args['title']) {
      $this->setPageTitle($args['title']);
      unset($args['title']);
    }

    // Handle 'page_title'
    if (isset($args['page_title']) && $args['page_title']) {
      $this->setPageTitle($args['title']);
      unset($args['page_title']);
    }

    // Handle 'menu_title'
    if (isset($args['menu_title']) && $args['menu_title']) {
      $this->setMenuTitle($args['menu_title']);
      unset($args['menu_title']);
    }

    // Handle 'menu_slug'
    if (isset($args['menu_slug']) && $args['menu_slug']) {
      $this->setMenuSlug($args['menu_slug']);
      unset($args['menu_slug']);
    }

    // Handle 'parent'
    if (isset($args['parent']) && $args['parent']) {
      $this->setParent($args['parent']);
      unset($args['parent']);
    }

    // Handle 'capability'
    if (isset($args['capability']) && $args['capability']) {
      $this->setCapability($args['capability']);
      unset($args['capability']);
    }

    // Handle 'icon_url'
    if (isset($args['icon_url']) && $args['icon_url']) {
      $this->setIconUrl($args['icon_url']);
      unset($args['icon_url']);
    }

    // Handle 'position'
    if (isset($args['position']) && $args['position']) {
      $this->setPosition($args['position']);
      unset($args['position']);
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

    // Handle 'data'
    if (isset($args['data']) && is_array($args['data'])) {
      $this->setData($args['data']);
      unset($args['data']);
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
   * Sets data
   * 
   * @param array $data
   */
  public function setData(array $data)
  {
    $this->data->set($data);

    return $this;
  }

  /**
   * Adds a single key/value data item
   * 
   * @param string $key   Data key
   * @param string $value Data value
   */
  public function addDataItem($key, $value)
  {
    $this->data->set($key, $value);

    return $this;
  }

  /**
   * Returns all data
   * 
   * @return array
   */
  public function getData()
  {
    return $this->data->getAll();
  }

  /**
   * Sets admin page ID
   * 
   * @param string $id
   */
  public function setId($id)
  {
    if (is_string($id)) {

      // Remove quotes, as these break data saving
      $id = str_replace(['"', "'"], "", $id);

      // Slugify $id
      $this->__trackable_id = Utils::slugify($id);
    }

    return $this;
  }

  /**
   * Returns admin page ID
   * 
   * @return string     
   */
  public function getId()
  {
    return $this->__trackable_id;
  }

  /**
   * Sets page title
   * 
   * @param string $title
   */
  public function setPageTitle($title)
  {
    if (!is_string($title))
      throw new \Exception('AdminPage title must be a string');

    $this->config->set('page_title', $title);

    if (!$this->getId())
      $this->setId($title);

    if (!$this->getMenuTitle())
      $this->setMenuTitle($title);

    if (!$this->getMenuSlug())
      $this->setMenuSlug($title);

    return $this;
  }

  /**
   * Returns page title
   * 
   * @return string
   */
  public function getPageTitle()
  {
    return $this->config->get('page_title');
  }

  /**
   * Sets menu title
   * 
   * @param string $title
   */
  public function setMenuTitle($title)
  {
    if (!is_string($title))
      throw new \Exception('AdminPage menu title must be a string');

    $this->config->set('menu_title', $title);

    return $this;
  }

  /**
   * Returns menu title
   * 
   * @return string
   */
  public function getMenuTitle()
  {
    return $this->config->get('menu_title');
  }

  /**
   * Sets menu slug
   * 
   * @param string $slug
   */
  public function setMenuSlug($slug)
  {
    if (!is_string($slug))
      throw new \Exception('AdminPage menu slug must be a string');

    $slug = Utils::slugify($slug, array('separator' => '-'));

    $this->config->set('menu_slug', str_replace(['"', "'"], "", $slug));
    $this->config->set('url', admin_url() .'admin.php?page='. $this->config->get('menu_slug'));

    return $this;
  }

  /**
   * Returns menu slug
   * 
   * @return string
   */
  public function getMenuSlug()
  {
    return $this->config->get('menu_slug');
  }

  /**
   * Returns admin page URL
   * 
   * @return string
   */
  public function getUrl()
  {
    return $this->config->get('url');
  }

  /**
   * Sets parent page
   * 
   * @param string $parent
   */
  public function setParent($parent)
  {
    if (!is_string($parent))
      throw new \Exception('AdminPage parent must be a string');

    $this->config->set('parent', $parent);

    return $this;
  }

  /**
   * Returns parent
   * 
   * @return string
   */
  public function getParent()
  {
    return $this->config->get('parent');
  }

  /**
   * Sets capability
   * 
   * @param string $capability
   */
  public function setCapability($capability)
  {
    if (!is_string($capability))
      throw new \Exception('AdminPage capability must be a string');

    $this->config->set('capability', $capability);

    return $this;
  }

  /**
   * Returns capability
   * 
   * @return string
   */
  public function getCapability()
  {
    return $this->config->get('capability');
  }

  /**
   * Sets function
   * 
   * @param callable $fn
   */
  public function setFunction($function)
  {   
    if (!is_callable($function))
      throw new \Exception('AdminPage function must be callable');
        
    $this->config->set('function', $function);

    return $this;
  }

  /**
   * Returns function
   * 
   * @return callable
   */
  public function getFunction()
  {
    return $this->config->get('function');
  }

  /**
   * Sets position
   * 
   * @param mixed $position
   */
  public function setPosition($position)
  {   
    if (!is_string($position) && !is_integer($position))
      throw new \Exception('AdminPage position must be either a string or an integer');
        
    $this->config->set('position', $position);

    return $this;
  }

  /**
   * Returns position
   * 
   * @return mixed
   */
  public function getPosition()
  {
      return $this->config->get('position');
  }

  /**
   * Sets icon url
   * 
   * @param mixed $url
   */
  public function setIconUrl($url)
  {   
    if (!is_string($url))
      throw new \Exception('AdminPage icon url must be a string');
        
    $this->config->set('icon_url', $url);

    return $this;
  }

  /**
   * Returns icon url
   * 
   * @return string
   */
  public function getIconUrl()
  {
    return $this->config->get('icon_url');
  }

  /**
   * Adds a single content section
   * 
   * @param string $id   ID of a module in the UI ModuleFactory class
   * @param array  $args Arguments for the section
   */
  public function addSection($id, array $args)
  {
    if (ModuleFactory::canManufacture($id)) {
        
      $section = ModuleFactory::create($id, $args);
      $this->sections->push($section);
    }

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
    if (ModuleFactory::canManufacture($name)) {
        
      $args    = isset($args[0]) && is_array($args[0]) ? $args[0] : [];
      $section = ModuleFactory::create($name, $args);

      $this->sections->push($section);
    }

    return $this;
  }

  /**
   * Collects all field names within sections
   * 
   * @param  object $data       Data collection
   * @param  object $admin_page This metabox instance
   * @return void    
   */
  public function __collectSectionsFieldNames($data, $admin_page)
  {
    foreach($this->sections->getAll() as $section) {
      $section->renderMainTemplate();
    }
  }

  /**
   * Adds a single tab
   * 
   * @param string $title Tab title
   * @param mixed  $args  Tab callable or array of arguments
   */
  public function addTab($title, $args)
  {
    if (is_string($title) && (is_callable($args) || is_array($args))) {

      $id = $this->__trackable_id .'-'. Utils::slugify($title, array('separator' => '-'));

      $this->tabs->push(new Tab($id, $title, $args));
    }

    return $this;
  }

  /**
   * Returns all tabs
   * 
   * @return array
   */
  public function getTabs()
  {
    return $this->tabs->getAll();
  }

  /**
   * Register single page settings based on page contents
   * 
   * @return void
   */
  public function __handleSettingsRegistration()
  {
    // Get tabs
    $tabs = $this->getTabs();
    
    if (!$tabs) {
      
      // Get sections & callable
      $sections = $this->getAllSections();
      $function = $this->getFunction();
      $names    = [];

      // Fetch control elements name attribute from function
      if ($function) {

        $names += Utils::getControlNamesFromCallable($function, array($this->data, $this));
      }

      // Fetch control elements name attribute from sections
      if ($this->getAllSections()) {
        
        $names += Utils::getControlNamesFromCallable([$this, '__collectSectionsFieldNames'], array($this->data, $this));
      }

      if ($names) {

        $this->options->pushList($names);

        foreach ($names as $name) {  
          register_setting($this->getId(), $name);
        }
      }

      // Reset sections so that we do not have duplicates
      $this->sections->clear()->pushList($sections);
    }
  }

  /**
   * Sets single page data
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
   * Defines which function should be used based on the settings provided
   * See http://codex.wordpress.org/Administration_Menus
   * 
   * @return void
   */
  public function __handlePageRegistration()
  {
    $parent = $this->getParent();

    if ($parent) {

      switch ($parent) {
        case 'dashboard':
          $fn = 'add_dashboard_page';
          break;

        case 'posts':
          $fn = 'add_posts_page';
          break;

        case 'pages':
          $fn = 'add_pages_page';
          break;

        case 'media':
          $fn = 'add_media_page';
          break;

        case 'links':
          $fn = 'add_links_page';
          break;

        case 'comments':
          $fn = 'add_comments_page';
          break;

        case 'theme':
          $fn = 'add_theme_page';
          break;

        case 'plugins':
          $fn = 'add_plugins_page';
          break;

        case 'users':
          $fn = 'add_users_page';
          break;

        case 'tools':
          $fn = 'add_management_page';
          break;

        case 'settings':
          $fn = 'add_options_page';
          break;
        
        default:
          $fn = 'add_submenu_page';
          break;
      }

      if ($fn == 'add_submenu_page') {

        $fn(
          $this->getParent(),
          $this->getPageTitle(), 
          $this->getMenuTitle(), 
          $this->getCapability(), 
          $this->getMenuSlug(), 
          array($this, 'baseHtml')
        );
      }

      else {

        $fn(
          $this->getPageTitle(), 
          $this->getMenuTitle(), 
          $this->getCapability(), 
          $this->getMenuSlug(), 
          array($this, 'baseHtml')
        );
      }
    }

    else {

      add_menu_page(
        $this->getPageTitle(), 
        $this->getMenuTitle(), 
        $this->getCapability(), 
        $this->getMenuSlug(), 
        array($this, 'baseHtml'), 
        $this->getIconUrl(), 
        $this->getPosition() 
      );
    }   
  }

  /**
   * Removes this admin page
   * 
   * @return void
   */
  public function destroy()
  {
    remove_menu_page($this->getMenuSlug());
  }

  /**
   * Checks if the user have permission to access this page
   * 
   * @return void
   */
  protected function __checkPermissions()
  {
    if (!current_user_can($this->getCapability()))
      wp_die(__( 'You do not have sufficient permissions to access this page.'));
  }

  /**
   * Base Html for any administration page
   * Executes the function passed in the "function" parameter
   * 
   * @return void
   */
  public function baseHtml()
  { 
    // Check if currently authenticated user can access this page
    $this->__checkPermissions(); ?>

    <div class="wrap">
      <h2><?php echo $this->getPageTitle(); ?></h2>
      <form method="post" action="options.php">
          
        <?php settings_errors();

        if ($this->getTabs()) { 

          $this->renderTabs();
        }

        elseif ($this->getFunction() || $this->getAllSections()) {

          $this->renderSinglePage();

        } ?>

      </form>
    </div><!-- /.wrap -->
      
  <?php }

  /**
   * Renders tabbed UI
   * 
   * @return void
   */
  protected function renderTabs()
  {   
    $tabs        = $this->getTabs();
    $page_url    = $this->config->get('url');
    $current_tab = isset($_GET['tab']) ? $_GET['tab'] : $tabs[0]->getId();

    ?>
    
    <h2 class="nav-tab-wrapper">
      <?php foreach ($tabs as $tab) { ?>
        <a href="<?php echo $page_url . '&tab=' . $tab->getId(); ?>" class="nav-tab<?php if ($current_tab == $tab->getId()) echo ' nav-tab-active'; ?>">
            <?php echo $tab->getTitle(); ?>
        </a>
      <?php } ?>
    </h2>

    <div style="padding-top:30px;">
      <?php foreach ($tabs as $tab) {
          
        if ($current_tab == $tab->getId()) {

          settings_fields($tab->getId());
          $tab->render();
        }
      } ?>
    </div>

  <?php }

  /**
   * Renders single page UI
   * 
   * @return void
   */
  protected function renderSinglePage()
  {
    echo '<div style="padding-top:30px;">';
    
    settings_fields($this->getId());
    $this->__setData();

    // Execute callable
    if ($function = $this->getFunction())
      call_user_func_array($function, array($this->data, $this));

    // Render sections
    $sections = $this->getAllSections();

    if ($sections) {
      foreach($sections as $section) {
        $section->render($this->data->getAll());
      }
    }

    echo '</div>';
  }
}