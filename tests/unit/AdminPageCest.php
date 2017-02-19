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
      }
    ]);

    // Mock admin_url
    $this->m['admin_url'] = Test::func('Ponticlaro\Bebop\Cms', 'admin_url', '/wp-admin/');

    // Mock add_action
    $this->m['add_action'] = Test::func('Ponticlaro\Bebop\Cms', 'add_action', true);

    // Mock callback
    $this->m['callback'] = Test::func('Ponticlaro\Bebop\Cms', 'callback_mock', true);

    // Mock callback alternative
    $this->m['callback_alt'] = Test::func('Ponticlaro\Bebop\Cms', 'callback_mock_alt', true);

    // Mock AdminPage Tab
    $this->m['Tab'] = Test::double('Ponticlaro\Bebop\Cms\AdminPage\Tab', [
      '__construct' => null
    ]);
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
  public function createWithConfigArray(UnitTester $I)
  {
    // Create test instance
    $page = new AdminPage('Title', [

    ]);

    // Create test instance
    $page = new AdminPage([
      
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
  public function setAndGetTabs(UnitTester $I)
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
    $this->m['Tab']->verifyInvokedOnce(['title-tab1', 'Tab1', [
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
}