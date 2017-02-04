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
