<?php

use AspectMock\Test;
use Ponticlaro\Bebop\Cms\AdminPage;

class AdminPageCest
{
  /**
   * Expected values for the default config of a 'Title' AdminPage
   * 
   * @var array
   */
  private $base_cfg = [
    'config' => [
      'id'         => 'title',
      'page_title' => 'Title',
      'menu_title' => 'Title',
      'capability' => 'manage_options',
      'menu_slug'  => 'title',
      'function'   => 'Ponticlaro\Bebop\Cms\callback_mock',
      'icon_url'   => '',
      'position'   => null,
      'url'        => '/wp-admin/admin.php?page=title',
      'parent'     => '',
    ],
    'options'  => [],
    'sections' => [],
    'tabs'     => [],
    'data'     => []
  ];

  /**
   * List of mock instances
   * 
   * @var array
   */
  private $m = [];

  public function _before(UnitTester $I)
  {
    // Mock Utils
    $this->m['Utils'] = Test::double('Ponticlaro\Bebop\Common\Utils', [
      'slugify' => function() {
        return strtolower(func_get_arg(0));
      },
      'getControlNamesFromCallable' => function() {
        return [
          'dummy_field_name_1',
          'dummy_field_name_2',
        ];
      }
    ]);

    // Mock AdminPage Tab
    $this->m['Tab'] = Test::double('Ponticlaro\Bebop\Cms\AdminPage\Tab', [
      '__construct' => null,
      'getId'       => 'title-tabmock',
      'render'      => true,
    ]);

    // Mock bebop-ui ModuleAbstract
    $ui_module_mock = $this->m['ui_module'] = Test::double('Ponticlaro\Bebop\UI\Modules\Input', [
      '__construct'        => null,
      'render'             => true,
      'renderMainTemplate' => true,
    ]);

    $this->m['ui_module'] = $ui_module_mock;

    // Mock bebop-ui ModuleFactory
    $this->m['ui_factory'] = Test::double('Ponticlaro\Bebop\UI\Helpers\ModuleFactory', [
      'canManufacture' => true,
      'create'         => function() use($ui_module_mock) {
        return $ui_module_mock->make();
      }
    ]);

    // Mock admin_url
    $this->m['admin_url'] = Test::func('Ponticlaro\Bebop\Cms', 'admin_url', '/wp-admin/');

    // Mock add_action
    $this->m['add_action'] = Test::func('Ponticlaro\Bebop\Cms', 'add_action', true);

    // Mock remove_menu_page
    $this->m['remove_menu_page'] = Test::func('Ponticlaro\Bebop\Cms', 'remove_menu_page', true);

    // Mock settings_errors
    $this->m['settings_errors'] = Test::func('Ponticlaro\Bebop\Cms', 'settings_errors', true);

    // Mock settings_fields
    $this->m['settings_fields'] = Test::func('Ponticlaro\Bebop\Cms', 'settings_fields', true);

    // Mock register_setting
    $this->m['register_setting'] = Test::func('Ponticlaro\Bebop\Cms', 'register_setting', true);

    // Mock get_option
    $this->m['get_option'] = Test::func('Ponticlaro\Bebop\Cms', 'get_option', function() {
      return func_get_arg(0) .'_value';
    });

    // Mock all Administration Menus API registration functions
    $this->m['add_menu_page']       = Test::func('Ponticlaro\Bebop\Cms', 'add_menu_page', true);
    $this->m['add_submenu_page']    = Test::func('Ponticlaro\Bebop\Cms', 'add_submenu_page', true);
    $this->m['add_dashboard_page']  = Test::func('Ponticlaro\Bebop\Cms', 'add_dashboard_page', true);
    $this->m['add_posts_page']      = Test::func('Ponticlaro\Bebop\Cms', 'add_posts_page', true);
    $this->m['add_pages_page']      = Test::func('Ponticlaro\Bebop\Cms', 'add_pages_page', true);
    $this->m['add_media_page']      = Test::func('Ponticlaro\Bebop\Cms', 'add_media_page', true);
    $this->m['add_links_page']      = Test::func('Ponticlaro\Bebop\Cms', 'add_links_page', true);
    $this->m['add_comments_page']   = Test::func('Ponticlaro\Bebop\Cms', 'add_comments_page', true);
    $this->m['add_theme_page']      = Test::func('Ponticlaro\Bebop\Cms', 'add_theme_page', true);
    $this->m['add_plugins_page']    = Test::func('Ponticlaro\Bebop\Cms', 'add_plugins_page', true);
    $this->m['add_users_page']      = Test::func('Ponticlaro\Bebop\Cms', 'add_users_page', true);
    $this->m['add_management_page'] = Test::func('Ponticlaro\Bebop\Cms', 'add_management_page', true);
    $this->m['add_options_page']    = Test::func('Ponticlaro\Bebop\Cms', 'add_options_page', true);

    // Mock callback
    $this->m['callback'] = Test::func('Ponticlaro\Bebop\Cms', 'callback_mock', true);

    // Mock callback alternative
    $this->m['callback_alt'] = Test::func('Ponticlaro\Bebop\Cms', 'callback_mock_alt', true);
  }

