<?php

namespace AdminPage;

use \UnitTester;
use AspectMock\Test;
use Ponticlaro\Bebop\Cms\AdminPage\Tab;

class TabCest
{
  /**
   * Expected values for the default config of a 'Title' AdminPage/Tab
   * 
   * @var array
   */
  private $base_cfg = [
    'id'       => 'id',
    'title'    => 'Title',
    'function' => 'Ponticlaro\Bebop\Cms\AdminPage\callback_mock',
    'options'  => [],
    'sections' => [],
    'data'     => [],
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

    // Mock add_action
    $this->m['add_action'] = Test::func('Ponticlaro\Bebop\Cms\AdminPage', 'add_action', true);

    // Mock settings_errors
    $this->m['settings_errors'] = Test::func('Ponticlaro\Bebop\Cms\AdminPage', 'settings_errors', true);

    // Mock settings_fields
    $this->m['settings_fields'] = Test::func('Ponticlaro\Bebop\Cms\AdminPage', 'settings_fields', true);

    // Mock register_setting
    $this->m['register_setting'] = Test::func('Ponticlaro\Bebop\Cms\AdminPage', 'register_setting', true);

    // Mock get_option
    $this->m['get_option'] = Test::func('Ponticlaro\Bebop\Cms\AdminPage', 'get_option', function() {
      return func_get_arg(0) .'_value';
    });

    // Mock callback
    $this->m['callback'] = Test::func('Ponticlaro\Bebop\Cms\AdminPage', 'callback_mock', true);

    // Mock callback alternative
    $this->m['callback_alt'] = Test::func('Ponticlaro\Bebop\Cms\AdminPage', 'callback_mock_alt', true);
  }

