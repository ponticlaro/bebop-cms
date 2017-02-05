<?php

use AspectMock\Test;
use Ponticlaro\Bebop\Cms\PostType;

class PostTypeCest
{
  /**
   * Expected values for the default config of a 'Product' type
   * 
   * @var array
   */
  private $prod_cfg = [
    'config' => [
      'id'                 => 'product',
      'public'             => true,
      'has_archive'        => true,
      'publicly_queryable' => true,
      'show_ui'            => true, 
      'query_var'          => true,
      'can_export'         => true,
      'singular_name'      => 'Product',  // Added dynamically
      'plural_name'        => 'Products', // Added dynamically
    ],
    'features' => [
      'title',
      'editor',
      'revisions'
    ],
    'labels' => [
      'name'               => 'Products',
      'singular_name'      => 'Product',
      'menu_name'          => 'Products',
      'all_items'          => 'Products',
      'add_new'            => 'Add Product',
      'add_new_item'       => 'Add new Product', 
      'edit_item'          => 'Edit Product', 
      'new_item'           => 'New Product',
      'view_item'          => 'View Product',
      'search_items'       => 'Search Products',
      'not_found'          => 'There are no Products',
      'not_found_in_trash' => 'There are no Products in trash', 
      'parent_item_colon'  => 'Parent Product:'
    ],
    'capabilities'   => [],
    'taxonomies'     => [],
    'rewrite_config' => [
      'with_front' => false
    ]
  ];

  /**
   * List of mock instances
   * 
   * @var array
   */
  private $mocks = [];

  public function _before(UnitTester $I)
  {
    // Mock Utils
    $this->mocks['Utils'] = Test::double('Ponticlaro\Bebop\Common\Utils', [
      'slugify' => function() {
        return strtolower(func_get_arg(0));
      }
    ]);

    // Mock add_action function
    $this->mocks['add_action'] = Test::func('Ponticlaro\Bebop\Cms', 'add_action', true);
  }