  public function _after(UnitTester $I)
  {
    Test::clean();
    Mockery::close();
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\AdminPage::__construct
   * 
   * @param UnitTester $I Tester Module
   */
  public function create(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Check $page->config
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'config');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($page);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->base_cfg['config']);

    // Check $page->options
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'options');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($page);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->base_cfg['options']);

    // Check $page->sections
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'sections');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($page);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->base_cfg['sections']);

    // Check $page->tabs
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'tabs');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($page);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->base_cfg['tabs']);

    // Check $page->data
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'data');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($page);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->base_cfg['data']);

    // Verify add_action was called correctly
    $this->m['add_action']->verifyInvokedOnce(['admin_init', [$page, '__handleSettingsRegistration']]);
    $this->m['add_action']->verifyInvokedOnce(['admin_menu', [$page, '__handlePageRegistration']]);
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\AdminPage::__construct
   * @covers Ponticlaro\Bebop\Cms\AdminPage::applyArgs
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function applyRawArgs(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage([
      'tabs' => [
        [
          'title' => 'Tab1'
        ]
      ],
      'id'         => 'id',
      'title'      => 'Title',
      'page_title' => 'Page Title',
      'menu_title' => 'Menu Title',
      'menu_slug'  => 'menu-slug',
      'parent'     => 'parent',
      'capability' => 'capability',
      'icon_url'   => '/icon/url',
      'position'   => 'position',
      'fn'         => 'Ponticlaro\Bebop\Cms\callback_mock_alt',
      'data'       => [
        'key_1' => 'value_1',
        'key_2' => 'value_2',
      ],
      'sections'   => [
        [
          'ui' => 'input'
        ]
      ]
    ]);

    // Check $page->config
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'config');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($page);

    $I->assertEquals($prop_val->getAll(), [
      'id'         => 'id',
      'page_title' => 'Page Title',
      'menu_title' => 'Menu Title',
      'capability' => 'capability',
      'menu_slug'  => 'menu-slug',
      'function'   => 'Ponticlaro\Bebop\Cms\callback_mock_alt',
      'icon_url'   => '/icon/url',
      'position'   => 'position',
      'url'        => '/wp-admin/admin.php?page=menu-slug',
      'parent'     => 'parent',
    ]);

    // Check $page->sections
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'sections');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($page);
    $sections = $prop_val->getAll();

    $I->assertCount(1, $sections);
    $I->assertTrue(reset($sections) instanceof \Ponticlaro\Bebop\UI\Patterns\ModuleAbstract);

    // Check $page->tabs
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'tabs');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($page);

    $tabs = $prop_val->getAll();

    $I->assertCount(1, $tabs);
    $I->assertTrue(reset($tabs) instanceof \Ponticlaro\Bebop\Cms\AdminPage\Tab);

    // Check $page->data
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'data');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($page);

    $I->assertEquals($prop_val->getAll(), [
      'key_1' => 'value_1',
      'key_2' => 'value_2',
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::getObjectId
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function getObjectId(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::getObjectId
    $I->assertEquals($this->base_cfg['config']['id'], $page->getObjectId());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::getObjectType
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function getObjectType(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::getObjectType
    $I->assertEquals('admin_page', $page->getObjectType());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::setId
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::getId
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetId(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::getId default value
    $I->assertEquals($this->base_cfg['config']['id'], $page->getId());

    // Test ::setId
    $page->setId('test_id');

    // Test ::getId updated value
    $I->assertEquals('test_id', $page->getId());

    // Test ::setId with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($page, $bad_arg_val) {
        $page->setId($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::setPageTitle
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::getPageTitle
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetPageTitle(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::getPageTitle default value
    $I->assertEquals($this->base_cfg['config']['page_title'], $page->getPageTitle());

    // Test ::setPageTitle
    $page->setPageTitle('test_title');

    // Test ::getPageTitle updated value
    $I->assertEquals('test_title', $page->getPageTitle());

    // Test ::setPageTitle with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($page, $bad_arg_val) {
        $page->setPageTitle($bad_arg_val);
      });
    }  
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::setMenuTitle
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::getMenuTitle
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetMenuTitle(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::getMenuTitle default value
    $I->assertEquals($this->base_cfg['config']['menu_title'], $page->getMenuTitle());

    // Test ::setMenuTitle
    $page->setMenuTitle('test_menu_title');

    // Test ::getMenuTitle updated value
    $I->assertEquals('test_menu_title', $page->getMenuTitle());

    // Test ::setMenuTitle with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($page, $bad_arg_val) {
        $page->setMenuTitle($bad_arg_val);
      });
    }  
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::setMenuSlug
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::getMenuSlug
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetMenuSlug(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::getMenuSlug default value
    $I->assertEquals($this->base_cfg['config']['menu_slug'], $page->getMenuSlug());

    // Test ::setMenuSlug
    $page->setMenuSlug('test_menu_slug');

    // Test ::getMenuSlug updated value
    $I->assertEquals('test_menu_slug', $page->getMenuSlug());

    // Test ::setMenuSlug with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($page, $bad_arg_val) {
        $page->setMenuSlug($bad_arg_val);
      });
    }  
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::getUrl
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function getUrl(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::getUrl default value
    $I->assertEquals($this->base_cfg['config']['url'], $page->getUrl());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::setParent
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::getParent
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetParent(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::getParent default value
    $I->assertEquals($this->base_cfg['config']['parent'], $page->getParent());

    // Test ::setParent
    $page->setParent('test_parent');

    // Test ::getParent updated value
    $I->assertEquals('test_parent', $page->getParent());

    // Test ::setParent with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($page, $bad_arg_val) {
        $page->setParent($bad_arg_val);
      });
    }  
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::setCapability
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::getCapability
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetCapability(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::getCapability default value
    $I->assertEquals($this->base_cfg['config']['capability'], $page->getCapability());

    // Test ::setCapability
    $page->setCapability('test_capability');

    // Test ::getCapability updated value
    $I->assertEquals('test_capability', $page->getCapability());

    // Test ::setCapability with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($page, $bad_arg_val) {
        $page->setCapability($bad_arg_val);
      });
    }  
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::setFunction
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::getFunction
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetFunction(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::getFunction default value
    $I->assertEquals($this->base_cfg['config']['function'], $page->getFunction());

    // Test ::setFunction
    $page->setFunction('Ponticlaro\Bebop\Cms\callback_mock_alt');

    // Test ::getFunction updated value
    $I->assertEquals('Ponticlaro\Bebop\Cms\callback_mock_alt', $page->getFunction());

    // Test ::setFunction with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($page, $bad_arg_val) {
        $page->setFunction($bad_arg_val);
      });
    } 
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::setPosition
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::getPosition
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetPosition(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::getPosition default value
    $I->assertEquals($this->base_cfg['config']['position'], $page->getPosition());

    // Test ::setPosition
    $page->setPosition('test_position');

    // Test ::getPosition updated value
    $I->assertEquals('test_position', $page->getPosition());

    // Test ::setPosition with bad arguments
    $bad_args = [
      null,
      [1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($page, $bad_arg_val) {
        $page->setPosition($bad_arg_val);
      });
    }  
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::setIconUrl
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::getIconUrl
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetIconUrl(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::getIconUrl default value
    $I->assertEquals($this->base_cfg['config']['icon_url'], $page->getIconUrl());

    // Test ::setIconUrl
    $page->setIconUrl('test_icon_url');

    // Test ::getIconUrl updated value
    $I->assertEquals('test_icon_url', $page->getIconUrl());

    // Test ::setIconUrl with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($page, $bad_arg_val) {
        $page->setIconUrl($bad_arg_val);
      });
    }  
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::addDataItem
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::setData
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::getData
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function manageData(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::getData default value
    $I->assertEquals($this->base_cfg['data'], $page->getData());

    // Test ::addDataItem
    $page->addDataItem('key_1', 'value_1');

    // Test ::getData updated value
    $I->assertEquals($page->getData(), [
      'key_1' => 'value_1',
    ]);

    // Test ::setData
    $page->setData([
      'key_1' => 'value_1_1',
      'key_2' => 'value_2',
      'key_3' => 'value_3',
    ]);

    // Test ::getData updated value
    $I->assertEquals($page->getData(), [
      'key_1' => 'value_1_1',
      'key_2' => 'value_2',
      'key_3' => 'value_3',
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::addTab
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::getTabs
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetTabs(UnitTester $I, $scenario)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::getTabs default value
    $I->assertEquals($this->base_cfg['tabs'], $page->getTabs());

    // Test ::addTab
    $page->addTab('Tab1', [
      'arg_1' => 'value_1',
      'arg_2' => 'value_2',
    ]);

    // Verify that AdminPage Tab
    $this->m['Tab']->verifyInvokedOnce('__construct', ['title-tab1', 'Tab1', [
      'arg_1' => 'value_1',
      'arg_2' => 'value_2',
    ]]);

    // Get tabs
    $tabs = $page->getTabs();
    $tab  = reset($tabs);

    // Test ::getTabs updated value
    $I->assertTrue(count($tabs) == 1);
    $I->assertTrue($tab instanceof \Ponticlaro\Bebop\Cms\AdminPage\Tab);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::addSection
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::getAllSections
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function manageSections(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::getAllSections default value
    $I->assertEquals($this->base_cfg['sections'], $page->getAllSections());

    // Test ::addSection
    $page->addSection('section_dummy_1', []);

    // Get sections
    $sections = $page->getAllSections();

    // Test ::getAllSections updated value
    $I->assertTrue(count($sections) == 1);
    $I->assertTrue(reset($sections) instanceof \Ponticlaro\Bebop\UI\Patterns\ModuleAbstract);

    // Clean test for new mocks
    Test::clean();

    // Re-Mock bebop-ui ModuleFactory::canManufacture to always return false
    Test::double('Ponticlaro\Bebop\UI\Helpers\ModuleFactory', [
      'canManufacture' => false
    ]);

    // Test ::addSection with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [1],
      new \stdClass,
      '_______undefined_ui_section_id'
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($page, $bad_arg_val) {
        $page->addSection($bad_arg_val, []);
      });
    }  
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::destroy
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function destroy(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::destroy
    $page->destroy();

    // Verify remove_menu_page was called correctl 
    $this->m['remove_menu_page']->verifyInvokedOnce([$this->base_cfg['config']['menu_slug']]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::baseHtml
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function baseHtmlWithTabs(UnitTester $I)
  {
    // Mock AdminPage
    $mock = Test::double('Ponticlaro\Bebop\Cms\AdminPage', [
      '__checkPermissions' => true,
      'getPageTitle'       => true,
      'getTabs'            => true,
      'renderTabs'         => true,
      'getFunction'        => false,
      'getAllSections'     => []
    ]);

    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::baseHtml
    ob_start();
    $page->baseHtml();
    $html = ob_get_clean();

    // Verify required HTML markup is preset in output
    $I->assertTrue(false !== strpos($html, '<form method="post" action="options.php">'));
    $I->assertTrue(false !== strpos($html, '</form>'));

    // Verify ::__checkPermissions was invoked
    $mock->verifyInvokedOnce('__checkPermissions');

    // Verify ::getPageTitle was invoked
    $mock->verifyInvokedOnce('getPageTitle');

    // Verify settings_errors was invoked
    $this->m['settings_errors']->verifyInvokedOnce();

    // Verify ::getPageTitle was invoked
    $mock->verifyInvokedOnce('getPageTitle');

    // Verify ::getTabs was invoked
    $mock->verifyInvokedOnce('getTabs');

    // Verify ::renderTabs was invoked
    $mock->verifyInvokedOnce('renderTabs');

    // Verify ::getFunction was not invoked
    $mock->verifyNeverInvoked('getFunction');

    // Verify ::getAllSections was not invoked
    $mock->verifyNeverInvoked('getAllSections');
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::baseHtml
   * @depends create
   * @depends baseHtmlWithTabs
   * 
   * @param UnitTester $I Tester Module
   */
  public function baseHtmlWithCallbackOrSections(UnitTester $I)
  {
    // Mock AdminPage
    $mock = Test::double('Ponticlaro\Bebop\Cms\AdminPage', [
      '__checkPermissions' => true,
      'getTabs'            => false,
      'getFunction'        => true,
      'renderSinglePage'   => true,
    ]);

    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::baseHtml
    ob_start();
    $page->baseHtml();
    $html = ob_get_clean();

    // Verify ::getTabs was invoked
    $mock->verifyInvokedOnce('getTabs');

    // Verify ::renderTabs was not invoked
    $mock->verifyNeverInvoked('renderTabs');

    // Verify ::getFunction was not invoked
    $mock->verifyInvokedOnce('getFunction');

    // Verify ::renderSinglePage was not invoked
    $mock->verifyInvokedOnce('renderSinglePage');
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::renderTabs
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function renderTabs(UnitTester $I)
  {
    // Create AdminPage\Tab mock instance
    $tab_mock = $this->m['Tab']->make();

    // Mock AdminPage
    $mock = Test::double('Ponticlaro\Bebop\Cms\AdminPage', [
      'getTabs'            => [$tab_mock],
      'getFunction'        => true,
      'getAllSections'     => [],
      'renderSinglePage'   => true,
    ]);

    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Get reflection of ::renderTabs and make it accessible
    $method = new \ReflectionMethod('Ponticlaro\Bebop\Cms\AdminPage', 'renderTabs');
    $method->setAccessible(true);
   
    // Test ::renderTabs
    ob_start();
    $method->invoke($page, null, null, null);
    $html = ob_get_clean();
    
    // Verify ::getTabs was invoked
    $mock->verifyInvokedOnce('getTabs');

    // Verify AdminPage\Tab::getId was invoked
    $this->m['Tab']->verifyInvokedMultipleTimes('getId', 5);

    // Verify AdminPage\Tab::getTitle was invoked
    $this->m['Tab']->verifyInvokedOnce('getTitle');

    // Verify settings_fields was invoked
    $this->m['settings_fields']->verifyInvokedOnce($tab_mock->getId());

    // Verify AdminPage\Tab::render was invoked
    $this->m['Tab']->verifyInvokedOnce('render');
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::renderSinglePage
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function renderSinglePage(UnitTester $I)
  {
    // Mock AdminPage
    $mock = Test::double('Ponticlaro\Bebop\Cms\AdminPage', [
      'getId'          => $this->base_cfg['config']['id'],
      '__setData'      => true,
      'getFunction'    => $this->base_cfg['config']['function'],
      'getAllSections' => [
        $this->m['ui_module']->make()
      ],
    ]);

    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Get reflection of ::renderSinglePage and make it accessible
    $method = new \ReflectionMethod('Ponticlaro\Bebop\Cms\AdminPage', 'renderSinglePage');
    $method->setAccessible(true);
   
    // Test ::renderSinglePage
    ob_start();
    $method->invoke($page, null, null, null);
    $html = ob_get_clean();

    // Get reflection of $data property and make it accessible
    $data_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'data');
    $data_prop->setAccessible(true);
    $data = $data_prop->getValue($page);

    // Verify settings_fields was invoked
    $this->m['settings_fields']->verifyInvokedOnce($this->base_cfg['config']['id']);

    // Verify ::getFunction was invoked
    $mock->verifyInvokedOnce('getFunction');

    // Verify callback_mock was invoked
    $this->m['callback']->verifyInvokedOnce([$data, $page]);

    // Verify ::getAllSections was invoked
    $mock->verifyInvokedOnce('getAllSections');

    // Verify ui_module::render was invoked
    $this->m['ui_module']->verifyInvokedOnce('render', [$data->getAll()]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::__call
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function callMagicMethod(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::__call
    $page->input([]);

    // Verify bebop-ui ModuleFactory methods were called
    $this->m['ui_factory']->verifyInvokedOnce('canManufacture', ['input']);
    $this->m['ui_factory']->verifyInvokedOnce('create', ['input', []]);

    // Clean test for more mocks
    Test::clean();

    // Re-Mock bebop-ui ModuleFactory to always return false
    $ui_factory_mock = Test::double('Ponticlaro\Bebop\UI\Helpers\ModuleFactory', [
      'canManufacture' => false
    ]);

    // Check if exception is thrown with bad arguments
    $I->expectException(Exception::class, function() use($page) {
      $page->______testUndefinedUISection();
    });

    $ui_factory_mock->verifyInvokedOnce('canManufacture', ['______testUndefinedUISection']);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::__checkPermissions
   * @depends create
   * @depends manageSections 
   * 
   * @param UnitTester $I Tester Module
   */
  public function checkPermissions(UnitTester $I)
  {
    // Mock current_user_can
    $current_user_can_mock = Test::func('Ponticlaro\Bebop\Cms', 'current_user_can', false);

    // Mock wp_die
    $wp_die_mock = Test::func('Ponticlaro\Bebop\Cms', 'wp_die', true);

    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Get reflection of ::__checkPermissions and make it accessible
    $method = new \ReflectionMethod('Ponticlaro\Bebop\Cms\AdminPage', '__checkPermissions');
    $method->setAccessible(true);
    $method->invoke($page);

    // Verify current_user_can method is called
    $current_user_can_mock->verifyInvokedOnce([$this->base_cfg['config']['capability']]);

    // Verify current_user_can method is called
    $wp_die_mock->verifyInvokedOnce();
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::__collectSectionsFieldNames
   * @depends create
   * @depends manageSections 
   * 
   * @param UnitTester $I Tester Module
   */
  public function collectSectionsFieldNames(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Add section
    $page->addSection('section_1', []);

    // Get reflection of ::__collectSectionsFieldNames and make it accessible
    $method = new \ReflectionMethod('Ponticlaro\Bebop\Cms\AdminPage', '__collectSectionsFieldNames');
    $method->setAccessible(true);
    $method->invoke($page, null, null);

    // Verify bebop-ui ModuleAbstract method is called
    $this->m['ui_module']->verifyInvokedOnce('renderMainTemplate');
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::__handleSettingsRegistration
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function handleSettingsRegistration(UnitTester $I)
  {
    // Mock AdminPage
    $mock = Test::double('Ponticlaro\Bebop\Cms\AdminPage', [
      'getId'          => $this->base_cfg['config']['id'],
      '__setData'      => true,
      'getTabs'        => [],
      'getFunction'    => $this->base_cfg['config']['function'],
      'getAllSections' => [
        $this->m['ui_module']->make()
      ],
    ]);

    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');
   
    // Test ::__handleSettingsRegistration
    $page->__handleSettingsRegistration();

    // Get reflection of $data property and make it accessible
    $data_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'data');
    $data_prop->setAccessible(true);
    $data = $data_prop->getValue($page);

    // Get reflection of $options property and make it accessible
    $options_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'options');
    $options_prop->setAccessible(true);
    $options = $options_prop->getValue($page);

    // Get reflection of $sections property and make it accessible
    $sections_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'sections');
    $sections_prop->setAccessible(true);
    $sections = $sections_prop->getValue($page);

    // Verify ::getTabs was invoked
    $mock->verifyInvokedOnce('getTabs');

    // Verify ::getFunction was invoked
    $mock->verifyInvokedOnce('getFunction');

    // Verify ::getAllSections was invoked
    $mock->verifyInvokedOnce('getAllSections');

    // Verify Utils::getControlNamesFromCallable was invoked for the callback
    $this->m['Utils']->verifyInvokedOnce('getControlNamesFromCallable', [
      $this->base_cfg['config']['function'],
      [
        $data,
        $page,
      ]
    ]);

    // Verify Utils::getControlNamesFromCallable was invoked for sections
    $this->m['Utils']->verifyInvokedOnce('getControlNamesFromCallable', [
      [
        $page,
        '__collectSectionsFieldNames',
      ],
      [
        $data,
        $page,
      ]
    ]);

    // Verify options property was updated
    $I->assertEquals($options->getAll(), [
      'dummy_field_name_1',
      'dummy_field_name_2',
    ]);

    // Verify register_setting was invoked
    foreach ($options->getAll() as $name) {
      $this->m['register_setting']->verifyInvokedOnce([$this->base_cfg['config']['id'], $name]);
    }
    
    // Verify sections property only contain the single added element
    $I->assertCount(1, $sections->getAll());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::__setData
   * @depends create
   * @depends manageSections 
   * 
   * @param UnitTester $I Tester Module
   */
  public function setData(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Get reflection of $options property and make it accessible
    $options_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'options');
    $options_prop->setAccessible(true);
    $options = $options_prop->getValue($page);

    // Add options for test
    $options_list = ['name_1','name_2','name_3'];
    $options->setList($options_list);

    // Get reflection of ::__setData and make it accessible
    $method = new \ReflectionMethod('Ponticlaro\Bebop\Cms\AdminPage', '__setData');
    $method->setAccessible(true);
    
    // Test ::__setData
    $method->invoke($page);

    // Verify bebop-ui ModuleAbstract method is called
    foreach ($options_list as $option_name) {
      $this->m['get_option']->verifyInvokedOnce([$option_name]);
    }
    
    // Get reflection of $data property and make it accessible
    $data_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'data');
    $data_prop->setAccessible(true);
    $data = $data_prop->getValue($page);

    // Build expected data array
    $expected_data = [];

    foreach ($options_list as $option_name) {
      $expected_data[$option_name] = $option_name .'_value';
    }

    // Test data property updated value
    $I->assertEquals($data->getAll(), $expected_data);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::__handlePageRegistration
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::registerTopPage
   * @depends create
   * @depends manageSections 
   * 
   * @param UnitTester $I Tester Module
   */
  public function registerCustomMainPage(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test :: __handlePageRegistration
    $page->__handlePageRegistration();

    // Verify add_menu_page is invoked
    $this->m['add_menu_page']->verifyInvokedOnce([
      $this->base_cfg['config']['page_title'],
      $this->base_cfg['config']['menu_title'],
      $this->base_cfg['config']['capability'],
      $this->base_cfg['config']['menu_slug'],
      [$page, 'baseHtml'],
      $this->base_cfg['config']['icon_url'],
      $this->base_cfg['config']['position'],
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::__handlePageRegistration
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::registerSubPage
   * @depends create
   * @depends manageSections 
   * 
   * @param UnitTester $I Tester Module
   */
  public function registerCustomSubPage(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Get reflection of $config property and make it accessible
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($page);

    // Set parent
    $config->set('parent', 'dummy_parent_page');

    // Test :: __handlePageRegistration
    $page->__handlePageRegistration();

    // Verify add_submenu_page is invoked
    $this->m['add_submenu_page']->verifyInvokedOnce([
      'dummy_parent_page',
      $this->base_cfg['config']['page_title'],
      $this->base_cfg['config']['menu_title'],
      $this->base_cfg['config']['capability'],
      $this->base_cfg['config']['menu_slug'],
      [$page, 'baseHtml']
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::__handlePageRegistration
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::registerDashboardPage
   * @depends create
   * @depends manageSections 
   * 
   * @param UnitTester $I Tester Module
   */
  public function registerDashboardPage(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Get reflection of $config property and make it accessible
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($page);

    // Set parent
    $config->set('parent', 'dashboard');

    // Test :: __handlePageRegistration
    $page->__handlePageRegistration();

    // Verify add_dashboard_page is invoked
    $this->m['add_dashboard_page']->verifyInvokedOnce([
      $this->base_cfg['config']['page_title'],
      $this->base_cfg['config']['menu_title'],
      $this->base_cfg['config']['capability'],
      $this->base_cfg['config']['menu_slug'],
      [$page, 'baseHtml']
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::__handlePageRegistration
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::registerPostsPage
   * @depends create
   * @depends manageSections 
   * 
   * @param UnitTester $I Tester Module
   */
  public function registerPostsPage(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Get reflection of $config property and make it accessible
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($page);

    // Set parent
    $config->set('parent', 'posts');

    // Test :: __handlePageRegistration
    $page->__handlePageRegistration();

    // Verify add_posts_page is invoked
    $this->m['add_posts_page']->verifyInvokedOnce([
      $this->base_cfg['config']['page_title'],
      $this->base_cfg['config']['menu_title'],
      $this->base_cfg['config']['capability'],
      $this->base_cfg['config']['menu_slug'],
      [$page, 'baseHtml']
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::__handlePageRegistration
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::registerPagesPage
   * @depends create
   * @depends manageSections 
   * 
   * @param UnitTester $I Tester Module
   */
  public function registerPagesPage(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Get reflection of $config property and make it accessible
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($page);

    // Set parent
    $config->set('parent', 'pages');

    // Test :: __handlePageRegistration
    $page->__handlePageRegistration();

    // Verify add_pages_page is invoked
    $this->m['add_pages_page']->verifyInvokedOnce([
      $this->base_cfg['config']['page_title'],
      $this->base_cfg['config']['menu_title'],
      $this->base_cfg['config']['capability'],
      $this->base_cfg['config']['menu_slug'],
      [$page, 'baseHtml']
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::__handlePageRegistration
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::registerMediaPage
   * @depends create
   * @depends manageSections 
   * 
   * @param UnitTester $I Tester Module
   */
  public function registerMediaPage(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Get reflection of $config property and make it accessible
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($page);

    // Set parent
    $config->set('parent', 'media');

    // Test :: __handlePageRegistration
    $page->__handlePageRegistration();

    // Verify add_media_page is invoked
    $this->m['add_media_page']->verifyInvokedOnce([
      $this->base_cfg['config']['page_title'],
      $this->base_cfg['config']['menu_title'],
      $this->base_cfg['config']['capability'],
      $this->base_cfg['config']['menu_slug'],
      [$page, 'baseHtml']
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::__handlePageRegistration
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::registerLinksPage
   * @depends create
   * @depends manageSections 
   * 
   * @param UnitTester $I Tester Module
   */
  public function registerLinksPage(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Get reflection of $config property and make it accessible
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($page);

    // Set parent
    $config->set('parent', 'links');

    // Test :: __handlePageRegistration
    $page->__handlePageRegistration();

    // Verify add_links_page is invoked
    $this->m['add_links_page']->verifyInvokedOnce([
      $this->base_cfg['config']['page_title'],
      $this->base_cfg['config']['menu_title'],
      $this->base_cfg['config']['capability'],
      $this->base_cfg['config']['menu_slug'],
      [$page, 'baseHtml']
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::__handlePageRegistration
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::registerCommentsPage
   * @depends create
   * @depends manageSections 
   * 
   * @param UnitTester $I Tester Module
   */
  public function registerCommentsPage(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Get reflection of $config property and make it accessible
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($page);

    // Set parent
    $config->set('parent', 'comments');

    // Test :: __handlePageRegistration
    $page->__handlePageRegistration();

    // Verify add_comments_page is invoked
    $this->m['add_comments_page']->verifyInvokedOnce([
      $this->base_cfg['config']['page_title'],
      $this->base_cfg['config']['menu_title'],
      $this->base_cfg['config']['capability'],
      $this->base_cfg['config']['menu_slug'],
      [$page, 'baseHtml']
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::__handlePageRegistration
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::registerThemePage
   * @depends create
   * @depends manageSections 
   * 
   * @param UnitTester $I Tester Module
   */
  public function registerThemePage(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Get reflection of $config property and make it accessible
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($page);

    // Set parent
    $config->set('parent', 'theme');

    // Test :: __handlePageRegistration
    $page->__handlePageRegistration();

    // Verify add_theme_page is invoked
    $this->m['add_theme_page']->verifyInvokedOnce([
      $this->base_cfg['config']['page_title'],
      $this->base_cfg['config']['menu_title'],
      $this->base_cfg['config']['capability'],
      $this->base_cfg['config']['menu_slug'],
      [$page, 'baseHtml']
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::__handlePageRegistration
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::registerPluginsPage
   * @depends create
   * @depends manageSections 
   * 
   * @param UnitTester $I Tester Module
   */
  public function registerPluginsPage(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Get reflection of $config property and make it accessible
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($page);

    // Set parent
    $config->set('parent', 'plugins');

    // Test :: __handlePageRegistration
    $page->__handlePageRegistration();

    // Verify add_plugins_page is invoked
    $this->m['add_plugins_page']->verifyInvokedOnce([
      $this->base_cfg['config']['page_title'],
      $this->base_cfg['config']['menu_title'],
      $this->base_cfg['config']['capability'],
      $this->base_cfg['config']['menu_slug'],
      [$page, 'baseHtml']
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::__handlePageRegistration
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::registerUsersPage
   * @depends create
   * @depends manageSections 
   * 
   * @param UnitTester $I Tester Module
   */
  public function registerUsersPage(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Get reflection of $config property and make it accessible
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($page);

    // Set parent
    $config->set('parent', 'users');

    // Test :: __handlePageRegistration
    $page->__handlePageRegistration();

    // Verify add_users_page is invoked
    $this->m['add_users_page']->verifyInvokedOnce([
      $this->base_cfg['config']['page_title'],
      $this->base_cfg['config']['menu_title'],
      $this->base_cfg['config']['capability'],
      $this->base_cfg['config']['menu_slug'],
      [$page, 'baseHtml']
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::__handlePageRegistration
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::registerToolsPage
   * @depends create
   * @depends manageSections 
   * 
   * @param UnitTester $I Tester Module
   */
  public function registerToolsPage(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Get reflection of $config property and make it accessible
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($page);

    // Set parent
    $config->set('parent', 'tools');

    // Test :: __handlePageRegistration
    $page->__handlePageRegistration();

    // Verify add_management_page is invoked
    $this->m['add_management_page']->verifyInvokedOnce([
      $this->base_cfg['config']['page_title'],
      $this->base_cfg['config']['menu_title'],
      $this->base_cfg['config']['capability'],
      $this->base_cfg['config']['menu_slug'],
      [$page, 'baseHtml']
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::__handlePageRegistration
   * @covers  Ponticlaro\Bebop\Cms\AdminPage::registerSettingsPage
   * @depends create
   * @depends manageSections 
   * 
   * @param UnitTester $I Tester Module
   */
  public function registerSettingsPage(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Get reflection of $config property and make it accessible
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($page);

    // Set parent
    $config->set('parent', 'settings');

    // Test :: __handlePageRegistration
    $page->__handlePageRegistration();

    // Verify add_options_page is invoked
    $this->m['add_options_page']->verifyInvokedOnce([
      $this->base_cfg['config']['page_title'],
      $this->base_cfg['config']['menu_title'],
      $this->base_cfg['config']['capability'],
      $this->base_cfg['config']['menu_slug'],
      [$page, 'baseHtml']
    ]);
  }
}