  public function _after(UnitTester $I)
  {
    Test::clean();
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\AdminPage\Tab::__construct
   * 
   * @param UnitTester $I Tester Module
   */
  public function create(UnitTester $I)
  {
    // Create test instance
    $tab = new Tab('ID', 'Title', 'Ponticlaro\Bebop\Cms\AdminPage\callback_mock');

    // Check $tab->id
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage\Tab', 'id');
    $prop->setAccessible(true);

    $I->assertEquals($prop->getValue($tab), $this->base_cfg['id']);

    // Check $tab->title
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage\Tab', 'title');
    $prop->setAccessible(true);

    $I->assertEquals($prop->getValue($tab), $this->base_cfg['title']);

    // Check $tab->function
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage\Tab', 'function');
    $prop->setAccessible(true);

    $I->assertEquals($prop->getValue($tab), $this->base_cfg['function']);

    // Check $tab->options
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage\Tab', 'options');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($tab);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->base_cfg['options']);

    // Check $tab->sections
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage\Tab', 'sections');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($tab);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->base_cfg['sections']);

    // Check $tab->data
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage\Tab', 'data');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($tab);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->base_cfg['data']);

    // Verify add_action was called correctly
    $this->m['add_action']->verifyInvokedOnce(['admin_init', [$tab, '__handleSettingsRegistration']]);
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\AdminPage\Tab::__construct
   * @covers Ponticlaro\Bebop\Cms\AdminPage\Tab::applyArgs
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function applyRawArgs(UnitTester $I)
  {
    // Create test instance
    $tab = new Tab('id', 'Title', [
      'id'         => 'id_alt',
      'title'      => 'TitleAlt',
      'fn'         => 'Ponticlaro\Bebop\Cms\AdminPage\callback_mock',
      'options'    => [
        'option_value_1',
        'option_value_2',
      ],
      'sections'   => [
        [
          'ui' => 'input'
        ]
      ]
    ]);

    // Check $tab->id
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage\Tab', 'id');
    $prop->setAccessible(true);

    $I->assertEquals($prop->getValue($tab), 'id_alt');

    // Check $tab->title
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage\Tab', 'title');
    $prop->setAccessible(true);

    $I->assertEquals($prop->getValue($tab), 'TitleAlt');

    // Check $tab->function
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage\Tab', 'function');
    $prop->setAccessible(true);

    $I->assertEquals($prop->getValue($tab), $this->base_cfg['function']);

    // Check $tab->options
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage\Tab', 'options');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($tab);

    $I->assertEquals($prop_val->getAll(), [
      'option_value_1',
      'option_value_2',
    ]);

    // Check $tab->sections
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage\Tab', 'sections');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($tab);
    $sections = $prop_val->getAll();

    $I->assertCount(1, $sections);
    $I->assertTrue(reset($sections) instanceof \Ponticlaro\Bebop\UI\Patterns\ModuleAbstract);

    // Check $tab->data
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage\Tab', 'data');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($tab);

    $I->assertEquals($prop_val->getAll(), $this->base_cfg['data']);
  }


  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage\Tab::setId
   * @covers  Ponticlaro\Bebop\Cms\AdminPage\Tab::getId
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetId(UnitTester $I)
  {
    // Create test instance
    $tab = new Tab('ID', 'Title', 'Ponticlaro\Bebop\Cms\AdminPage\callback_mock');

    // Test ::getId default value
    $I->assertEquals($this->base_cfg['id'], $tab->getId());

    // Test ::setId
    $tab->setId('test_id');

    // Test ::getId updated value
    $I->assertEquals('test_id', $tab->getId());

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
      $I->expectException(\Exception::class, function() use($tab, $bad_arg_val) {
        $tab->setId($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage\Tab::setTitle
   * @covers  Ponticlaro\Bebop\Cms\AdminPage\Tab::getTitle
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetTitle(UnitTester $I)
  {
    // Create test instance
    $tab = new Tab('ID', 'Title', 'Ponticlaro\Bebop\Cms\AdminPage\callback_mock');

    // Test ::getTitle default value
    $I->assertEquals($this->base_cfg['title'], $tab->getTitle());

    // Test ::setTitle
    $tab->setTitle('test_title');

    // Test ::getTitle updated value
    $I->assertEquals('test_title', $tab->getTitle());

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
      $I->expectException(\Exception::class, function() use($tab, $bad_arg_val) {
        $tab->setTitle($bad_arg_val);
      });
    }  
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage\Tab::setFunction
   * @covers  Ponticlaro\Bebop\Cms\AdminPage\Tab::getFunction
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetFunction(UnitTester $I)
  {
    // Create test instance
    $tab = new Tab('ID', 'Title', 'Ponticlaro\Bebop\Cms\AdminPage\callback_mock');

    // Test ::getFunction default value
    $I->assertEquals($this->base_cfg['function'], $tab->getFunction());

    // Test ::setFunction
    $tab->setFunction('Ponticlaro\Bebop\Cms\AdminPage\callback_mock_alt');

    // Test ::getFunction updated value
    $I->assertEquals('Ponticlaro\Bebop\Cms\AdminPage\callback_mock_alt', $tab->getFunction());

    // Test ::setFunction with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [1],
      new \stdClass,
      '_______undefined_callable',
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(\Exception::class, function() use($tab, $bad_arg_val) {
        $tab->setFunction($bad_arg_val);
      });
    } 
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage\Tab::addOption
   * @covers  Ponticlaro\Bebop\Cms\AdminPage\Tab::setOptions
   * @covers  Ponticlaro\Bebop\Cms\AdminPage\Tab::getOptions
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function manageOptions(UnitTester $I)
  {
    // Create test instance
    $tab = new Tab('ID', 'Title', 'Ponticlaro\Bebop\Cms\AdminPage\callback_mock');

    // Test ::getOptions default value
    $I->assertEquals($this->base_cfg['options'], $tab->getOptions());

    // Test ::addOption
    $tab->addOption('value_1');

    // Test ::getOptions updated value
    $I->assertEquals($tab->getOptions(), [
      'value_1',
    ]);

    // Test ::setOptions
    $tab->setOptions([
      'value_2',
      'value_3',
      'value_4',
    ]);

    // Test ::getOptions updated value
    $I->assertEquals($tab->getOptions(), [
      'value_1', // ::setOptions do not replace existing options
      'value_2',
      'value_3',
      'value_4',
    ]);

    // Test ::addOption with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(\Exception::class, function() use($tab, $bad_arg_val) {
        $tab->addOption($bad_arg_val);
      });
    } 
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage\Tab::addSection
   * @covers  Ponticlaro\Bebop\Cms\AdminPage\Tab::getAllSections
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function manageSections(UnitTester $I)
  {
    // Create test instance
    $tab = new Tab('ID', 'Title', 'Ponticlaro\Bebop\Cms\AdminPage\callback_mock');

    // Test ::getAllSections default value
    $I->assertEquals($this->base_cfg['sections'], $tab->getAllSections());

    // Test ::addSection
    $tab->addSection('section_dummy_1', []);

    // Get sections
    $sections = $tab->getAllSections();

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
      $I->expectException(\Exception::class, function() use($tab, $bad_arg_val) {
        $tab->addSection($bad_arg_val, []);
      });
    }  
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage\Tab::__call
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function callMagicMethod(UnitTester $I)
  {
    // Create test instance
    $tab = new Tab('ID', 'Title', 'Ponticlaro\Bebop\Cms\AdminPage\callback_mock');

    // Test ::__call
    $tab->input([]);

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
    $I->expectException(\Exception::class, function() use($tab) {
      $tab->______testUndefinedUISection();
    });

    $ui_factory_mock->verifyInvokedOnce('canManufacture', ['______testUndefinedUISection']);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage\Tab::__collectSectionsFieldNames
   * @depends create
   * @depends manageSections 
   * 
   * @param UnitTester $I Tester Module
   */
  public function collectSectionsFieldNames(UnitTester $I)
  {
    // Create test instance
    $tab = new Tab('ID', 'Title', 'Ponticlaro\Bebop\Cms\AdminPage\callback_mock');

    // Add section
    $tab->addSection('section_1', []);

    // Get reflection of ::__collectSectionsFieldNames and make it accessible
    $method = new \ReflectionMethod('Ponticlaro\Bebop\Cms\AdminPage\Tab', '__collectSectionsFieldNames');
    $method->setAccessible(true);
    $method->invoke($tab, null, null);

    // Verify bebop-ui ModuleAbstract method is called
    $this->m['ui_module']->verifyInvokedOnce('renderMainTemplate');
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage\Tab::__handleSettingsRegistration
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function handleSettingsRegistration(UnitTester $I)
  {
    // Mock AdminPageTab
    $mock = Test::double('Ponticlaro\Bebop\Cms\AdminPage\Tab', [
      'getId'          => $this->base_cfg['id'],
      '__setData'      => true,
      'getFunction'    => $this->base_cfg['function'],
      'getAllSections' => [
        $this->m['ui_module']->make()
      ],
    ]);

    // Create test instance
    $tab = new Tab('ID', 'Title', 'Ponticlaro\Bebop\Cms\AdminPage\callback_mock');
   
    // Test ::__handleSettingsRegistration
    $tab->__handleSettingsRegistration();

    // Get reflection of $data property and make it accessible
    $data_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage\Tab', 'data');
    $data_prop->setAccessible(true);
    $data = $data_prop->getValue($tab);

    // Get reflection of $options property and make it accessible
    $options_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage\Tab', 'options');
    $options_prop->setAccessible(true);
    $options = $options_prop->getValue($tab);

    // Get reflection of $sections property and make it accessible
    $sections_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage\Tab', 'sections');
    $sections_prop->setAccessible(true);
    $sections = $sections_prop->getValue($tab);

    // Verify ::getFunction was invoked
    $mock->verifyInvokedOnce('getFunction');

    // Verify ::getAllSections was invoked
    $mock->verifyInvokedOnce('getAllSections');

    // Verify Utils::getControlNamesFromCallable was invoked for the callback
    $this->m['Utils']->verifyInvokedOnce('getControlNamesFromCallable', [
      $this->base_cfg['function'],
      [
        $data,
        $tab,
      ]
    ]);

    // Verify Utils::getControlNamesFromCallable was invoked for sections
    $this->m['Utils']->verifyInvokedOnce('getControlNamesFromCallable', [
      [
        $tab,
        '__collectSectionsFieldNames',
      ],
      [
        $data,
        $tab,
      ]
    ]);

    // Verify options property was updated
    $I->assertEquals($options->getAll(), [
      'dummy_field_name_1',
      'dummy_field_name_2',
    ]);

    // Verify register_setting was invoked
    foreach ($options->getAll() as $name) {
      $this->m['register_setting']->verifyInvokedOnce([$this->base_cfg['id'], $name]);
    }
    
    // Verify sections property only contain the single added element
    $I->assertCount(1, $sections->getAll());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\AdminPage\Tab::__setData
   * @depends create
   * @depends manageSections 
   * 
   * @param UnitTester $I Tester Module
   */
  public function setData(UnitTester $I)
  {
    // Create test instance
    $tab = new Tab('ID', 'Title', 'Ponticlaro\Bebop\Cms\AdminPage\callback_mock');
   
    // Get reflection of $options property and make it accessible
    $options_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage\Tab', 'options');
    $options_prop->setAccessible(true);
    $options = $options_prop->getValue($tab);

    // Add options for test
    $options_list = ['name_1','name_2','name_3'];
    $options->setList($options_list);

    // Get reflection of ::__setData and make it accessible
    $method = new \ReflectionMethod('Ponticlaro\Bebop\Cms\AdminPage\Tab', '__setData');
    $method->setAccessible(true);
    
    // Test ::__setData
    $method->invoke($tab);

    // Verify bebop-ui ModuleAbstract method is called
    foreach ($options_list as $option_name) {
      $this->m['get_option']->verifyInvokedOnce([$option_name]);
    }
    
    // Get reflection of $data property and make it accessible
    $data_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage\Tab', 'data');
    $data_prop->setAccessible(true);
    $data = $data_prop->getValue($tab);

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
   * @covers  Ponticlaro\Bebop\Cms\AdminPage\Tab::render
   * @depends create
   * @depends manageSections 
   * 
   * @param UnitTester $I Tester Module
   */
  public function render(UnitTester $I)
  {
    // Mock AdminPageTab
    $mock = Test::double('Ponticlaro\Bebop\Cms\AdminPage\Tab', [
      '__setData'      => true,
      'getFunction'    => $this->base_cfg['function'],
      'getAllSections' => [
        $this->m['ui_module']->make()
      ],
    ]);

    // Create test instance
    $tab = new Tab('ID', 'Title', 'Ponticlaro\Bebop\Cms\AdminPage\callback_mock');
    
    // Test ::render()
    ob_start();
    $tab->render();
    $html = ob_get_clean();

    // Get reflection of $data property and make it accessible
    $data_prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\AdminPage\Tab', 'data');
    $data_prop->setAccessible(true);
    $data = $data_prop->getValue($tab);

    // Verify that ::__setData was invoked
    $mock->verifyInvokedOnce('__setData');

    // Verify callback_mock was invoked
    $this->m['callback']->verifyInvokedOnce([
      $data,
      $tab,
    ]);

    // Verify that bebop-ui UI Module ::render was invoked
    $this->m['ui_module']->verifyInvokedOnce('render', [
      $data->getAll(),
    ]);
  }
}