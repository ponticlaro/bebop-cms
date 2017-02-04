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

  public function _before(UnitTester $I)
  {
    // Mock Utils
    // - ::slugify
    Test::double('Ponticlaro\Bebop\Common\Utils', [
      'slugify' => function() {
        return strtolower(func_get_arg(0));
      }
    ]);

    \WP_Mock::setUp();
  }

  public function _after(UnitTester $I)
  {
    Test::clean();
    \WP_Mock::tearDown();
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
    // Making sure PostType will be registered on the WordPress 'init' hook
    //\WP_Mock::expectActionAdded('init', []);

    // Create test instance
    $type = new PostType('Product');

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
      [],
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
   * 
   * @param UnitTester $I Tester Module
   */
  public function createWithIrregularPluralForm(UnitTester $I)
  {
    // Making sure PostType will be registered on the WordPress 'init' hook
    //\WP_Mock::expectActionAdded('init', []);

    // Create test instance
    $type = new PostType(['Gallery', 'Galleries']);

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
   * 
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function makePublic(UnitTester $I)
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