  public function _after(UnitTester $I)
  {
    Test::clean();
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\PostType::__construct
   * @covers Ponticlaro\Bebop\Cms\PostType::__setName
   * @covers Ponticlaro\Bebop\Cms\PostType::__setSingularName
   * @covers Ponticlaro\Bebop\Cms\PostType::__setPluralName
   * @covers Ponticlaro\Bebop\Cms\PostType::__setDefaultLabels
   * 
   * @param UnitTester $I Tester Module
   */
  public function create(UnitTester $I)
  {  
    // Create test instance
    $type = new PostType('Product');

    // Verify add_action was called once
    $this->mocks['add_action']->verifyInvokedOnce(['init', [$type, '__register'], 1]);

    // Check $type->config
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\PostType', 'config');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($type);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->prod_cfg['config']);

    // Check $type->features
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\PostType', 'features');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($type);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->prod_cfg['features']);

    // Check $type->labels
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\PostType', 'labels');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($type);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->prod_cfg['labels']);

    // Check $type->capabilities
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\PostType', 'capabilities');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($type);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->prod_cfg['capabilities']);

    // Check $type->taxonomies
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\PostType', 'taxonomies');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($type);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->prod_cfg['taxonomies']);

    // Check $type->rewrite_config
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\PostType', 'rewrite_config');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($type);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->prod_cfg['rewrite_config']);

    // Test ::__setSingularName and ::__setPluralName with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [null, null],
      new \stdClass,
      ['Product', 0],
      ['Product', 1],
      ['Product', []],
      ['Product', new \stdClass],
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        new PostType($bad_arg_val);
      });
    }    
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\PostType::__construct
   * @covers Ponticlaro\Bebop\Cms\PostType::__setName
   * @covers Ponticlaro\Bebop\Cms\PostType::__setSingularName
   * @covers Ponticlaro\Bebop\Cms\PostType::__setPluralName
   * @covers Ponticlaro\Bebop\Cms\PostType::__setDefaultLabels
   * 
   * @param UnitTester $I Tester Module
   */
  public function createWithIrregularPluralForm(UnitTester $I)
  {
    // Create test instance
    $type = new PostType(['Gallery', 'Galleries']);

    // Verify add_action was called once
    $this->mocks['add_action']->verifyInvokedOnce(['init', [$type, '__register'], 1]);

    // Check $type->config
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\PostType', 'config');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($type);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), [
      'id'                 => 'gallery',
      'public'             => true,
      'has_archive'        => true,
      'publicly_queryable' => true,
      'show_ui'            => true, 
      'query_var'          => true,
      'can_export'         => true,
      'singular_name'      => 'Gallery',  // Added dynamically
      'plural_name'        => 'Galleries', // Added dynamically
    ]);

    // Check $type->features
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\PostType', 'features');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($type);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), [
      'title',
      'editor',
      'revisions'
    ]);

    // Check $type->labels
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\PostType', 'labels');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($type);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), [
      'name'               => 'Galleries',
      'singular_name'      => 'Gallery',
      'menu_name'          => 'Galleries',
      'all_items'          => 'Galleries',
      'add_new'            => 'Add Gallery',
      'add_new_item'       => 'Add new Gallery', 
      'edit_item'          => 'Edit Gallery', 
      'new_item'           => 'New Gallery',
      'view_item'          => 'View Gallery',
      'search_items'       => 'Search Galleries',
      'not_found'          => 'There are no Galleries',
      'not_found_in_trash' => 'There are no Galleries in trash', 
      'parent_item_colon'  => 'Parent Gallery:'
    ]);

    // Check $type->capabilities
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\PostType', 'capabilities');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($type);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEmpty($prop_val->getAll());

    // Check $type->taxonomies
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\PostType', 'taxonomies');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($type);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEmpty($prop_val->getAll());

    // Check $type->rewrite_config
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\PostType', 'rewrite_config');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($type);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), [
      'with_front' => false
    ]);
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\PostType::__construct
   * 
   * @param UnitTester $I Tester Module
   */
  public function createBuiltInPostType(UnitTester $I)
  {
    $build_in_types = [
      'attachment', 
      'post', 
      'page', 
      'revision', 
      'nav_menu_item'
    ];

    foreach ($build_in_types as $type_name) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type_name) {
        new PostType($type_name);
      });
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::getObjectId
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function getObjectId(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Test ::getObjectId
    $I->assertEquals($this->prod_cfg['config']['id'], $type->getObjectId());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::getObjectType
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function getObjectType(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Test ::getObjectType
    $I->assertEquals('post_type', $type->getObjectType());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::setId
   * @covers  Ponticlaro\Bebop\Cms\PostType::getId
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetId(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Test ::getId default value
    $I->assertEquals($this->prod_cfg['config']['id'], $type->getId());

    // Test ::setId
    $type->setId('test_id');
    $I->assertEquals('test_id', $type->getId());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::setLabels
   * @covers  Ponticlaro\Bebop\Cms\PostType::setLabel
   * @covers  Ponticlaro\Bebop\Cms\PostType::getLabels
   * @covers  Ponticlaro\Bebop\Cms\PostType::getLabel
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetLabels(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Test ::getLabels default value
    $I->assertEquals($this->prod_cfg['labels'], $type->getLabels());

    // Test ::getLabel
    foreach ($this->prod_cfg['labels'] as $key => $value) {
      $I->assertEquals($value, $type->getLabel($key));
    }

    // Test ::setLabels
    $updated_labels = [];

    foreach ($this->prod_cfg['labels'] as $key => $value) {
      $updated_labels[$key] = 'Test 1 '. $value;
    }

    $type->setLabels($updated_labels);
    $I->assertEquals($updated_labels, $type->getLabels());
    
    // Test ::setLabel
    $updated_labels = [];

    foreach ($this->prod_cfg['labels'] as $key => $value) {

      $updated_labels[$key] = 'Test 2 '. $value;
      $type->setLabel($key, $updated_labels[$key]);
    }

    $I->assertEquals($updated_labels, $type->getLabels());

    // Test ::setLabel with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->setLabel(reset($this->prod_cfg['labels']), $bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::setDescription
   * @covers  Ponticlaro\Bebop\Cms\PostType::getDescription
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetDescription(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Test ::getDescription default value
    $I->assertNull($type->getDescription());

    // Test ::setDescription
    $type->setDescription('Test type description');

    // Test ::getDescription after update
    $I->assertEquals($type->getDescription(), 'Test type description');

    // Test ::setDescription with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->setDescription($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::makePublic
   * @covers  Ponticlaro\Bebop\Cms\PostType::isPublic
   * @covers  Ponticlaro\Bebop\Cms\PostType::__call
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetPublic(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Test ::isPublic default value
    $I->assertTrue($type->isPublic());

    // Test ::makePublic
    $type->makePublic(false);

    // Test ::isPublic updated value
    $I->assertFalse($type->isPublic());

    // Test ::setPublic alias method
    $type->setPublic(true);

   // Test ::isPublic updated value
    $I->assertTrue($type->isPublic());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::archiveEnabled
   * @covers  Ponticlaro\Bebop\Cms\PostType::hasArchive
   * @covers  Ponticlaro\Bebop\Cms\PostType::__call
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetArchive(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Test ::hasArchive default value
    $I->assertTrue($type->hasArchive());

    // Test ::archiveEnabled
    $type->archiveEnabled(false);

    // Test ::hasArchive updated value
    $I->assertFalse($type->hasArchive());

    // Test ::setHasArchive alias method
    $type->setHasArchive(true);

    // Test ::hasArchive updated value
    $I->assertTrue($type->hasArchive());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::setExcludeFromSearch
   * @covers  Ponticlaro\Bebop\Cms\PostType::isExcludedFromSearch
   * @depends create
   * @depends setAndGetPublic
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetExcludeFromSearch(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Test ::isExcludedFromSearch default value
    $I->assertFalse($type->isExcludedFromSearch());

    // Test ::setExcludeFromSearch
    $type->setExcludeFromSearch(true);

    // Test ::isExcludedFromSearch updated value
    $I->assertTrue($type->isExcludedFromSearch());

    // Test ::setExcludeFromSearch
    $type->setExcludeFromSearch(false);

    // Test ::isExcludedFromSearch updated value
    $I->assertFalse($type->isExcludedFromSearch());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::setHierarchical
   * @covers  Ponticlaro\Bebop\Cms\PostType::isHierarchical
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetHierarchical(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Test ::isHierarchical default value
    $I->assertFalse($type->isHierarchical());

    // Test ::setHierarchical
    $type->setHierarchical(true);

    // Test ::isHierarchical updated value
    $I->assertTrue($type->isHierarchical());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::setExportable
   * @covers  Ponticlaro\Bebop\Cms\PostType::isExportable
   * @covers  Ponticlaro\Bebop\Cms\PostType::__call
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetExportable(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Test ::isExportable default value
    $I->assertTrue($type->isExportable());

    // Test ::setExportable
    $type->setExportable(false);

    // Test ::isExportable updated value
    $I->assertFalse($type->isExportable());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::setPubliclyQueryable
   * @covers  Ponticlaro\Bebop\Cms\PostType::isPubliclyQueryable
   * @depends create
   * @depends setAndGetPublic
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetPubliclyQueryable(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Test ::isPubliclyQueryable default value
    $I->assertTrue($type->isPubliclyQueryable());

    // Test ::setPubliclyQueryable
    $type->setPubliclyQueryable(false);

    // Test ::isPubliclyQueryable updated value
    $I->assertFalse($type->isPubliclyQueryable());

    // Test ::setPubliclyQueryable
    $type->setPubliclyQueryable(true);

    // Test ::isPubliclyQueryable updated value
    $I->assertTrue($type->isPubliclyQueryable());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::showUi
   * @covers  Ponticlaro\Bebop\Cms\PostType::__call
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setShowUI(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Get $type->config
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\PostType', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($type);

    // Test show_ui default value
    $I->assertTrue($config->get('show_ui'));

    // Test ::setShowUI
    $type->showUi(false);

    // Test show_ui updated value
    $I->assertFalse($config->get('show_ui'));

    // Test ::setShowUI alias method
    $type->setShowUi(true);

    // Test show_ui updated value
    $I->assertTrue($config->get('show_ui'));

    // Test ::setShowUI with bad arguments
    $bad_args = [
      null,
      0,
      1,
      'string',
      ['string'],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->showUi($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::showInNavMenus
   * @covers  Ponticlaro\Bebop\Cms\PostType::__call
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setShowInNavMenus(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Get $type->config
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\PostType', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($type);

    // Test show_in_nav_menus default value
    $I->assertNull($config->get('show_in_nav_menus'));

    // Test ::showInNavMenus
    $type->showInNavMenus(true);

    // Test show_in_nav_menus updated value
    $I->assertTrue($config->get('show_in_nav_menus'));

    // Test ::setShowInNavMenus alias method
    $type->setShowInNavMenus(false);

    // Test show_in_nav_menus updated value
    $I->assertFalse($config->get('show_in_nav_menus'));

    // Test ::showInNavMenus with bad arguments
    $bad_args = [
      null,
      0,
      1,
      'string',
      ['string'],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->showInNavMenus($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::showInMenu
   * @covers  Ponticlaro\Bebop\Cms\PostType::__call
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setShowInMenu(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Get $type->config
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\PostType', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($type);

    // Test show_in_menu default value
    $I->assertNull($config->get('show_in_menu'));

    // Test ::showInMenu
    $type->showInMenu(true);

    // Test show_in_menu updated value
    $I->assertTrue($config->get('show_in_menu'));

    // Test ::setShowInMenu alias method
    $type->setShowInMenu(false);

    // Test show_in_menu updated value
    $I->assertFalse($config->get('show_in_menu'));

    // Test ::showInMenu with bad arguments
    $bad_args = [
      null,
      0,
      1,
      'string',
      ['string'],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->showInMenu($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::showInAdminBar
   * @covers  Ponticlaro\Bebop\Cms\PostType::__call
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setShowInAdminBar(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Get $type->config
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\PostType', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($type);

    // Test show_in_admin_bar default value
    $I->assertNull($config->get('show_in_admin_bar'));

    // Test ::showInAdminBar
    $type->showInAdminBar(true);

    // Test show_in_admin_bar updated value
    $I->assertTrue($config->get('show_in_admin_bar'));

    // Test ::setShowInAdminBar alias method
    $type->setShowInAdminBar(false);

    // Test show_in_admin_bar updated value
    $I->assertFalse($config->get('show_in_admin_bar'));

    // Test ::showInAdminBar with bad arguments
    $bad_args = [
      null,
      0,
      1,
      'string',
      ['string'],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->showInAdminBar($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::setMenuPosition
   * @covers  Ponticlaro\Bebop\Cms\PostType::getMenuPosition
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetMenuPosition(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Test ::getMenuPosition default value
    $I->assertNull($type->getMenuPosition());

    // Test ::setMenuPosition
    $type->setMenuPosition(1);

    // Test ::getMenuPosition updated value
    $I->assertEquals($type->getMenuPosition(), 1);

    // Test ::setMenuPosition with bad arguments
    $bad_args = [
      null,
      'string',
      [null, 'string'],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->setMenuPosition($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::setMenuIcon
   * @covers  Ponticlaro\Bebop\Cms\PostType::getMenuIcon
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetMenuIcon(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Test ::getMenuIcon default value
    $I->assertNull($type->getMenuIcon());

    // Test ::setMenuIcon
    $type->setMenuIcon('path/to/menu/icon.jpg');

    // Test ::getMenuIcon updated value
    $I->assertEquals($type->getMenuIcon(), 'path/to/menu/icon.jpg');

    // Test ::setMenuIcon with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [0, 1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->setMenuIcon($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::setCapabilityType
   * @covers  Ponticlaro\Bebop\Cms\PostType::getCapabilityType
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetCapabilityType(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Test ::getCapabilityType default value
    $I->assertNull($type->getCapabilityType());

    // Test ::setCapabilityType
    $type->setCapabilityType('page');

    // Test ::getCapabilityType updated value
    $I->assertEquals($type->getCapabilityType(), 'page');

    // Test ::setCapabilityType with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [0, 1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->setCapabilityType($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::getCapabilities
   * @covers  Ponticlaro\Bebop\Cms\PostType::setCapabilities
   * @covers  Ponticlaro\Bebop\Cms\PostType::addCapability
   * @covers  Ponticlaro\Bebop\Cms\PostType::replaceCapabilities
   * @covers  Ponticlaro\Bebop\Cms\PostType::removeCapabilities
   * @covers  Ponticlaro\Bebop\Cms\PostType::removeCapability
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function manageCapabilities(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Test ::getCapabilities default value
    $I->assertEmpty($type->getCapabilities());

    // Test ::addCapability
    $type->addCapability('edit_post', 'edit_product_init');

     // Test ::getCapabilities updated value
    $I->assertEquals($type->getCapabilities(), [
      'edit_post' => 'edit_product_init'
    ]);

    // Capabilities to set
    $caps = [
      'edit_post'          => 'edit_product', 
      'read_post'          => 'read_product', 
      'delete_post'        => 'delete_product', 
      'edit_posts'         => 'edit_product', 
      'edit_others_posts'  => 'edit_others_products', 
      'publish_posts'      => 'publish_products',       
      'read_private_posts' => 'read_private_products', 
      'create_posts'       => 'edit_products', 
    ];

    // Test ::setCapabilities
    $type->setCapabilities($caps);

    // Test ::getCapabilities updated value
    $I->assertEquals($type->getCapabilities(), $caps);

    // Capabilities to replace with
    $replace_caps = [
      'edit_post'          => 'edit_product_mod1', 
      'read_post'          => 'read_product_mod1', 
      'delete_post'        => 'delete_product_mod1', 
      'edit_posts'         => 'edit_product_mod1', 
      'edit_others_posts'  => 'edit_others_products_mod1', 
      'publish_posts'      => 'publish_products_mod1',       
      'read_private_posts' => 'read_private_products_mod1', 
      'create_posts'       => 'edit_products_mod1', 
    ];

    // Test ::replaceCapabilities
    $type->replaceCapabilities($replace_caps);

    // Test ::getCapabilities updated value
    $I->assertEquals($type->getCapabilities(), $replace_caps);

    // Test ::removeCapability
    $type->removeCapability('edit_post');

    // Update $replace_caps to match expected value
    unset($replace_caps['edit_post']);

    // Test ::getCapabilities updated value
    $I->assertEquals($type->getCapabilities(), $replace_caps);

    // Test ::removeCapabilities
    $type->removeCapabilities([
      'read_post',
      'delete_post'
    ]);

    // Update $replace_caps to match expected value
    unset($replace_caps['read_post']);
    unset($replace_caps['delete_post']);

    // Test ::getCapabilities updated value
    $I->assertEquals($type->getCapabilities(), $replace_caps);

    // Test ::addCapability with bad arguments
    $bad_args = [
      [null, 'value'],
      [0, 'value'],
      [1, 'value'],
      [[0, 1], 'value'],
      [new \stdClass, 'value'],
      ['value', null],
      ['value', 0],
      ['value', 1],
      ['value', [0, 1]],
      ['value', new \stdClass],
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->addCapability($bad_arg_val[0], $bad_arg_val[1]);
      });
    }  

    // Test ::removeCapability with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [0, 1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->removeCapability($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::setMapMetaCapabilities
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setMapMetaCapabilities(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Get $type->config
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\PostType', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($type);

    // Test map_meta_cap default value
    $I->assertNull($config->get('map_meta_cap'));

    // Test ::setMapMetaCapabilities
    $type->setMapMetaCapabilities(true);

    // Test map_meta_cap updated value
    $I->assertTrue($config->get('map_meta_cap'));

    // Test ::setMapMetaCap alias method
    $type->setMapMetaCap(false);

    // Test map_meta_cap updated value
    $I->assertFalse($config->get('map_meta_cap'));

    // Test ::setMapMetaCapabilities with bad arguments
    $bad_args = [
      null,
      0,
      1,
      'string',
      ['string'],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->setMapMetaCapabilities($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::getFeatures
   * @covers  Ponticlaro\Bebop\Cms\PostType::addFeatures
   * @covers  Ponticlaro\Bebop\Cms\PostType::addFeature
   * @covers  Ponticlaro\Bebop\Cms\PostType::replaceFeatures
   * @covers  Ponticlaro\Bebop\Cms\PostType::removeFeatures
   * @covers  Ponticlaro\Bebop\Cms\PostType::removeFeature
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function manageFeatures(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Get default features
    $def_feats = $this->prod_cfg['features'];

    // Test ::getFeatures default value
    $I->assertEquals($type->getFeatures(), $def_feats);

    // Test ::addFeature
    $type->addFeature('excerpt');

    // Update $def_feats to match expected value
    $def_feats[] = 'excerpt';

     // Test ::getFeatures updated value
    $I->assertEquals($type->getFeatures(), $def_feats);

    // Features to set
    $add_feats = [
      'feat_1',
      'feat_2'
    ];

    // Update $def_feats to match expected value
    $def_feats[] = 'feat_1';
    $def_feats[] = 'feat_2';

    // Test ::addFeatures
    $type->addFeatures($add_feats);

    // Test ::getFeatures updated value
    $I->assertSame($type->getFeatures(), $def_feats);

    // Features to replace with
    $replace_feats = [
      'feat_1_replaced',
      'feat_2_replaced',
      'feat_3_replaced',
      'feat_4_replaced',
      'feat_5_replaced',
    ];

    // Test ::replaceFeatures
    $type->replaceFeatures($replace_feats);

    // Test ::getFeatures updated value
    $I->assertSame($type->getFeatures(), $replace_feats);

    // Test ::removeFeature
    $type->removeFeature('feat_1_replaced');

    // Update $replace_feats to match expected value
    unset($replace_feats[0]);

    // Test ::getFeatures updated value
    $I->assertSame($type->getFeatures(), $replace_feats);

    // Test ::removeFeatures
    $type->removeFeatures([
      'feat_2_replaced',
      'feat_3_replaced'
    ]);

    // Update $replace_feats to match expected value
    unset($replace_feats[1]);
    unset($replace_feats[2]);

    // Test ::getFeatures updated value
    $I->assertSame($type->getFeatures(), $replace_feats);

    // Test ::addFeature with bad arguments
    $bad_args = [
      null,
      0,
      1,
      ['string'],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->addFeature($bad_arg_val);
      });
    }  

    // Test ::removeFeature with bad arguments
    $bad_args = [
      null,
      0,
      1,
      ['string'],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->removeFeature($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::setMetaboxesCallback
   * @covers  Ponticlaro\Bebop\Cms\PostType::getMetaboxesCallback
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetMetaboxesCallback(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Test ::getMetaboxesCallback default value
    $I->assertNull($type->getMetaboxesCallback());

    // Test ::setMetaboxesCallback
    $type->setMetaboxesCallback('is_string');

    // Test ::getMetaboxesCallback updated value
    $I->assertEquals($type->getMetaboxesCallback(), 'is_string');

    // Test ::setRegisterMetaBoxCb alias method
    $type->setRegisterMetaBoxCb('is_bool');

    // Test ::getMetaboxesCallback updated value
    $I->assertEquals($type->getMetaboxesCallback(), 'is_bool');

    // Test ::setMetaboxesCallback with bad arguments
    $bad_args = [
      null,
      0,
      1,
      'string',
      [0, 1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->setMetaboxesCallback($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::getTaxonomies
   * @covers  Ponticlaro\Bebop\Cms\PostType::addTaxonomies
   * @covers  Ponticlaro\Bebop\Cms\PostType::addTaxonomy
   * @covers  Ponticlaro\Bebop\Cms\PostType::replaceTaxonomies
   * @covers  Ponticlaro\Bebop\Cms\PostType::removeTaxonomies
   * @covers  Ponticlaro\Bebop\Cms\PostType::removeTaxonomy
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function manageTaxonomies(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Get default taxonomies
    $def_taxs = $this->prod_cfg['taxonomies'];

    // Test ::getTaxonomies default value
    $I->assertEquals($type->getTaxonomies(), $def_taxs);

    // Test ::addTaxonomy
    $type->addTaxonomy('tax_1');

    // Update $add_taxs to match expected value
    $def_taxs[] = 'tax_1';

     // Test ::getTaxonomies updated value
    $I->assertEquals($type->getTaxonomies(), $def_taxs);

    // Features to set
    $add_taxs = [
      'tax_2',
      'tax_3'
    ];

    // Update $add_taxs to match expected value
    $def_taxs[] = 'tax_2';
    $def_taxs[] = 'tax_3';

    // Test ::addTaxonomies
    $type->addTaxonomies($add_taxs);

    // Test ::getTaxonomies updated value
    $I->assertSame($type->getTaxonomies(), $def_taxs);

    // Features to replace with
    $replace_taxs = [
      'tax_1_replaced',
      'tax_2_replaced',
      'tax_3_replaced',
      'tax_4_replaced',
      'tax_5_replaced',
    ];

    // Test ::replaceTaxonomies
    $type->replaceTaxonomies($replace_taxs);

    // Test ::getTaxonomies updated value
    $I->assertSame($type->getTaxonomies(), $replace_taxs);

    // Test ::removeTaxonomy
    $type->removeTaxonomy('tax_1_replaced');

    // Update $replace_taxs to match expected value
    unset($replace_taxs[0]);

    // Test ::getTaxonomies updated value
    $I->assertSame($type->getTaxonomies(), $replace_taxs);

    // Test ::removeFeatures
    $type->removeTaxonomies([
      'tax_2_replaced',
      'tax_3_replaced'
    ]);

    // Update $replace_taxs to match expected value
    unset($replace_taxs[1]);
    unset($replace_taxs[2]);

    // Test ::getTaxonomies updated value
    $I->assertSame($type->getTaxonomies(), $replace_taxs);

    // Test ::addTaxonomy with bad arguments
    $bad_args = [
      null,
      0,
      1,
      ['string'],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->addTaxonomy($bad_arg_val);
      });
    }  

    // Test ::removeTaxonomy with bad arguments
    $bad_args = [
      null,
      0,
      1,
      ['string'],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->removeTaxonomy($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::setPermalinkEpmask
   * @covers  Ponticlaro\Bebop\Cms\PostType::getPermalinkEpmask
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetPermalinkEpmask(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Test ::getPermalinkEpmask default value
    $I->assertNull($type->getPermalinkEpmask());

    // Test ::setPermalinkEpmask
    $type->setPermalinkEpmask('test_ep_mask');

    // Test ::getPermalinkEpmask updated value
    $I->assertEquals($type->getPermalinkEpmask(), 'test_ep_mask');

    // Test ::setPermalinkEpmask with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [0, 1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->setPermalinkEpmask($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::setRewrite
   * @covers  Ponticlaro\Bebop\Cms\PostType::setRewriteSlug
   * @covers  Ponticlaro\Bebop\Cms\PostType::setRewriteWithFront
   * @covers  Ponticlaro\Bebop\Cms\PostType::setRewriteFeeds
   * @covers  Ponticlaro\Bebop\Cms\PostType::setRewritePages
   * @covers  Ponticlaro\Bebop\Cms\PostType::setRewriteEpmask
   * @covers  Ponticlaro\Bebop\Cms\PostType::getRewrite
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function manageRewrite(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');
 
    // Get default taxonomies
    $def_rewrite = $this->prod_cfg['rewrite_config'];

    // Test ::getRewrite default value
    $I->assertEquals($type->getRewrite(), $def_rewrite);

    // Test ::setRewriteSlug
    $type->setRewriteSlug('product-slug1');

    // Change $def_rewrite to match expected value
    $def_rewrite['slug'] = 'product-slug1';

    // Test ::getRewrite updated value
    $I->assertEquals($type->getRewrite(), $def_rewrite); 

    // Test ::setRewriteWithFront
    $type->setRewriteWithFront(true);

    // Change $def_rewrite to match expected value
    $def_rewrite['with_front'] = true;

    // Test ::getRewrite updated value
    $I->assertEquals($type->getRewrite(), $def_rewrite); 

    // Test ::setRewriteFeeds
    $type->setRewriteFeeds(false);

    // Change $def_rewrite to match expected value
    $def_rewrite['feeds'] = false;

    // Test ::getRewrite updated value
    $I->assertEquals($type->getRewrite(), $def_rewrite); 

    // Test ::setRewritePages
    $type->setRewritePages(false);

    // Change $def_rewrite to match expected value
    $def_rewrite['pages'] = false;

    // Test ::getRewrite updated value
    $I->assertEquals($type->getRewrite(), $def_rewrite); 

    // Test ::setRewriteEpmask
    $type->setRewriteEpmask('test_ep_mask');

    // Change $def_rewrite to match expected value
    $def_rewrite['ep_mask'] = 'test_ep_mask';

    // Test ::getRewrite updated value
    $I->assertEquals($type->getRewrite(), $def_rewrite); 

    // Build modification array
    $rewrite_mods = [
      'slug'       => 'product-slug1-mod',
      'with_front' => false,
      'feeds'      => true,
      'pages'      => true,
      'ep_mask'    => 'test_ep_mask_mod'
    ];

    // Test ::setRewrite
    $type->setRewrite($rewrite_mods);

    // Test ::getRewrite updated value
    $I->assertEquals($type->getRewrite(), $rewrite_mods); 

    // Test ::setRewriteSlug with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [0, 1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->setRewriteSlug($bad_arg_val);
      });
    }    

    // Test ::setRewriteWithFront with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [0, 1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->setRewriteWithFront($bad_arg_val);
      });
    }   

    // Test ::setRewriteFeeds with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [0, 1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->setRewriteFeeds($bad_arg_val);
      });
    }   

    // Test ::setRewritePages with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [0, 1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->setRewritePages($bad_arg_val);
      });
    }   

    // Test ::setRewriteEpmask with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [0, 1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->setRewriteEpmask($bad_arg_val);
      });
    }  
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::setQueryVar
   * @covers  Ponticlaro\Bebop\Cms\PostType::getQueryVar
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetQueryVar(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Test ::getQueryVar default value
    $I->assertTrue($type->getQueryVar());

    // Test ::setQueryVar
    $type->setQueryVar('product_test');

    // Test ::getQueryVar updated value
    $I->assertEquals($type->getQueryVar(), 'product_test');

    // Test ::setQueryVar
    $type->setQueryVar(false);

    // Test ::getQueryVar updated value
    $I->assertFalse($type->getQueryVar());

    // Test ::setQueryVar with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [0, 1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($type, $bad_arg_val) {
        $type->setQueryVar($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::getFullConfig
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function getFullConfig(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Build expected config array
    $config                 = $this->prod_cfg['config'];
    $config['labels']       = $this->prod_cfg['labels'];
    $config['supports']     = $this->prod_cfg['features'];
    $config['capabilities'] = $this->prod_cfg['capabilities'];
    $config['taxonomies']   = $this->prod_cfg['taxonomies'];
    $config['rewrite']      = $this->prod_cfg['rewrite_config'];

    // Verify values match
    $I->assertEquals($config, $type->getFullConfig());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::applyRawArgs
   * @depends create
   * @depends getFullConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function applyRawArgs(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Build base test config array
    $config                 = $this->prod_cfg['config'];
    $config['labels']       = $this->prod_cfg['labels'];
    $config['supports']     = $this->prod_cfg['features'];
    $config['capabilities'] = $this->prod_cfg['capabilities'];
    $config['taxonomies']   = $this->prod_cfg['taxonomies'];
    $config['rewrite']      = $this->prod_cfg['rewrite_config'];

    // Build raw args number 1 to modify config
    $mod_config_1 = [
      'labels' => [
        'menu_name' => 'Products Mod1',
        'all_items' => 'Products Mod1',
      ],
      'supports' => [
        'title_mod1',
        'excerpt_mod1'
      ],
      'capabilities' => [
        'edit_post'   => 'edit_product_mod1',
        'read_post'   => 'read_product_mod1',
        'delete_post' => 'delete_product_mod1',
      ],
      'taxonomies' => [
        'dummy_taxonomy_1_mod1',
        'dummy_taxonomy_2_mod1',
        'dummy_taxonomy_3_mod1',
      ],
      'rewrite' => [
        'with_front' => true,
        'slug'       => 'products_mod1'
      ],
    ];

    // Build expected config for modification 1
    $expected_config                 = $config;
    $expected_config['labels']       = array_merge($config['labels'], $mod_config_1['labels']);
    $expected_config['supports']     = $mod_config_1['supports'];
    $expected_config['capabilities'] = $mod_config_1['capabilities'];
    $expected_config['taxonomies']   = $mod_config_1['taxonomies'];
    $expected_config['rewrite']      = $mod_config_1['rewrite'];

    // Apply first raw args modification
    $type->applyRawArgs($mod_config_1);

    // Verify configs match
    $I->assertEquals($expected_config, $type->getFullConfig());

    // Build raw args number 2 to modify config
    $mod_config_2 = [
      'labels' => [
        'menu_name' => 'Products Mod2',
        'all_items' => 'Products Mod2',
      ],
      'supports' => [
        'title_mod2',
        'excerpt_mod2'
      ],
      'capabilities' => [
        'edit_post'   => 'edit_product_mod2',
        'read_post'   => 'read_product_mod2',
        'delete_post' => 'delete_product_mod2',
      ],
      'taxonomies' => [
        'dummy_taxonomy_1_mod2',
        'dummy_taxonomy_2_mod2',
        'dummy_taxonomy_3_mod2',
      ],
      'rewrite' => [
        'with_front' => false,
        'slug'       => 'products-mod2'
      ],
    ];

    // Build expected config for modification 2
    $expected_config                 = $config;
    $expected_config['labels']       = array_merge($config['labels'], $mod_config_2['labels']);
    $expected_config['supports']     = $mod_config_2['supports'];
    $expected_config['capabilities'] = $mod_config_2['capabilities'];
    $expected_config['taxonomies']   = $mod_config_2['taxonomies'];
    $expected_config['rewrite']      = $mod_config_2['rewrite'];

    // Apply second raw args modification
    $type->applyRawArgs($mod_config_2);
     
    // Verify configs match
    $I->assertEquals($expected_config, $type->getFullConfig());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::__register
   * @depends create
   * @depends getFullConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function register(UnitTester $I)
  {
    // Mock register_post_type
    $mock = Test::func('Ponticlaro\Bebop\Cms', 'register_post_type', true);

    // Create test instance
    $type = new PostType('Product');

    // Call __register
    $type->__register();

    // Verify that register_post_type is called once with the correct args
    $mock->verifyInvokedOnce([
      $type->getId(),
      $type->getFullConfig()
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\PostType::__call
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function callUndefinedAliasMethod(UnitTester $I)
  {
    // Create test instance
    $type = new PostType('Product');

    // Check if exception is thrown with bad arguments
    $I->expectException(Exception::class, function() use($type) {
      $type->______testUndefinedMethod();
    });
  }
}
