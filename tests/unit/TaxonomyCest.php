<?php

use AspectMock\Test;
use Ponticlaro\Bebop\Cms\Taxonomy;

class TaxonomyCest
{
  /**
   * Expected values for the default config of a 'Type' taxonomy
   * 
   * @var array
   */
  private $tax_cfg = [
    'config' => [
      'id'                => 'type',
      'hierarchical'      => true,
      'show_ui'           => true,
      'show_admin_column' => true,
      'query_var'         => true,
      'singular_name'     => 'Type',  // Added dynamically
      'plural_name'       => 'Types', // Added dynamically
      'label'             => 'Types', // Added dynamically
    ],
    'labels' => [
      'name'              => 'Types',
      'singular_name'     => 'Type',
      'search_items'      => 'Search Types',
      'all_items'         => 'All Types',
      'parent_item'       => 'Parent Type',
      'parent_item_colon' => 'Parent Type:',
      'edit_item'         => 'Edit Type',
      'update_item'       => 'Update Type',
      'add_new_item'      => 'Add New Type',
      'new_item_name'     => 'New Type Name',
      'menu_name'         => 'Types',
    ],
    'capabilities'   => [],
    'rewrite_config' => [],
    'post_types'     => [],
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
   * @covers Ponticlaro\Bebop\Cms\Taxonomy::__construct
   * @covers Ponticlaro\Bebop\Cms\Taxonomy::__setName
   * @covers Ponticlaro\Bebop\Cms\Taxonomy::__setSingularName
   * @covers Ponticlaro\Bebop\Cms\Taxonomy::__setPluralName
   * @covers Ponticlaro\Bebop\Cms\Taxonomy::__setDefaultLabels
   * 
   * @param UnitTester $I Tester Module
   */
  public function create(UnitTester $I)
  {  
    // Create test instance
    $tax = new Taxonomy('Type');

    // Verify add_action was called once
    $this->mocks['add_action']->verifyInvokedOnce(['init', [$tax, '__register']]);

    // Check $tax->config
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Taxonomy', 'config');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($tax);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->tax_cfg['config']);

    // Check $tax->labels
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Taxonomy', 'labels');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($tax);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->tax_cfg['labels']);

    // Check $tax->capabilities
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Taxonomy', 'capabilities');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($tax);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->tax_cfg['capabilities']);

    // Check $tax->rewrite_config
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Taxonomy', 'rewrite_config');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($tax);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->tax_cfg['rewrite_config']);

    // Check $tax->post_types
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Taxonomy', 'post_types');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($tax);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->tax_cfg['post_types']);

    // Test ::__setSingularName and ::__setPluralName with bad arguments
    $bad_args = [
      [null, null],
      [0, null],
      [1, null],
      [[null, null], null],
      [[0, 0], null],
      [[1, 1], null],
      [[new \stdClass, new \stdClass], null],
      [['Type', 0], null],
      [['Type', 1], null],
      [['Type', []], null],
      [['Type', new \stdClass], null],
      [['Type', 'Types'], 0],
      [['Type', 'Types'], 1],
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($tax, $bad_arg_val) {
        new Taxonomy($bad_arg_val[0], $bad_arg_val[1]);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::__construct
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::__setName
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::__setSingularName
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::__setPluralName
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::__setDefaultLabels
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function createWithIrregularPluralForm(UnitTester $I)
  {
    // Create test instance
    $tax = new Taxonomy(['Class', 'Classes']);

    // Check $tax->config
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Taxonomy', 'config');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($tax);

    $I->assertEquals($prop_val->getAll(), [
      'id'                => 'class',
      'hierarchical'      => true,
      'show_ui'           => true,
      'show_admin_column' => true,
      'query_var'         => true,
      'singular_name'     => 'Class',   // Added dynamically
      'plural_name'       => 'Classes', // Added dynamically
      'label'             => 'Classes', // Added dynamically
    ]);

    // Check $tax->labels
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Taxonomy', 'labels');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($tax);

    $I->assertEquals($prop_val->getAll(), [
      'name'              => 'Classes',
      'singular_name'     => 'Class',
      'search_items'      => 'Search Classes',
      'all_items'         => 'All Classes',
      'parent_item'       => 'Parent Class',
      'parent_item_colon' => 'Parent Class:',
      'edit_item'         => 'Edit Class',
      'update_item'       => 'Update Class',
      'add_new_item'      => 'Add New Class',
      'new_item_name'     => 'New Class Name',
      'menu_name'         => 'Classes',
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::__construct
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function createWithAssociatedTypes(UnitTester $I)
  { 
    // Create reflection of PostType class
    $refl_type = new \ReflectionClass('Ponticlaro\Bebop\Cms\PostType');

    // Build tests
    $tests = [
      [
        'args'     => 'Type1',
        'expected' => ['type1']
      ],
      [
        'args'     => ['Type1', 'Type2'],
        'expected' => ['type1', 'type2']
      ],
      [
        'args'     => $refl_type->newInstance('Type1'),
        'expected' => ['type1']
      ],
      [
        'args'     => [
          $refl_type->newInstance('Type1'),
          $refl_type->newInstance('Type2'),
        ],
        'expected' => ['type1', 'type2']
      ],
      [
        'args'     => [
          'Type1',
          $refl_type->newInstance('Type2'),
        ],
        'expected' => ['type1', 'type2']
      ],
    ];

    // Run tests
    foreach ($tests as $test) {

      // Create test instance
      $tax = new Taxonomy('Type', $test['args']);

      // Get $tax->post_types
      $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Taxonomy', 'post_types');
      $prop->setAccessible(true);
      $prop_val = $prop->getValue($tax);

      $I->assertEquals($prop_val->getAll(), $test['expected']);
    } 
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::getObjectId
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function getObjectId(UnitTester $I)
  {
    // Create test instance
    $tax = new Taxonomy('Type');

    // Test ::getObjectId
    $I->assertEquals($this->tax_cfg['config']['id'], $tax->getObjectId());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::getObjectType
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function getObjectType(UnitTester $I)
  {
    // Create test instance
    $tax = new Taxonomy('Type');

    // Test ::getObjectType
    $I->assertEquals('taxonomy', $tax->getObjectType());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::setId
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::getId
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetId(UnitTester $I)
  {
    // Create test instance
    $tax = new Taxonomy('Type');

    // Test ::getId default value
    $I->assertEquals($this->tax_cfg['config']['id'], $tax->getId());

    // Test ::setId
    $tax->setId('test_id');
    $I->assertEquals('test_id', $tax->getId());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::setLabels
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::setLabel
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::getLabels
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::getLabel
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetLabels(UnitTester $I)
  {
    // Create test instance
    $tax = new Taxonomy('Type');

    // Test ::getLabels default value
    $I->assertEquals($this->tax_cfg['labels'], $tax->getLabels());

    // Test ::getLabel
    foreach ($this->tax_cfg['labels'] as $key => $value) {
      $I->assertEquals($value, $tax->getLabel($key));
    }

    // Test ::setLabels
    $updated_labels = [];

    foreach ($this->tax_cfg['labels'] as $key => $value) {
      $updated_labels[$key] = 'Test 1 '. $value;
    }

    $tax->setLabels($updated_labels);
    $I->assertEquals($updated_labels, $tax->getLabels());
    
    // Test ::setLabel
    $updated_labels = [];

    foreach ($this->tax_cfg['labels'] as $key => $value) {

      $updated_labels[$key] = 'Test 2 '. $value;
      $tax->setLabel($key, $updated_labels[$key]);
    }

    $I->assertEquals($updated_labels, $tax->getLabels());

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
      $I->expectException(Exception::class, function() use($tax, $bad_arg_val) {
        $tax->setLabel(reset($this->tax_cfg['labels']), $bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::getCapabilities
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::setCapabilities
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::addCapability
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::replaceCapabilities
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::removeCapabilities
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::removeCapability
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function manageCapabilities(UnitTester $I)
  {
    // Create test instance
    $tax = new Taxonomy('Type');

    // Test ::getCapabilities default value
    $I->assertEmpty($tax->getCapabilities());

    // Test ::addCapability
    $tax->addCapability('manage_terms', 'manage_categories_init');

     // Test ::getCapabilities updated value
    $I->assertEquals($tax->getCapabilities(), [
      'manage_terms' => 'manage_categories_init'
    ]);

    // Capabilities to set
    $caps = [
      'manage_terms' => 'manage_categories',
      'edit_terms'   => 'manage_categories',
      'delete_terms' => 'manage_categories',
      'assign_terms' => 'edit_posts',
    ];

    // Test ::setCapabilities
    $tax->setCapabilities($caps);

    // Test ::getCapabilities updated value
    $I->assertEquals($tax->getCapabilities(), $caps);

    // Capabilities to replace with
    $replace_caps = [
      'manage_terms' => 'manage_categories_mod1',
      'edit_terms'   => 'manage_categories_mod1',
      'delete_terms' => 'manage_categories_mod1',
      'assign_terms' => 'edit_posts_mod1',
    ];

    // Test ::replaceCapabilities
    $tax->replaceCapabilities($replace_caps);

    // Test ::getCapabilities updated value
    $I->assertEquals($tax->getCapabilities(), $replace_caps);

    // Test ::removeCapability
    $tax->removeCapability('manage_terms');

    // Update $replace_caps to match expected value
    unset($replace_caps['manage_terms']);

    // Test ::getCapabilities updated value
    $I->assertEquals($tax->getCapabilities(), $replace_caps);

    // Test ::removeCapabilities
    $tax->removeCapabilities([
      'edit_terms',
      'delete_terms'
    ]);

    // Update $replace_caps to match expected value
    unset($replace_caps['edit_terms']);
    unset($replace_caps['delete_terms']);

    // Test ::getCapabilities updated value
    $I->assertEquals($tax->getCapabilities(), $replace_caps);

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
      $I->expectException(Exception::class, function() use($tax, $bad_arg_val) {
        $tax->addCapability($bad_arg_val[0], $bad_arg_val[1]);
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
      $I->expectException(Exception::class, function() use($tax, $bad_arg_val) {
        $tax->removeCapability($bad_arg_val);
      });
    }    
  }


  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::getPostTypes
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::addPostTypes
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::addPostType
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::setPostTypes
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::removePostTypes
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::removePostType
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function managePostTypes(UnitTester $I)
  {
    // Create reflection of PostType class
    $refl_type = new \ReflectionClass('Ponticlaro\Bebop\Cms\PostType');

    // Create test instance
    $tax = new Taxonomy('Type');

    // Test ::getPostTypes default value
    $I->assertEmpty($tax->getPostTypes());

    // Test ::addPostType
    $tax->addPostType('Type1');
    $tax->addPostType($refl_type->newInstance('Type2'));

     // Test ::getPostTypes updated value
    $I->assertEquals($tax->getPostTypes(), [
      'type1',
      'type2',
    ]);

    // PostTypes to set
    $types = [
      'type3',
      $refl_type->newInstance('Type4'),
    ];

    // Test ::addPostTypes
    $tax->addPostTypes($types);

    // Test ::getPostTypes updated value
    $I->assertEquals($tax->getPostTypes(), [
      'type1',
      'type2',  
      'type3',
      'type4',
    ]);

    // PostTypes to replace with
    $replace_types = [
      'type5',
      $refl_type->newInstance('Type6'),
      'type7',
      'type8',
      'type9',
    ];

    // Test ::setPostTypes
    $tax->setPostTypes($replace_types);

    // Test ::getPostTypes updated value
    $I->assertEquals($tax->getPostTypes(), [
      'type5',
      'type6',
      'type7',
      'type8',
      'type9',
    ]);

    // Test ::removePostType
    $tax->removePostType('type5');
    $tax->removePostType($refl_type->newInstance('Type6'));

    // Test ::getPostTypes updated value
    $types = $tax->getPostTypes();
    sort($types);

    $I->assertEquals($types, [
      'type7',
      'type8',
      'type9',
    ]);

    // Test ::removePostTypes
    $tax->removePostTypes([
      'type7',
      $refl_type->newInstance('Type8'),
    ]);

    // Test ::getPostTypes updated value
    $types = $tax->getPostTypes();
    sort($types); 
    
    $I->assertEquals($types, [
      'type9',
    ]);

    // Test ::addPostType with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [0, 1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($tax, $bad_arg_val) {
        $tax->addPostType($bad_arg_val);
      });
    }  

    // Test ::removePostType with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [0, 1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($tax, $bad_arg_val) {
        $tax->removePostType($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::setRewrite
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::setRewriteSlug
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::setRewriteWithFront
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::setRewriteHierarchical
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::setRewriteEpmask
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::getRewrite
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function manageRewrite(UnitTester $I)
  {
    // Create test instance
    $tax = new Taxonomy('Type');
 
    // Get default taxonomies
    $def_rewrite = $this->tax_cfg['rewrite_config'];

    // Test ::getRewrite default value
    $I->assertEquals($tax->getRewrite(), $def_rewrite);

    // Test ::setRewriteSlug
    $tax->setRewriteSlug('type-slug1');

    // Change $def_rewrite to match expected value
    $def_rewrite['slug'] = 'type-slug1';

    // Test ::getRewrite updated value
    $I->assertEquals($tax->getRewrite(), $def_rewrite); 

    // Test ::setRewriteWithFront
    $tax->setRewriteWithFront(true);

    // Change $def_rewrite to match expected value
    $def_rewrite['with_front'] = true;

    // Test ::getRewrite updated value
    $I->assertEquals($tax->getRewrite(), $def_rewrite); 

    // Test ::setRewriteHierarchical
    $tax->setRewriteHierarchical(false);

    // Change $def_rewrite to match expected value
    $def_rewrite['hierarchical'] = false;

    // Test ::getRewrite updated value
    $I->assertEquals($tax->getRewrite(), $def_rewrite); 

    // Test ::setRewriteEpmask
    $tax->setRewriteEpmask('test_ep_mask');

    // Change $def_rewrite to match expected value
    $def_rewrite['ep_mask'] = 'test_ep_mask';

    // Test ::getRewrite updated value
    $I->assertEquals($tax->getRewrite(), $def_rewrite); 

    // Build modification array
    $rewrite_mods = [
      'slug'         => 'type-slug1-mod',
      'with_front'   => false,
      'hierarchical' => true,
      'ep_mask'      => 'test_ep_mask_mod'
    ];

    // Test ::setRewrite
    $tax->setRewrite($rewrite_mods);

    // Test ::getRewrite updated value
    $I->assertEquals($tax->getRewrite(), $rewrite_mods); 

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
      $I->expectException(Exception::class, function() use($tax, $bad_arg_val) {
        $tax->setRewriteSlug($bad_arg_val);
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
      $I->expectException(Exception::class, function() use($tax, $bad_arg_val) {
        $tax->setRewriteWithFront($bad_arg_val);
      });
    }   

    // Test ::setRewriteHierarchical with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [0, 1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($tax, $bad_arg_val) {
        $tax->setRewriteHierarchical($bad_arg_val);
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
      $I->expectException(Exception::class, function() use($tax, $bad_arg_val) {
        $tax->setRewriteEpmask($bad_arg_val);
      });
    }  
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::makePublic
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::isPublic
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::__call
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetPublic(UnitTester $I)
  {
    // Create test instance
    $tax = new Taxonomy('Type');

    // Test ::isPublic default value
    $I->assertNull($tax->isPublic());

    // Test ::makePublic
    $tax->makePublic(false);

    // Test ::isPublic updated value
    $I->assertFalse($tax->isPublic());

    // Test ::setPublic alias method
    $tax->setPublic(true);

    // Test ::isPublic updated value
    $I->assertTrue($tax->isPublic());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::setHierarchical
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::isHierarchical
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetHierarchical(UnitTester $I)
  {
    // Create test instance
    $tax = new Taxonomy('Type');

    // Test ::isHierarchical default value
    $I->assertTrue($tax->isHierarchical());

    // Test ::setHierarchical
    $tax->setHierarchical(false);

    // Test ::isHierarchical updated value
    $I->assertFalse($tax->isHierarchical());

    // Test ::setHierarchical alias method
    $tax->setHierarchical(true);

   // Test ::isHierarchical updated value
    $I->assertTrue($tax->isHierarchical());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::setQueryVar
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::getQueryVar
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetQueryVar(UnitTester $I)
  {
    // Create test instance
    $tax = new Taxonomy('Type');

    // Test ::getQueryVar default value
    $I->assertTrue($tax->getQueryVar());

    // Test ::setQueryVar
    $tax->setQueryVar('type_test');

    // Test ::getQueryVar updated value
    $I->assertEquals($tax->getQueryVar(), 'type_test');

    // Test ::setQueryVar
    $tax->setQueryVar(false);

    // Test ::getQueryVar updated value
    $I->assertFalse($tax->getQueryVar());

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
      $I->expectException(Exception::class, function() use($tax, $bad_arg_val) {
        $tax->setQueryVar($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::showUi
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setShowUI(UnitTester $I)
  {
    // Create test instance
    $tax = new Taxonomy('Type');

    // Get $tax->config
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Taxonomy', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($tax);

    // Test show_ui default value
    $I->assertTrue($config->get('show_ui'));

    // Test ::setShowUI
    $tax->showUi(false);

    // Test show_ui updated value
    $I->assertFalse($config->get('show_ui'));

    // Test ::setShowUI alias method
    $tax->showUi(true);

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
      $I->expectException(Exception::class, function() use($tax, $bad_arg_val) {
        $tax->showUi($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::showInNavMenus
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::__call
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setShowInNavMenus(UnitTester $I)
  {
    // Create test instance
    $tax = new Taxonomy('Type');

    // Get $tax->config
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Taxonomy', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($tax);

    // Test show_in_nav_menus default value
    $I->assertNull($config->get('show_in_nav_menus'));

    // Test ::showInNavMenus
    $tax->showInNavMenus(true);

    // Test show_in_nav_menus updated value
    $I->assertTrue($config->get('show_in_nav_menus'));

    // Test ::setShowInNavMenus alias method
    $tax->setShowInNavMenus(false);

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
      $I->expectException(Exception::class, function() use($tax, $bad_arg_val) {
        $tax->showInNavMenus($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::showTagcloud
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::__call
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setShowTagCloud(UnitTester $I)
  {
    // Create test instance
    $tax = new Taxonomy('Type');

    // Get $tax->config
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Taxonomy', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($tax);

    // Test show_tagcloud default value
    $I->assertNull($config->get('show_tagcloud'));

    // Test ::showTagcloud
    $tax->showTagcloud(true);

    // Test show_tagcloud updated value
    $I->assertTrue($config->get('show_tagcloud'));

    // Test ::setShowTagcloud alias method
    $tax->setShowTagcloud(false);

    // Test show_tagcloud updated value
    $I->assertFalse($config->get('show_tagcloud'));

    // Test ::showTagcloud with bad arguments
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
      $I->expectException(Exception::class, function() use($tax, $bad_arg_val) {
        $tax->showTagcloud($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::showAdminColumn
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::__call
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setShowAdminColumn(UnitTester $I)
  {
    // Create test instance
    $tax = new Taxonomy('Type');

    // Get $tax->config
    $config_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Taxonomy', 'config');
    $config_prop->setAccessible(true);
    $config = $config_prop->getValue($tax);

    // Test show_admin_column default value
    $I->assertTrue($config->get('show_admin_column'));

    // Test ::showAdminColumn
    $tax->showAdminColumn(false);

    // Test show_admin_column updated value
    $I->assertFalse($config->get('show_admin_column'));

    // Test ::setShowTagcloud alias method
    $tax->setShowAdminColumn(true);

    // Test show_admin_column updated value
    $I->assertTrue($config->get('show_admin_column'));

    // Test ::showAdminColumn with bad arguments
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
      $I->expectException(Exception::class, function() use($tax, $bad_arg_val) {
        $tax->showAdminColumn($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::setMetaboxCallback
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::getMetaboxCallback
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::__call
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetMetaboxCallback(UnitTester $I)
  {
    // Create test instance
    $tax = new Taxonomy('Type');

    // Test ::getMetaboxCallback default value
    $I->assertNull($tax->getMetaboxCallback());

    // Test ::setMetaboxCallback
    $tax->setMetaboxCallback('is_string');

    // Test ::getMetaboxCallback updated value
    $I->assertEquals($tax->getMetaboxCallback(), 'is_string');

    // Test ::setMetaboxCallback
    $tax->setMetaboxCallback(null);

    // Test ::getMetaboxCallback updated value
    $I->assertNull($tax->getMetaboxCallback());

    // Test ::setMetaBoxCb alias method
    $tax->setMetaBoxCb('is_string');

    // Test ::getMetaboxCallback updated value
    $I->assertEquals($tax->getMetaboxCallback(), 'is_string');

    // Test ::setMetaboxCallback with bad arguments
    $bad_args = [
      true,
      false,
      0,
      1,
      [0, 1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($tax, $bad_arg_val) {
        $tax->setMetaboxCallback($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::setUpdateCountCallback
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::getUpdateCountCallback
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetUpdateCountCallback(UnitTester $I)
  {
    // Create test instance
    $tax = new Taxonomy('Type');

    // Test ::getUpdateCountCallback default value
    $I->assertNull($tax->getUpdateCountCallback());

    // Test ::setUpdateCountCallback
    $tax->setUpdateCountCallback('is_string');

    // Test ::getUpdateCountCallback updated value
    $I->assertEquals($tax->getUpdateCountCallback(), 'is_string');

    // Test ::setUpdateCountCallback
    $tax->setUpdateCountCallback(null);

    // Test ::getUpdateCountCallback updated value
    $I->assertNull($tax->getUpdateCountCallback());

    // Test ::setUpdateCountCallback with bad arguments
    $bad_args = [
      true,
      false,
      0,
      1,
      [0, 1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($tax, $bad_arg_val) {
        $tax->setUpdateCountCallback($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::setSort
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::getSort
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetSort(UnitTester $I)
  {
    // Create test instance
    $tax = new Taxonomy('Type');

    // Test ::getSort default value
    $I->assertNull($tax->getSort());

    // Test ::getSort
    $tax->setSort(true);

    // Test ::getSort updated value
    $I->assertTrue($tax->getSort());

    // Test ::setSort
    $tax->setSort(false);

    // Test ::getSort updated value
    $I->assertFalse($tax->getSort());

    // Test ::setSort with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [0, 1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($tax, $bad_arg_val) {
        $tax->setSort($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::getFullConfig
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function getFullConfig(UnitTester $I)
  {
    // Create test instance
    $tax = new Taxonomy('Type');

    // Build expected config array
    $config                 = $this->tax_cfg['config'];
    $config['labels']       = $this->tax_cfg['labels'];
    $config['capabilities'] = $this->tax_cfg['capabilities'];
    $config['rewrite']      = $this->tax_cfg['rewrite_config'];

    // Verify values match
    $I->assertEquals($config, $tax->getFullConfig());
  }


  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::applyRawArgs
   * @depends create
   * @depends managePostTypes
   * @depends getFullConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function applyRawArgs(UnitTester $I)
  {
    // Create reflection of PostType class
    $refl_type = new \ReflectionClass('Ponticlaro\Bebop\Cms\PostType');

    // Create test instance
    $tax = new Taxonomy('Type');

    // Build base test config array
    $config                 = $this->tax_cfg['config'];
    $config['labels']       = $this->tax_cfg['labels'];
    $config['capabilities'] = $this->tax_cfg['capabilities'];
    $config['rewrite']      = $this->tax_cfg['rewrite_config'];

    // Build raw args number 1 to modify config
    $mod_config_1 = [
      'labels' => [
        'menu_name' => 'Types Mod1',
        'all_items' => 'All Types Mod1',
      ],
      'capabilities' => [
        'manage_terms' => 'manage_categories_mod1',
        'edit_terms'   => 'manage_categories_mod1',
        'delete_terms' => 'manage_categories_mod1',
        'assign_terms' => 'edit_posts_mod1',
      ],
      'rewrite' => [
        'with_front' => true,
        'slug'       => 'type_mod1'
      ],
      'post_types' => [
        'type1',
        $refl_type->newInstance('Type2'),
        'type3',
        $refl_type->newInstance('Type4'),
      ]
    ];

    // Build expected config for modification 1
    $expected_config                 = $config;
    $expected_config['labels']       = array_merge($config['labels'], $mod_config_1['labels']);
    $expected_config['capabilities'] = $mod_config_1['capabilities'];
    $expected_config['rewrite']      = $mod_config_1['rewrite'];

    // Apply first raw args modification
    $tax->applyRawArgs($mod_config_1);

    // Verify configs match
    $I->assertEquals($tax->getPostTypes(), [
      'type1',
      'type2',
      'type3',
      'type4'
    ]);

    $I->assertEquals($tax->getFullConfig(), $expected_config);

    // Build raw args number 2 to modify config
    $mod_config_2 = [
      'labels' => [
        'menu_name' => 'Types Mod2',
        'all_items' => 'All Types Mod2',
      ],
      'capabilities' => [
        'manage_terms' => 'manage_categories_mod2',
        'edit_terms'   => 'manage_categories_mod2',
        'delete_terms' => 'manage_categories_mod2',
        'assign_terms' => 'edit_posts_mod2',
      ],
      'rewrite' => [
        'with_front' => false,
        'slug'       => 'type_mod2'
      ],
      'post_types' => [
        'type5',
        $refl_type->newInstance('Type6'),
        'type7',
        $refl_type->newInstance('Type8'),
      ]
    ];

    // Build expected config for modification 2
    $expected_config                 = $config;
    $expected_config['labels']       = array_merge($config['labels'], $mod_config_2['labels']);
    $expected_config['capabilities'] = $mod_config_2['capabilities'];
    $expected_config['rewrite']      = $mod_config_2['rewrite'];

    // Apply second raw args modification
    $tax->applyRawArgs($mod_config_2);
     
    // Verify configs match
    $I->assertEquals($tax->getPostTypes(), [
      'type5',
      'type6',
      'type7',
      'type8'
    ]);

    $I->assertEquals($tax->getFullConfig(), $expected_config);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::__register
   * @depends create
   * @depends getFullConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function register(UnitTester $I)
  {
    // Mock register_taxonomy
    $mock = Test::func('Ponticlaro\Bebop\Cms', 'register_taxonomy', true);

    // Create test instance
    $tax = new Taxonomy('Type');

    // Call __register
    $tax->__register();

    // Verify that register_taxonomy is called once with the correct args
    $mock->verifyInvokedOnce([
      $tax->getId(),
      $tax->getPostTypes(),
      $tax->getFullConfig()
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Taxonomy::__call
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function callUndefinedAliasMethod(UnitTester $I)
  {
    // Create test instance
    $tax = new Taxonomy('Type');

    // Check if exception is thrown with bad arguments
    $I->expectException(Exception::class, function() use($tax) {
      $tax->______testUndefinedMethod();
    });
  }
}
