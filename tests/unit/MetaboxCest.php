<?php

use AspectMock\Test;
use Ponticlaro\Bebop\Cms\Metabox;
use Ponticlaro\Bebop\Cms\Helpers\MetaboxData;

class MetaboxCest
{
  /**
   * Expected values for the default config of a 'Title' type
   * 
   * @var array
   */
  private $expected_cfg = [
    'config' => [
      'context'       => 'normal',
      'priority'      => 'default',
      'callback_args' => array(),
      'id'            => 'title',                   // Added dynamically
      'title'         => 'Title',                   // Added dynamically
      'callback'      => 'sample_control_elements', // Added dynamically
    ],
    'post_types'   => [
      'type1'
    ],
    'meta_fields'  => [],
    'sections'     => [],
    'data'         => []
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

    // Mock get_post_meta function
    $this->mocks['get_post_meta'] = Test::func('Ponticlaro\Bebop\Cms', 'get_post_meta', function() {
      return func_get_arg(1) .'_value';
    });

    // Mock wp_nonce_field function
    $this->mocks['wp_nonce_field'] = Test::func('Ponticlaro\Bebop\Cms', 'wp_nonce_field', true);
  }

  public function _after(UnitTester $I)
  {
    Test::clean();
    Mockery::close();
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Metabox::__construct
   * 
   * @param UnitTester $I Tester Module
   */
  public function create(UnitTester $I)
  {
    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Verify add_action was invoked
    $this->mocks['add_action']->verifyInvokedOnce(['save_post', [$metabox, '__saveMeta']]);
    $this->mocks['add_action']->verifyInvokedOnce(['add_meta_boxes', [$metabox, '__register']]);
  
    // Check $type->config
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Metabox', 'config');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($metabox);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->expected_cfg['config']);

    // Check $type->post_types
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Metabox', 'post_types');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($metabox);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->expected_cfg['post_types']);

    // Check $type->meta_fields
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Metabox', 'meta_fields');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($metabox);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->expected_cfg['meta_fields']);

    // Check $type->sections
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Metabox', 'sections');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($metabox);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->expected_cfg['sections']);

    // Check $type->data
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Metabox', 'data');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($metabox);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Cms\Helpers\MetaboxData);
    $I->assertEquals($prop_val->getAll(), $this->expected_cfg['data']);
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Metabox::__construct
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function createWithPostTypeList(UnitTester $I)
  {
    // Create reflection of PostType class
    $refl_type = new \ReflectionClass('Ponticlaro\Bebop\Cms\PostType');

    // Create test instance
    $metabox = new Metabox('Title', [
      'type1',
      $refl_type->newInstance('Type2')
    ], 'sample_control_elements');

    // Check $type->post_types
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Metabox', 'post_types');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($metabox);

    $I->assertEquals($prop_val->getAll(), [
      'type1',
      'type2'
    ]);
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Metabox::__construct
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function createWithTitleAndConfigArray(UnitTester $I)
  {
    // Mock register_taxonomy
    $mock = Test::double('Ponticlaro\Bebop\Cms\Metabox', [
      'applyArgs' => null
    ]);

    $args = [
      'post_types' => 'type1',
      'fn'         => 'sample_control_elements'
    ];

    // Create test instance
    $metabox = new Metabox('Title', $args);

    // Check is ::applyArgs was called
    $mock->verifyInvokedOnce('applyArgs', [$args]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::getObjectId
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function getObjectId(UnitTester $I)
  {
    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Test ::getObjectId
    $I->assertEquals($this->expected_cfg['config']['id'], $metabox->getObjectId());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::getObjectType
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function getObjectType(UnitTester $I)
  {
    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Test ::getObjectType
    $I->assertEquals('metabox', $metabox->getObjectType());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::setId
   * @covers  Ponticlaro\Bebop\Cms\Metabox::getId
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetId(UnitTester $I)
  {
    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Test ::getId default value
    $I->assertEquals($this->expected_cfg['config']['id'], $metabox->getId());

    // Test ::setId
    $metabox->setId('test_id');

    // Test ::getId updated value
    $I->assertEquals('test_id', $metabox->getId());

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
      $I->expectException(Exception::class, function() use($metabox, $bad_arg_val) {
        $metabox->setId($bad_arg_val);
      });
    }   
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::setTitle
   * @covers  Ponticlaro\Bebop\Cms\Metabox::getTitle
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetTitle(UnitTester $I)
  {
    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Test ::getTitle default value
    $I->assertEquals($this->expected_cfg['config']['title'], $metabox->getTitle());

    // Test ::setTitle
    $metabox->setTitle('test_title');

    // Test ::getTitle updated value
    $I->assertEquals('test_title', $metabox->getTitle());

    // Test ::setTitle with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($metabox, $bad_arg_val) {
        $metabox->setTitle($bad_arg_val);
      });
    }  
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::setCallback
   * @covers  Ponticlaro\Bebop\Cms\Metabox::getCallback
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetCallback(UnitTester $I)
  {
    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Test ::getCallback default value
    $I->assertEquals($this->expected_cfg['config']['callback'], $metabox->getCallback());

    // Test ::setCallback
    $metabox->setCallback('is_string');

    // Test ::getCallback updated value
    $I->assertEquals('is_string', $metabox->getCallback());

    // Test ::setCallback with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($metabox, $bad_arg_val) {
        $metabox->setCallback($bad_arg_val);
      });
    }   
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::setContext
   * @covers  Ponticlaro\Bebop\Cms\Metabox::getContext
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetContext(UnitTester $I)
  {
    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Test ::getContext default value
    $I->assertEquals($this->expected_cfg['config']['context'], $metabox->getContext());

    // Test ::setContext
    $metabox->setContext('test_context');

    // Test ::getContext updated value
    $I->assertEquals('test_context', $metabox->getContext());

    // Test ::setContext with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($metabox, $bad_arg_val) {
        $metabox->setContext($bad_arg_val);
      });
    }  
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::setPriority
   * @covers  Ponticlaro\Bebop\Cms\Metabox::getPriority
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetPriority(UnitTester $I)
  {
    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Test ::getPriority default value
    $I->assertEquals($this->expected_cfg['config']['priority'], $metabox->getPriority());

    // Test ::setPriority
    $metabox->setPriority('test_priority');

    // Test ::getPriority updated value
    $I->assertEquals('test_priority', $metabox->getPriority());

    // Test ::setPriority with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($metabox, $bad_arg_val) {
        $metabox->setPriority($bad_arg_val);
      });
    }  
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::setCallbackArgs
   * @covers  Ponticlaro\Bebop\Cms\Metabox::getCallbackArgs
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetCallbackArgs(UnitTester $I)
  {
    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Test ::getCallbackArgs default value
    $I->assertEquals($this->expected_cfg['config']['callback_args'], $metabox->getCallbackArgs());

    $args = ['arg1','arg2'];

    // Test ::setCallbackArgs
    $metabox->setCallbackArgs($args);

    // Test ::getCallbackArgs updated value
    $I->assertEquals($args, $metabox->getCallbackArgs());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::setMetaFields
   * @covers  Ponticlaro\Bebop\Cms\Metabox::addMetaField
   * @covers  Ponticlaro\Bebop\Cms\Metabox::removeMetaField
   * @covers  Ponticlaro\Bebop\Cms\Metabox::getMetaFields
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function manageMetaFields(UnitTester $I)
  {
    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Test ::getMetaFields default value
    $I->assertEquals($this->expected_cfg['meta_fields'], $metabox->getMetaFields());

    // Test ::addMetaField
    $metabox->addMetaField('test_meta_field_1');

    // Test ::getMetaFields updated value
    $I->assertEquals($metabox->getMetaFields(), [
      'test_meta_field_1'
    ]);

    // Test ::setMetaFields
    $metabox->setMetaFields([
      'test_meta_field_2',
      'test_meta_field_3',
    ]);

    // Test ::getMetaFields updated value
    $I->assertEquals($metabox->getMetaFields(), [
      'test_meta_field_1',
      'test_meta_field_2',
      'test_meta_field_3',
    ]);

    // Test ::removeMetaField
    $metabox->removeMetaField('test_meta_field_1');

    // Test ::getMetaFields updated value
    $meta_fields = $metabox->getMetaFields();
    sort($meta_fields); 
    
    $I->assertEquals($meta_fields, [
      'test_meta_field_2',
      'test_meta_field_3',
    ]);

    // Test ::addMetaField and ::removeMetaField with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($metabox, $bad_arg_val) {
        $metabox->addMetaField($bad_arg_val);
      });
    }  

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($metabox, $bad_arg_val) {
        $metabox->removeMetaField($bad_arg_val);
      });
    }  
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::addSection
   * @covers  Ponticlaro\Bebop\Cms\Metabox::getAllSections
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function manageSections(UnitTester $I)
  {
    // Mock bebop-ui ModuleFactory
    $mock = Test::double('Ponticlaro\Bebop\UI\Helpers\ModuleFactory', [
      'canManufacture' => true,
      'create'         => function() {
        return func_get_arg(0);
      }
    ]);

    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Test ::getAllSections default value
    $I->assertEquals($this->expected_cfg['sections'], $metabox->getAllSections());

    // Test ::addSection
    $metabox->addSection('section_dummy_1', []);
    $metabox->addSection('section_dummy_2', []);

    // Test ::getAllSections updated value
    $I->assertEquals($metabox->getAllSections(), [
      'section_dummy_1',
      'section_dummy_2',
    ]);

    // Clean test for new mocks
    Test::clean();

    // Mock bebop-ui ModuleFactory
    $mock = Test::double('Ponticlaro\Bebop\UI\Helpers\ModuleFactory', [
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
      $I->expectException(Exception::class, function() use($metabox, $bad_arg_val) {
        $metabox->addSection($bad_arg_val, []);
      });
    }  
  }


  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::getPostTypes
   * @covers  Ponticlaro\Bebop\Cms\Metabox::addPostType
   * @covers  Ponticlaro\Bebop\Cms\Metabox::setPostTypes
   * @covers  Ponticlaro\Bebop\Cms\Metabox::removePostType
   * @covers  Ponticlaro\Bebop\Cms\Metabox::clearPostTypes
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function managePostTypes(UnitTester $I)
  {
    // Create reflection of PostType class
    $refl_type = new \ReflectionClass('Ponticlaro\Bebop\Cms\PostType');

    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Test ::getPostTypes default value
    $I->assertEquals($this->expected_cfg['post_types'], $metabox->getPostTypes());

    // Test ::addPostType
    $metabox->addPostType('Type2');
    $metabox->addPostType($refl_type->newInstance('Type3'));

     // Test ::getPostTypes updated value
    $I->assertEquals($metabox->getPostTypes(), [
      'type1',
      'type2',
      'type3',
    ]);

    // PostTypes to replace with
    $replace_types = [
      'type4',
      $refl_type->newInstance('Type5'),
    ];

    // Test ::setPostTypes
    $metabox->setPostTypes($replace_types);

    // Test ::getPostTypes updated value
    $I->assertEquals($metabox->getPostTypes(), [
      'type1',
      'type2',
      'type3',
      'type4',
      'type5',
    ]);

    // Test ::removePostType
    $metabox->removePostType('type4');
    $metabox->removePostType($refl_type->newInstance('Type5'));

    // Test ::getPostTypes updated value
    $types = $metabox->getPostTypes();
    sort($types);

    $I->assertEquals($types, [
      'type1',
      'type2',
      'type3',
    ]);

    // Test ::clearPostTypes
    $metabox->clearPostTypes();

    // Test ::getPostTypes updated value
    $I->assertEmpty($metabox->getPostTypes());

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
      $I->expectException(Exception::class, function() use($metabox, $bad_arg_val) {
        $metabox->addPostType($bad_arg_val);
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
      $I->expectException(Exception::class, function() use($metabox, $bad_arg_val) {
        $metabox->removePostType($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::__call
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function callMagicMethod(UnitTester $I)
  {
    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Mock bebop-ui ModuleFactory
    $mock = Test::double('Ponticlaro\Bebop\UI\Helpers\ModuleFactory', [
      'canManufacture' => true,
      'create'         => function() {
        return func_get_arg(0);
      }
    ]);

    // Test ::__call
    $metabox->input([]);

    // Verify bebop-ui ModuleFactory methods were called
    $mock->verifyInvokedOnce('canManufacture', ['input']);
    $mock->verifyInvokedOnce('create', ['input', []]);

    // Clean test for more mocks
    Test::clean();

    // Mock bebop-ui ModuleFactory
    $mock = Test::double('Ponticlaro\Bebop\UI\Helpers\ModuleFactory', [
      'canManufacture' => false
    ]);

    // Check if exception is thrown with bad arguments
    $I->expectException(Exception::class, function() use($metabox) {
      $metabox->______testUndefinedUISection();
    });

    $mock->verifyInvokedOnce('canManufacture', ['______testUndefinedUISection']);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::__collectSectionsFieldNames
   * @depends create
   * @depends manageSections 
   * 
   * @param UnitTester $I Tester Module
   */
  public function collectSectionsFieldNames(UnitTester $I)
  {
    // Mock bebop-ui ModuleAbstract
    $ui_module_mock = Test::double('Ponticlaro\Bebop\UI\Modules\Input', [
      '__construct'        => null,
      'renderMainTemplate' => true
    ]);
    
    // Mock bebop-ui ModuleFactory
    $ui_factory_mock = Test::double('Ponticlaro\Bebop\UI\Helpers\ModuleFactory', [
      'canManufacture' => true,
      'create'         => function() use($ui_module_mock) {
        return $ui_module_mock->make();
      }
    ]);

    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Add section
    $metabox->addSection('section_1', []);

    // Get reflection of ::__collectSectionsFieldNames and make it accessible
    $method = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Metabox', '__collectSectionsFieldNames');
    $method->setAccessible(true);
    $method->invoke($metabox, null, null, null);

    // Verify bebop-ui ModuleAbstract method is called
    $ui_module_mock->verifyInvoked('renderMainTemplate');
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::__setMetaFields
   * @depends create
   * @depends manageMetaFields
   * 
   * @param UnitTester $I Tester Module
   */
  public function setMetaFieldsViaCallback(UnitTester $I)
  {
    // Mock WP_Post
    $wp_post_mock = \Mockery::mock('alias:WP_Post');

    // Mock bebop-common Utils
    $util_mock = Test::double('Ponticlaro\Bebop\Common\Utils', [
      'getControlNamesFromCallable' => [
        'test_field_name_1',
        'test_field_name_2',
      ]
    ]);

    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Get reflection of ::__setMetaFields and make it accessible
    $method = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Metabox', '__setMetaFields');
    $method->setAccessible(true);
    $method->invoke($metabox);

    // Verify bebop-ui ModuleAbstract method is called
    $util_mock->verifyInvoked('getControlNamesFromCallable');
    
    // Test ::getMetaFields updated value
    $I->assertEquals($metabox->getMetaFields(), [
      'test_field_name_1',
      'test_field_name_2',
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::__setMetaFields
   * @depends create
   * @depends manageMetaFields 
   * @depends manageSections
   * 
   * @param UnitTester $I Tester Module
   */
  public function setMetaFieldsViaSections(UnitTester $I)
  {
    // Mock WP_Post
    $wp_post_mock = \Mockery::mock('alias:WP_Post');

    // Mock bebop-common Utils
    $util_mock = Test::double('Ponticlaro\Bebop\Common\Utils', [
      'getControlNamesFromCallable' => [
        'test_field_name_1',
        'test_field_name_2',
      ]
    ]);

    // Mock bebop-ui ModuleFactory
    $ui_factory_mock = Test::double('Ponticlaro\Bebop\UI\Helpers\ModuleFactory', [
      'canManufacture' => true,
      'create'         => function() {
        return func_get_arg(0);
      }
    ]);

    // Create test instance
    $metabox = new Metabox('Title', 'type1');

    // Add section
    $metabox->addSection('section_1', []);

    // Get reflection of ::__setMetaFields and make it accessible
    $method = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Metabox', '__setMetaFields');
    $method->setAccessible(true);
    $method->invoke($metabox);

    // Verify bebop-ui ModuleAbstract method is called
    $util_mock->verifyInvoked('getControlNamesFromCallable');
    
    // Test ::getMetaFields updated value
    $I->assertEquals($metabox->getMetaFields(), [
      'test_field_name_1',
      'test_field_name_2',
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::__setMetaFields
   * @depends create
   * @depends manageMetaFields 
   * 
   * @param UnitTester $I Tester Module
   */
  public function doNotSetMetaFieldsIfAlreadyManuallyAdded(UnitTester $I)
  {
    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Add section
    $metabox->addMetaField('test_meta_field_1');

    // Get reflection of ::__setMetaFields and make it accessible
    $method = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Metabox', '__setMetaFields');
    $method->setAccessible(true);
    $method->invoke($metabox);

    // Test ::getMetaFields updated value
    $I->assertEquals($metabox->getMetaFields(), [
      'test_meta_field_1'
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::__callbackWrapper
   * @depends create
   * @depends setAndGetId
   * @depends manageMetaFields 
   * 
   * @param UnitTester $I Tester Module
   */
  public function callbackWrapper(UnitTester $I)
  {
    // Mock bebop-ui ModuleAbstract
    $callback_mock = Test::func('Ponticlaro\Bebop\Cms', 'callback_mock', true);

    // Mock WP_Post
    $wp_post_mock     = \Mockery::mock('alias:WP_Post');
    $wp_post_mock->ID = 1;

    // Mock bebop-ui ModuleAbstract
    $ui_module_mock = Test::double('Ponticlaro\Bebop\UI\Modules\Input', [
      '__construct' => null,
      'render'      => true
    ]);
    
    // Mock bebop-ui ModuleFactory
    $ui_factory_mock = Test::double('Ponticlaro\Bebop\UI\Helpers\ModuleFactory', [
      'canManufacture' => true,
      'create'         => function() use($ui_module_mock) {
        return $ui_module_mock->make();
      }
    ]);

    // Mock Metabox
    $metabox_mock = Test::double('Ponticlaro\Bebop\Cms\Metabox', [
      '__setMetaFields' => null
    ]);

    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Set metabox->meta_fields
    $meta_fields_prop = new ReflectionProperty('Ponticlaro\Bebop\Cms\Metabox', 'meta_fields');
    $meta_fields_prop->setAccessible(true);
    $meta_fields_prop->getValue($metabox)->push('meta_field_1');

    // Add section
    $metabox->addSection('section_1', []);

    // Test ::__callbackWrapper
    $metabox->__callbackWrapper($wp_post_mock, $metabox);

    // Verify get_post_meta was invoked correctly
    $this->mocks['get_post_meta']->verifyInvokedOnce([$wp_post_mock->ID, 'meta_field_1']);

    // Verify metabox->data is correct
    $data_prop = new ReflectionProperty('Ponticlaro\Bebop\Cms\Metabox', 'data');
    $data_prop->setAccessible(true);
    $data = $data_prop->getValue($metabox);

    $I->assertEquals($data->getAll(), [
      'meta_field_1' => 'meta_field_1_value'
    ]);

    // Verify sample_control_elements was invoked correctly
    $callback_mock->verifyInvoked([
      $data,
      $wp_post_mock,
      $metabox,
    ]);

    // Check if $ui_module_mock got called correctly
    $ui_module_mock->verifyInvoked('render', [$data->getAll()]);

    // Verify wp_nonce_field was invoked correctly
    $id = $metabox->getid();
    $this->mocks['wp_nonce_field']->verifyInvokedOnce(['metabox_'. $id .'_saving_meta', 'metabox_'. $id .'_nonce']);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::__construct
   * @covers  Ponticlaro\Bebop\Cms\Metabox::applyArgs
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function applyRawArgs(UnitTester $I)
  {
    // Create reflection of PostType class
    $refl_type = new \ReflectionClass('Ponticlaro\Bebop\Cms\PostType');

    // Mock Metabox
    $metabox_mock = Test::double('Ponticlaro\Bebop\Cms\Metabox', [
      'addSection' => true
    ]);

    // Create test instance
    $metabox = new Metabox([
      'id'    => 'metabox_id',
      'title' => 'Title',
      'types' => [
          'type1',
          $refl_type->newInstance('Type2')
      ],
      'fn'      => 'sample_control_elements',
      'fn_args' => [
        'arg1',
        'arg2',
      ],
      'context'  => 'context',
      'priority' => 'priority',
      'sections' => [
        [
          'ui' => 'input'
        ],
        [
          'ui' => 'textarea'
        ]
      ]
    ]);

    // Verify Updated values
    $I->assertEquals($metabox->getId(), 'metabox_id');
    $I->assertEquals($metabox->getTitle(), 'Title');
    $I->assertEquals($metabox->getPostTypes(), ['type1', 'type2']);
    $I->assertEquals($metabox->getCallback(), 'sample_control_elements');
    $I->assertEquals($metabox->getCallbackArgs(), ['arg1', 'arg2']);
    $I->assertEquals($metabox->getContext(), 'context');
    $I->assertEquals($metabox->getPriority(), 'priority');

    // Verify $metabox::addSection was called correctly
    $metabox_mock->verifyInvokedOnce('addSection', ['input', []]);
    $metabox_mock->verifyInvokedOnce('addSection', ['textarea', []]);

    // Test ::applyArgs variations
    $metabox->applyArgs([
      'types' => 'type3' // This adds a post-type instead of replacing existing ones
    ]);

    // Verify Updated values
    $I->assertEquals($metabox->getPostTypes(), ['type1', 'type2', 'type3']);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::__saveMeta
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function saveMeta(UnitTester $I)
  {
    // Setup $_POST data
    $_POST = [
      '_inline_edit'               => 'somerandomhash',
      'metabox_title_nonce'        => 'anotherrandomhash',
      'post_type'                  => 'unit_test_type',
      'meta_field_with_string_val' => 'string',
      'meta_field_with_array_val'  => ['string_1', 'string_2'],
    ];

    // Mock wp_verify_nonce
    $wp_verify_nonce_mock = Test::func('Ponticlaro\Bebop\Cms', 'wp_verify_nonce', function() {
      return func_get_arg(1) == 'inlineeditnonce' ? false : true;
    });

    // Mock WP_Post
    $wp_post_mock = \Mockery::mock('alias:WP_Post');
    $wp_post_mock->post_type = 'unit_test_type';

    // Mock get_post
    $get_post_mock = Test::func('Ponticlaro\Bebop\Cms', 'get_post', function() use($wp_post_mock) {
      
      $wp_post_mock->ID = func_get_arg(0);

      return $wp_post_mock;
    });

    // Mock wp_verify_nonce
    $current_user_can_mock = Test::func('Ponticlaro\Bebop\Cms', 'current_user_can', true);

    // Mock delete_post_meta
    $delete_post_meta_mock = Test::func('Ponticlaro\Bebop\Cms', 'delete_post_meta', true);

    // Mock add_post_meta
    $add_post_meta_mock = Test::func('Ponticlaro\Bebop\Cms', 'add_post_meta', true);

    // Mock update_post_meta
    $update_post_meta_mock = Test::func('Ponticlaro\Bebop\Cms', 'update_post_meta', true);

    // Mock Collection to return desired $metabox->meta_fields
    $metabox_mock = Test::double('Ponticlaro\Bebop\Common\Collection', [
      'getAll' => [
        'meta_field_to_delete',
        'meta_field_with_array_val',
        'meta_field_with_string_val',
      ]
    ]);

    // Mock Metabox
    $metabox_mock = Test::double('Ponticlaro\Bebop\Cms\Metabox', [
      '__setMetaFields' => null
    ]);

    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Test ::__saveMeta
    $metabox->__saveMeta(1);

    // Verify wp_verify_nonce was only called twice
    $wp_verify_nonce_mock->verifyInvokedOnce([$_POST['_inline_edit'], 'inlineeditnonce']);
    $wp_verify_nonce_mock->verifyInvokedOnce([$_POST['metabox_title_nonce'], 'metabox_title_saving_meta']);

    // Verify that 'meta_field_to_delete' is deleted
    $delete_post_meta_mock->verifyInvokedOnce([1, 'meta_field_to_delete']);

    // Verify that 'meta_field_with_array_val' is deleted and added, value by value
    $delete_post_meta_mock->verifyInvokedOnce([1, 'meta_field_with_array_val']);
    $add_post_meta_mock->verifyInvokedOnce([1, 'meta_field_with_array_val', 'string_1']);
    $add_post_meta_mock->verifyInvokedOnce([1, 'meta_field_with_array_val', 'string_2']);

    // Verify that 'meta_field_with_string_val' is updated
    $update_post_meta_mock->verifyInvokedOnce([1, 'meta_field_with_string_val'], 'string');
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::__saveMeta
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function doNotSaveMetaIfUserCannotEditPost(UnitTester $I)
  {
    // Mock wp_verify_nonce
    $wp_verify_nonce_mock = Test::func('Ponticlaro\Bebop\Cms', 'wp_verify_nonce', function() {
      return func_get_arg(1) == 'inlineeditnonce' ? false : true;
    });

    // Mock WP_Post
    $wp_post_mock = \Mockery::mock('alias:WP_Post');
    $wp_post_mock->post_type = 'unit_test_type';

    // Mock get_post
    $get_post_mock = Test::func('Ponticlaro\Bebop\Cms', 'get_post', function() use($wp_post_mock) {
      
      $wp_post_mock->ID = func_get_arg(0);

      return $wp_post_mock;
    });

    // Mock wp_verify_nonce
    $current_user_can_mock = Test::func('Ponticlaro\Bebop\Cms', 'current_user_can', false);

    // Mock Metabox
    $metabox_mock = Test::double('Ponticlaro\Bebop\Cms\Metabox', [
      '__setMetaFields' => null
    ]);

    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Test ::__saveMeta
    $metabox->__saveMeta(1);

    // Verify ::__setMetaFields is never called
    $metabox_mock->verifyNeverInvoked('__setMetaFields');
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::__saveMeta
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function doNotSaveMetaIfDoingAutosave(UnitTester $I)
  {
    // Mock wp_verify_nonce
    $wp_verify_nonce_mock = Test::func('Ponticlaro\Bebop\Cms', 'wp_verify_nonce', function() {
      return func_get_arg(1) == 'inlineeditnonce' ? false : true;
    });

    // Mock WP_Post
    $wp_post_mock = \Mockery::mock('alias:WP_Post');
    $wp_post_mock->post_type = 'unit_test_type';

    // Mock get_post
    $get_post_mock = Test::func('Ponticlaro\Bebop\Cms', 'get_post', function() use($wp_post_mock) {
      
      $wp_post_mock->ID = func_get_arg(0);

      return $wp_post_mock;
    });

    // Mock wp_verify_nonce
    $current_user_can_mock = Test::func('Ponticlaro\Bebop\Cms', 'current_user_can', true);

    // Mock Metabox
    $metabox_mock = Test::double('Ponticlaro\Bebop\Cms\Metabox', [
      '__setMetaFields' => null
    ]);

    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Flag that we're doing an autosave
    define('DOING_AUTOSAVE', true);

    // Test ::__saveMeta
    $metabox->__saveMeta(1);

    // Verify that current_user_can is never invoked
    $current_user_can_mock->verifyNeverInvoked();
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::__saveMeta
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function doNotSaveMetaWhileInlineEditingViaAJAX(UnitTester $I)
  {
    // Setup $_POST data
    $_POST['_inline_edit']        = 'somerandomhash_2';
    $_POST['metabox_title_nonce'] = 'anotherrandomhash_2';

    // Mock wp_verify_nonce
    $wp_verify_nonce_mock = Test::func('Ponticlaro\Bebop\Cms', 'wp_verify_nonce', true);

    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Test ::__saveMeta
    $metabox->__saveMeta(1);

    // Verify wp_verify_nonce was only called once
    $wp_verify_nonce_mock->verifyInvokedOnce([$_POST['_inline_edit'], 'inlineeditnonce']);
    $wp_verify_nonce_mock->verifyNeverInvoked([$_POST['metabox_title_nonce'], 'metabox_title_saving_meta']);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Metabox::__register
   * @depends create
   * @depends managePostTypes
   * 
   * @param UnitTester $I Tester Module
   */
  public function register(UnitTester $I)
  {
    // Mock register_taxonomy
    $mock = Test::func('Ponticlaro\Bebop\Cms', 'add_meta_box', true);

    // Create test instance
    $metabox = new Metabox('Title', 'type1', 'sample_control_elements');

    // Call __register
    $metabox->__register();

    // Verify that add_meta_box is called once with the correct args
    foreach ($metabox->getPostTypes() as $post_type) {
      
      $mock->verifyInvokedOnce([
        $metabox->getId(),
        $metabox->getTitle(),
        [$metabox, '__callbackWrapper'],
        $post_type, 
        $metabox->getContext(),
        $metabox->getPriority(), 
        $metabox->getCallbackArgs()
      ]);
    }
  }
}
