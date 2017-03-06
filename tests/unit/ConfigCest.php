<?php

use AspectMock\Test;
use Ponticlaro\Bebop\Cms\Config;

class ConfigCest
{
  /**
   * List of mocks
   * 
   * @var array
   */
  private $m = [];

  public function _before(UnitTester $I)
  {
    // add_action mock
    $this->m['add_action'] = Test::func('Ponticlaro\Bebop\Cms', 'add_action', true);

    // PathManager mock
    $this->m['PathManager'] = Test::double('Ponticlaro\Bebop\Common\PathManager', [
      '__construct' => null,
      'get'         => function() {
        return '/path/to/theme/'. func_get_arg(1); 
      }
    ]);

    Test::double('Ponticlaro\Bebop\Common\PathManager', [
      'getInstance' => $this->m['PathManager']->construct()
    ]);


    // ConfigItem mock
    $this->m['ConfigItem'] = new BebopUnitTests\Configitem;

    // ConfigItemFactory mock
    $this->m['ConfigItemFactory'] = Test::double('Ponticlaro\Bebop\Cms\Helpers\ConfigItemFactory', [
      'canManufacture' => function() {
        return in_array(func_get_arg(0), [
          'admin_pages',
          'image_sizes',
          'metaboxes',
          'paths',
          'scripts',
          'shortcodes',
          'styles',
          'taxonomies',
          'types',
          'urls',
        ]) ? true : false;
      },
      'create' => $this->m['ConfigItem']
    ]);

    // ConfigSectionFactory mock
    $this->m['ConfigSectionFactory'] = Test::double('Ponticlaro\Bebop\Cms\Helpers\ConfigSectionFactory', [
      'canManufacture' => function() {
        return in_array(func_get_arg(0), [
          'scripts',
          'styles',
        ]) ? true : false;
      },
      'create' => new BebopUnitTests\ConfigSection
    ]);
  }

  public function _after(UnitTester $I)
  {
    Test::clean();
    \Mockery::close();
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config::__construct
   * @covers Ponticlaro\Bebop\Cms\Config::getInstance
   * 
   * @param UnitTester $I Tester Module
   */
  public function create(UnitTester $I)
  {
    // Create test instance
    $config = Config::getInstance();
  
    // Get $hooks property and make it accessible
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Config', 'hooks');
    $prop_refl->setAccessible(true);
    $prop_value = $prop_refl->getValue($config);

    // Verify that $hooks match expected type and value
    $I->assertTrue($prop_value instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_value->getAll(), [
      'presets' => [
        str_replace('/tests/unit', '/src', __DIR__) .'/presets.json',
        '/path/to/theme/bebop-presets.json'
      ],
      'build' => [
        '/path/to/theme/bebop.json'
      ]
    ]);

    // Get $config property and make it accessible
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Config', 'config');
    $prop_refl->setAccessible(true);
    $prop_value = $prop_refl->getValue($config);

    // Verify that $config match expected type and value
    $I->assertTrue($prop_value instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_value->getAll(), []);

    // Get $already_built property and make it accessible
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Config', 'already_built');
    $prop_refl->setAccessible(true);

    // Verify that $already_built match expected value
    $I->assertFalse($prop_refl->getValue($config));

    // Verify that add_action was invoked correctly
    $this->m['add_action']->verifyInvokedOnce([
      'after_setup_theme',
      [
        $config,
        'build',
      ]
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config::addToHook
   * @covers  Ponticlaro\Bebop\Cms\Config::setHook
   * @covers  Ponticlaro\Bebop\Cms\Config::getHook
   * @covers  Ponticlaro\Bebop\Cms\Config::clearHook
   * @depends create
   *
   * @param UnitTester $I Tester Module
   */
  public function manageHooks(UnitTester $I)
  {
    // Create test instance
    $config = new Config();

    // Get $hooks property and make it accessible
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Config', 'hooks');
    $prop_refl->setAccessible(true);

    // Test ::clearHook
    $config->clearHook('presets');
    $config->clearHook('build');

    // Verify that $hooks match expexted value
    $I->assertEquals($prop_refl->getValue($config)->getAll(), [
      'presets' => [],
      'build'   => [],
    ]);

    // Test ::setHook
    $config->setHook('unit_test_hook', [
      '/path/to/config_1.json',
      '/path/to/config_2.json'
    ]);

    // Verify that $hooks match expexted value
    $I->assertEquals($prop_refl->getValue($config)->getAll(), [
      'presets'        => [],
      'build'          => [],
      'unit_test_hook' => [
        '/path/to/config_1.json',
        '/path/to/config_2.json'
      ]
    ]);
 
    // Test ::addToHook
    $config->addToHook('unit_test_hook', '/path/to/config_3.json');
    $config->addToHook('unit_test_hook', '/path/to/config_4.json');

    $hooks = $prop_refl->getValue($config)->getAll();
    $I->assertEquals($hooks['unit_test_hook'], [
      '/path/to/config_1.json',
      '/path/to/config_2.json',
      '/path/to/config_3.json',
      '/path/to/config_4.json',
    ]);

    // Test ::getHook
    $I->assertEquals($config->getHook('unit_test_hook'), [
      '/path/to/config_1.json',
      '/path/to/config_2.json',
      '/path/to/config_3.json',
      '/path/to/config_4.json',
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config::runHooks
   * @depends create
   *
   * @param UnitTester $I Tester Module
   */
  public function runHooks(UnitTester $I)
  {
    // Config mock
    $config_mock = Test::double('Ponticlaro\Bebop\Cms\Config', [
      'processConfig' => null
    ]);

    // Create test instance
    $config = new Config();

    // Get ::runHooks and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config', 'runHooks');
    $method_refl->setAccessible(true);

    // Test ::runHooks
    $method_refl->invoke($config);

    // Verify ::processConfig was invoked correctly
    $config_mock->verifyInvokedOnce('processConfig', [
      'presets',
      str_replace('/tests/unit', '/src', __DIR__) .'/presets.json',
    ]);

    $config_mock->verifyInvokedOnce('processConfig', [
      'presets',
      '/path/to/theme/bebop-presets.json',
    ]);

    $config_mock->verifyInvokedOnce('processConfig', [
      'build',
      '/path/to/theme/bebop.json',
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config::processConfig
   * @depends create
   *
   * @param UnitTester $I Tester Module
   */
  public function processConfig(UnitTester $I)
  {
    // Config mock
    $config_mock = Test::double('Ponticlaro\Bebop\Cms\Config', [
      'processHookEnvConfig' => null
    ]);

    // Create test instance
    $config = new Config();

    // Define source configuration
    $src_config = [
      'development' => [
        'types'      => ['dummy_type_item'],
        'taxonomies' => ['dummy_tax_item'],
      ],
      'all' => [
        'types'      => ['dummy_type_item'],
        'taxonomies' => ['dummy_tax_item'],
      ]
    ];

    // Get ::processConfig and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config', 'processConfig');
    $method_refl->setAccessible(true);

    // Test ::processConfig
    $method_refl->invokeArgs($config, [
      'unit_test_hook',
      $src_config
    ]);

    // Verify ::processHookEnvConfig was invoked correctly
    $config_mock->verifyInvokedOnce('processHookEnvConfig', [
      'unit_test_hook',
      'development',
      $src_config['development'],
    ]);

    $config_mock->verifyInvokedOnce('processHookEnvConfig', [
      'unit_test_hook',
      'all',
      $src_config['all'],
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config::processConfig
   * @depends create
   *
   * @param UnitTester $I Tester Module
   */
  public function processConfigNotContainingEnvironments(UnitTester $I)
  {
    // Config mock
    $config_mock = Test::double('Ponticlaro\Bebop\Cms\Config', [
      'processHookEnvConfig' => null
    ]);

    // Create test instance
    $config = new Config();

    // Define source configuration
    $src_config = [
      'types'      => ['dummy_type_item'],
      'taxonomies' => ['dummy_tax_item'],
    ];

    // Get ::processConfig and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config', 'processConfig');
    $method_refl->setAccessible(true);

    // Test ::processConfig
    $method_refl->invokeArgs($config, [
      'unit_test_hook',
      $src_config
    ]);

    $config_mock->verifyInvokedOnce('processHookEnvConfig', [
      'unit_test_hook',
      'all',
      $src_config,
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config::processConfig
   * @depends create
   *
   * @param UnitTester $I Tester Module
   */
  public function processConfigContainingEnvironmentsKey(UnitTester $I)
  {
    // Config mock
    $config_mock = Test::double('Ponticlaro\Bebop\Cms\Config', [
      'processHookEnvConfig' => null
    ]);

    // Create test instance
    $config = new Config();

    // Define source configuration
    $src_config = [
      'environments' => [
        'development' => [
          'types'      => ['dummy_type_item'],
          'taxonomies' => ['dummy_tax_item'],
        ],
        'all' => [
          'types'      => ['dummy_type_item'],
          'taxonomies' => ['dummy_tax_item'],
        ]
      ]
    ];

    // Get ::processConfig and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config', 'processConfig');
    $method_refl->setAccessible(true);

    // Test ::processConfig
    $method_refl->invokeArgs($config, [
      'unit_test_hook',
      $src_config
    ]);

    // Verify ::processHookEnvConfig was invoked correctly
    $config_mock->verifyInvokedOnce('processHookEnvConfig', [
      'unit_test_hook',
      'development',
      $src_config['environments']['development'],
    ]);

    $config_mock->verifyInvokedOnce('processHookEnvConfig', [
      'unit_test_hook',
      'all',
      $src_config['environments']['all'],
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config::processConfig
   * @depends create
   *
   * @param UnitTester $I Tester Module
   */
  public function processConfigFullFromFile(UnitTester $I)
  {
    // Define source configuration
    $src_config = [
      'development' => [
        'types'      => ['dummy_type_item'],
        'taxonomies' => ['dummy_tax_item'],
      ],
      'all' => [
        'types'      => ['dummy_type_item'],
        'taxonomies' => ['dummy_tax_item'],
      ]
    ];

    // Config mock
    $config_mock = Test::double('Ponticlaro\Bebop\Cms\Config', [
      'processHookEnvConfig' => null,
      'getConfigFromFile'    => $src_config
    ]);

    // Create test instance
    $config = new Config();

    // Get ::processConfig and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config', 'processConfig');
    $method_refl->setAccessible(true);

    // Test ::processConfig
    $method_refl->invokeArgs($config, [
      'unit_test_hook',
      '/path/to/unit/tests/config/mock.json'
    ]);

    // Verify ::processHookEnvConfig was invoked correctly
    $config_mock->verifyInvokedOnce('processHookEnvConfig', [
      'unit_test_hook',
      'development',
      $src_config['development'],
    ]);

    $config_mock->verifyInvokedOnce('processHookEnvConfig', [
      'unit_test_hook',
      'all',
      $src_config['all'],
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config::processConfig
   * @depends create
   *
   * @param UnitTester $I Tester Module
   */
  public function processConfigEnvironmentFromFile(UnitTester $I)
  {
    // Config mock
    $config_mock = Test::double('Ponticlaro\Bebop\Cms\Config', [
      'processHookEnvConfig' => null,
      'getConfigFromFile'    => [
        'types'      => ['dummy_type_item'],
        'taxonomies' => ['dummy_tax_item'],
      ]
    ]);

    // Define source configuration
    $src_config = [
      'development' => '/path/to/unit/tests/config/mock.json',
      'all'         => [
        'types'      => ['dummy_type_item'],
        'taxonomies' => ['dummy_tax_item'],
      ]
    ];

    // Create test instance
    $config = new Config();

    // Get ::processConfig and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config', 'processConfig');
    $method_refl->setAccessible(true);

    // Test ::processConfig
    $method_refl->invokeArgs($config, [
      'unit_test_hook',
      $src_config
    ]);

    // Verify ::processHookEnvConfig was invoked correctly
    $config_mock->verifyInvokedOnce('processHookEnvConfig', [
      'unit_test_hook',
      'development',
      [
        'types'      => ['dummy_type_item'],
        'taxonomies' => ['dummy_tax_item'],
      ]
    ]);

    $config_mock->verifyInvokedOnce('processHookEnvConfig', [
      'unit_test_hook',
      'all',
      $src_config['all'],
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config::processHookEnvConfig
   * @depends create
   *
   * @param UnitTester $I Tester Module
   */
  public function processHookEnvConfig(UnitTester $I)
  {
    // Config mock
    $config_mock = Test::double('Ponticlaro\Bebop\Cms\Config', [
      'processSectionConfigItemsList' => null
    ]);

    // Create test instance
    $config = new Config();

    // Define source configuration
    $src_config = [
      'types'      => ['dummy_type_item'],
      'taxonomies' => ['dummy_tax_item'],
    ];

    // Get ::processHookEnvConfig and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config', 'processHookEnvConfig');
    $method_refl->setAccessible(true);

    // Test ::processHookEnvConfig
    $method_refl->invokeArgs($config, [
      'unit_test_hook',
      'all',
      $src_config
    ]);

    // Verify ::processSectionConfigItemsList is invoked correctly
    $config_mock->verifyInvokedOnce('processSectionConfigItemsList', [
      'unit_test_hook',
      'all',
      'types',
      $src_config['types'],
    ]);

    $config_mock->verifyInvokedOnce('processSectionConfigItemsList', [
      'unit_test_hook',
      'all',
      'taxonomies',
      $src_config['taxonomies'],
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config::processHookEnvConfig
   * @depends create
   *
   * @param UnitTester $I Tester Module
   */
  public function processHookEnvConfigFromFile(UnitTester $I)
  {
    // Config mock
    $config_mock = Test::double('Ponticlaro\Bebop\Cms\Config', [
      'processSectionConfigItemsList' => null,
      'getConfigFromFile'             => ['dummy_type_item'],
    ]);

    // Create test instance
    $config = new Config();

    // Define source configuration
    $src_config = [
      'types'      => '/path/to/unit/tests/config/mock.json',
      'taxonomies' => ['dummy_tax_item'],
    ];

    // Get ::processHookEnvConfig and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config', 'processHookEnvConfig');
    $method_refl->setAccessible(true);

    // Test ::processHookEnvConfig
    $method_refl->invokeArgs($config, [
      'unit_test_hook',
      'all',
      $src_config
    ]);

    // Verify ::processSectionConfigItemsList is invoked correctly
    $config_mock->verifyInvokedOnce('processSectionConfigItemsList', [
      'unit_test_hook',
      'all',
      'types',
      ['dummy_type_item'],
    ]);

    $config_mock->verifyInvokedOnce('processSectionConfigItemsList', [
      'unit_test_hook',
      'all',
      'taxonomies',
      $src_config['taxonomies'],
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config::processHookEnvConfig
   * @depends create
   *
   * @param UnitTester $I Tester Module
   */
  public function processHookEnvConfigFromManufacturableSection(UnitTester $I)
  {
    // Config mock
    $config_mock = Test::double('Ponticlaro\Bebop\Cms\Config', [
      'processSectionConfigItemsList' => null
    ]);

    // Create test instance
    $config = new Config();

    // Define source configuration
    $src_config = [
      'scripts'    => ['dummy_script_item'],
      'taxonomies' => ['dummy_tax_item'],
    ];

    // Get ::processHookEnvConfig and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config', 'processHookEnvConfig');
    $method_refl->setAccessible(true);

    // Test ::processHookEnvConfig
    $method_refl->invokeArgs($config, [
      'unit_test_hook',
      'all',
      $src_config
    ]);

    // Verify ::processSectionConfigItemsList is invoked correctly
    $config_mock->verifyInvokedOnce('processSectionConfigItemsList', [
      'unit_test_hook',
      'all',
      'scripts',
      [
        'dummy_section_item_1',
        'dummy_section_item_2',
      ],
    ]);

    $config_mock->verifyInvokedOnce('processSectionConfigItemsList', [
      'unit_test_hook',
      'all',
      'taxonomies',
      $src_config['taxonomies'],
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config::processSectionConfigItemsList
   * @depends create
   *
   * @param UnitTester $I Tester Module
   */
  public function processSectionConfigItemsList(UnitTester $I)
  {
    // Config mock
    $config_mock = Test::double('Ponticlaro\Bebop\Cms\Config', [
      'processSectionConfigItem' => null,
      'getConfigFromFile'        => [
        ['dummy_type_item_1'],
        ['dummy_type_item_2'],
      ]
    ]);

    // Create test instance
    $config = new Config();

    // Define source configuration
    $src_config = [
      '/path/to/unit/tests/config/mock.json',
      ['dummy_type_item_3'],
    ];

    // Get ::processHookEnvConfig and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config', 'processSectionConfigItemsList');
    $method_refl->setAccessible(true);

    // Test ::processHookEnvConfig
    $method_refl->invokeArgs($config, [
      'unit_test_hook',
      'all',
      'types',
      $src_config
    ]);

    // Verify ::processSectionConfigItem is invoked correctly
    $config_mock->verifyInvokedOnce('processSectionConfigItem', [
      'unit_test_hook',
      'all',
      'types',
      [
        'dummy_type_item_1'
      ],
    ]);

    $config_mock->verifyInvokedOnce('processSectionConfigItem', [
      'unit_test_hook',
      'all',
      'types',
      [
        'dummy_type_item_2'
      ],
    ]);

    $config_mock->verifyInvokedOnce('processSectionConfigItem', [
      'unit_test_hook',
      'all',
      'types',
      [
        'dummy_type_item_3'
      ],
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config::processSectionConfigItem
   * @depends create
   *
   * @param UnitTester $I Tester Module
   */
  public function processSectionConfigItem(UnitTester $I)
  {
    // Config mock
    $config_mock = Test::double('Ponticlaro\Bebop\Cms\Config', [
      'mergeConfigItemWithPreset' => function() {
        return func_get_arg(2);
      },
      'addConfigItem' => null,
    ]);

    // Create test instance
    $config = new Config();

    // Define source configuration
    $src_config = [
      'name'     => 'Unit Test',
      "supports" => [
        "title",
        "revisions"
      ],
      "rewrite" => [
        "slug" => "unit-test"
      ]
    ];

    // Get ::processHookEnvConfig and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config', 'processSectionConfigItem');
    $method_refl->setAccessible(true);

    // Test ::processHookEnvConfig
    $method_refl->invokeArgs($config, [
      'unit_test_hook',
      'all',
      'types',
      []
    ]);

    // Verify ::mergeConfigItemWithPreset is invoked correctly
    $config_mock->verifyInvokedOnce('mergeConfigItemWithPreset', [
      'all',
      'types',
      $this->m['ConfigItem']
    ]);

    // Verify ::addConfigItem is invoked correctly
    $config_mock->verifyInvokedOnce('addConfigItem', [
      'unit_test_hook',
      'all',
      'types',
      $this->m['ConfigItem']
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config::mergeConfigItemWithPreset
   * @depends create
   *
   * @param UnitTester $I Tester Module
   */
  public function mergeConfigItemWithPreset(UnitTester $I)
  {
    // Source ConfigItem
    $source_item = \Mockery::mock('Ponticlaro\Bebop\Cms\Patterns\ConfigItemInterface');

    // Verify that ConfigItem::getPresetId is called correctly on the source item
    $source_item->shouldReceive('getPresetId')
                ->once()
                ->andReturn('unit_test')
                ->mock();

    // Preset ConfigItem
    $preset_item = \Mockery::mock('Ponticlaro\Bebop\Cms\Patterns\ConfigItemInterface');

    // Verify that ConfigItem::merge is called correctly on the preset item
    $preset_item->shouldReceive('merge')
                ->with($source_item)
                ->once()
                ->andReturn(\Mockery::self())
                ->mock();

    // Verify that ConfigItem::remove is called correctly on the preset item
    $preset_item->shouldReceive('remove')
                ->with('preset')
                ->once()
                ->mock();

    // Mock config property
    $coll_mock = \Mockery::mock('Ponticlaro\Bebop\Common\Collection')
                 ->shouldReceive('get')
                 ->with("presets.all.types.unit_test")
                 ->once()
                 ->andReturn($preset_item)
                 ->mock();

    // Create test instance
    $config = new Config();

    // Override Config::config property with mock
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Config', 'config');
    $prop_refl->setAccessible(true);
    $prop_refl->setValue($config, $coll_mock);

    // Get ::processHookEnvConfig and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config', 'mergeConfigItemWithPreset');
    $method_refl->setAccessible(true);

    // Test ::mergeConfigItemWithPreset
    $returned = $method_refl->invokeArgs($config, [
      'all',
      'types',
      $source_item
    ]);

    // Verify that returned object matches preset object
    $I->assertSame($returned, $preset_item);

    \Mockery::close();
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config::addConfigItem
   * @depends create
   *
   * @param UnitTester $I Tester Module
   */
  public function addConfigItem(UnitTester $I)
  {
    // Source ConfigItem
    $source_item = \Mockery::mock('Ponticlaro\Bebop\Cms\Patterns\ConfigItemInterface');

    // Verify that ConfigItem::getUniqueId is called correctly on the source item
    $source_item->shouldReceive('getUniqueId')
                ->once()
                ->andReturn('unit_test')
                ->mock();

    // Previous ConfigItem
    $prev_item = \Mockery::mock('Ponticlaro\Bebop\Cms\Patterns\ConfigItemInterface');

    // Verify that ConfigItem::merge is called correctly on the previous item
    $prev_item->shouldReceive('merge')
              ->with($source_item)
              ->once()
              ->andReturn(\Mockery::self())
              ->mock();

    $prev_item->shouldReceive('getRequirements')
              ->andReturn([
                'requirement_1',
                'requirement_2',
              ])
              ->once()
              ->mock();

    // Mock config property
    $coll_mock = \Mockery::mock('Ponticlaro\Bebop\Common\Collection');

    // Verify that Collection::get is called correctly
    $coll_mock->shouldReceive('get')
              ->with("unit_test_hook.all.types.unit_test")
              ->once()
              ->andReturn($prev_item)
              ->mock();
    
    // Verify that Collection::set is called correctly
    $coll_mock->shouldReceive('set')
              ->withArgs([
                "unit_test_hook.all.types.unit_test",
                $prev_item
              ])
              ->once()
              ->mock();

    // Create test instance
    $config = \Mockery::mock('Ponticlaro\Bebop\Cms\Config')->shouldAllowMockingProtectedMethods();

    // Verify that Config::processHookEnvConfig is called correctly 
    $config->shouldReceive('processHookEnvConfig')
           ->withArgs([
             'unit_test_hook',
             'all', 
             [
               'requirement_1',
               'requirement_2',
             ]  
           ])
           ->once()
           ->mock();

    // Override Config::config property with mock
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Config', 'config');
    $prop_refl->setAccessible(true);
    $prop_refl->setValue($config, $coll_mock);

    // Get ::addConfigItem and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config', 'addConfigItem');
    $method_refl->setAccessible(true);

    // Test ::addConfigItem
    $method_refl->invokeArgs($config, [
      'unit_test_hook',
      'all',
      'types',
      $source_item
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config::addConfigItem
   * @depends create
   *
   * @param UnitTester $I Tester Module
   */
  public function addConfigItemForPresets(UnitTester $I)
  {
    // Source ConfigItem
    $source_item = \Mockery::mock('Ponticlaro\Bebop\Cms\Patterns\ConfigItemInterface');

    // Verify that ConfigItem::getUniqueId is called correctly
    $source_item->shouldReceive('getUniqueId')
                ->once()
                ->andReturn('unit_test')
                ->mock();

    // Verify that ConfigItem::getId is called correctly
    $source_item->shouldReceive('getId')
                ->once()
                ->andReturn('unit_test_preset')
                ->mock();

    // Mock config property
    $coll_mock = \Mockery::mock('Ponticlaro\Bebop\Common\Collection');

    // Verify that Collection::get is called correctly
    $coll_mock->shouldReceive('get')
              ->with("presets.all.types.unit_test_preset")
              ->once()
              ->andReturn(null)
              ->mock();
    
    // Verify that Collection::set is called correctly
    $coll_mock->shouldReceive('set')
              ->withArgs([
                "presets.all.types.unit_test_preset",
                $source_item
              ])
              ->once()
              ->mock();

    // Create test instance
    $config = \Mockery::mock('Ponticlaro\Bebop\Cms\Config');

    // Override Config::config property with mock
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Config', 'config');
    $prop_refl->setAccessible(true);
    $prop_refl->setValue($config, $coll_mock);

    // Get ::addConfigItem and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config', 'addConfigItem');
    $method_refl->setAccessible(true);

    // Test ::addConfigItem
    $method_refl->invokeArgs($config, [
      'presets',
      'all',
      'types',
      $source_item
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config::build
   * @depends create
   *
   * @param UnitTester $I Tester Module
   */
  public function build(UnitTester $I)
  {
    // Create test instance
    $config = \Mockery::mock('Ponticlaro\Bebop\Cms\Config')->shouldAllowMockingProtectedMethods();

    // Build ConfigItem mocks
    $config_item_1 = \Mockery::mock('Ponticlaro\Bebop\Cms\Patterns\ConfigItemInterface');
    $config_item_2 = \Mockery::mock('Ponticlaro\Bebop\Cms\Patterns\ConfigItemInterface');
    $config_item_3 = \Mockery::mock('Ponticlaro\Bebop\Cms\Patterns\ConfigItemInterface');
    $config_item_4 = \Mockery::mock('Ponticlaro\Bebop\Cms\Patterns\ConfigItemInterface');

    // Define build.all value
    $build_config = [
      'section_1' => [
        $config_item_1,
        $config_item_2,
      ],
      'section_2' => [
        $config_item_3,
        $config_item_4,
      ]
    ];

    // Mock config property
    $coll_mock = \Mockery::mock('Ponticlaro\Bebop\Common\Collection');

    // Verify that Collection::get is called correctly
    $coll_mock->shouldReceive('get')
              ->with("build.all")
              ->once()
              ->andReturn($build_config)
              ->mock();

    // Override Config::config property with mock
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Config', 'config');
    $prop_refl->setAccessible(true);
    $prop_refl->setValue($config, $coll_mock);

    // Verify that Config::runHooks is called correctly
    $config->shouldReceive('runHooks')
           ->once()
           ->mock();

    // Verify that Config::buildConfigItem is called correctly
    $config->shouldReceive('buildConfigItem')
           ->withArgs([
            'section_1',
            $config_item_1
           ])
           ->once()
           ->mock();

    $config->shouldReceive('buildConfigItem')
           ->withArgs([
            'section_1',
            $config_item_2
           ])
           ->once()
           ->mock();

    $config->shouldReceive('buildConfigItem')
           ->withArgs([
            'section_2',
            $config_item_3
           ])
           ->once()
           ->mock();

    $config->shouldReceive('buildConfigItem')
           ->withArgs([
            'section_2',
            $config_item_4
           ])
           ->once()
           ->mock();

    // Get Config::$already_built property and make it accessible
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Config', 'already_built');
    $prop_refl->setAccessible(true);
    
    // Verify that Config::$already_built have the expected value before invoking ::build
    $I->assertFalse($prop_refl->getValue($config));

    // Get ::build
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config', 'build');

    // Test ::build
    $method_refl->invoke($config);

    // Verify that Config::$already_built have the expected value after invoking ::build
    $I->assertTrue($prop_refl->getValue($config));

    // Test ::build with Config::$already_built as true
    $method_refl->invoke($config);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config::buildConfigItem
   * @depends create
   *
   * @param UnitTester $I Tester Module
   */
  public function buildConfigItem(UnitTester $I)
  {
    // Create test instance
    $env_manager = Test::double('Ponticlaro\Bebop\Common\EnvManager', [
      '__construct'   => null,
      'getCurrentKey' => 'unit_test_env'
    ]);

    Test::double('Ponticlaro\Bebop\Common\EnvManager', [
      'getInstance' => $env_manager->construct()
    ]);

    // Create test instance
    $config = \Mockery::mock('Ponticlaro\Bebop\Cms\Config')->shouldAllowMockingProtectedMethods();

    // Create ConfigItem mock
    $config_item = \Mockery::mock('Ponticlaro\Bebop\Cms\Patterns\ConfigItemInterface');

    // Verify that ConfigItem::getId is called correctly
    $config_item->shouldReceive('getId')
                ->once()
                ->andReturn('unit_test_item_id')
                ->mock();

    // Verify that ConfigItem::merge is called correctly
    $config_item->shouldReceive('merge')
                ->once()
                ->andReturn(\Mockery::self())
                ->mock();

    // Verify that ConfigItem::build is called correctly
    $config_item->shouldReceive('build')
                ->once()
                ->mock();

    // Mock config property
    $coll_mock = \Mockery::mock('Ponticlaro\Bebop\Common\Collection');

    // Verify that Collection::get is called correctly
    $coll_mock->shouldReceive('get')
              ->with("build.unit_test_env.unit_test_section.unit_test_item_id")
              ->once()
              ->andReturn(\Mockery::mock('Ponticlaro\Bebop\Cms\Patterns\ConfigItemInterface'))
              ->mock();

    // Override Config::config property with mock
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Config', 'config');
    $prop_refl->setAccessible(true);
    $prop_refl->setValue($config, $coll_mock);

    // Get ::buildConfigItem and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config', 'buildConfigItem');
    $method_refl->setAccessible(true);

    // Test ::buildConfigItem
    $method_refl->invokeArgs($config, [
      'unit_test_section',
      $config_item
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config::getConfigFromFile
   * @depends create
   *
   * @param UnitTester $I Tester Module
   */
  public function getConfigFromFileUsingAbsolutePath(UnitTester $I)
  {
    // Mock functions
    Test::func('Ponticlaro\Bebop\Cms', 'file_exists', true);
    Test::func('Ponticlaro\Bebop\Cms', 'file_get_contents', '{"config_section_1": ["config_item_1","config_item_2"]}');

    // Get ::getConfigFromFile and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config', 'getConfigFromFile');
    $method_refl->setAccessible(true);

    // Create test instance
    $config = \Mockery::mock('Ponticlaro\Bebop\Cms\Config');

    // Test ::getConfigFromFile
    $returned = $method_refl->invoke($config, '/path/to/existing/config.json');

    // Verify that ::getConfigFromFile returns expected value
    $I->assertEquals([
      'config_section_1' => [
        'config_item_1',
        'config_item_2',
      ]
    ], $returned);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config::getConfigFromFile
   * @depends create
   *
   * @param UnitTester $I Tester Module
   */
  public function getConfigFromFileUsingThemePath(UnitTester $I)
  {
    // Mock file_exists
    Test::func('Ponticlaro\Bebop\Cms', 'file_exists', function() {
      return func_get_arg(0) == '/path/to/theme/config.json' ? true : false;
    });

    // Mock file_get_contents
    Test::func('Ponticlaro\Bebop\Cms', 'file_get_contents', '{"config_section_2": ["config_item_3","config_item_4"]}');

    // Mock PathManager
    $path_manager = Test::double('Ponticlaro\Bebop\Common\PathManager', [
      '__construct' => null,
      'get'         => '/path/to/theme/config.json'
    ]);

    Test::double('Ponticlaro\Bebop\Common\PathManager', [
      'getInstance' => $path_manager->construct()
    ]);

    // Get ::getConfigFromFile and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config', 'getConfigFromFile');
    $method_refl->setAccessible(true);

    // Create test instance
    $config = \Mockery::mock('Ponticlaro\Bebop\Cms\Config');

    // Test ::getConfigFromFile
    $returned = $method_refl->invoke($config, '/absolute/path/to/config.json');

    // Verify that ::getConfigFromFile returns expected value
    $I->assertEquals([
      'config_section_2' => [
        'config_item_3',
        'config_item_4',
      ]
    ], $returned);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config::getConfigFromFile
   * @depends create
   *
   * @param UnitTester $I Tester Module
   */
  public function getConfigFromFileUsingUnreadablePath(UnitTester $I)
  {
    // Mock file_exists
    Test::func('Ponticlaro\Bebop\Cms', 'file_exists', false);

    // Mock PathManager
    $path_manager = Test::double('Ponticlaro\Bebop\Common\PathManager', [
      '__construct' => null,
      'get'         => '/path/to/theme/config.json'
    ]);

    Test::double('Ponticlaro\Bebop\Common\PathManager', [
      'getInstance' => $path_manager->construct()
    ]);

    // Get ::getConfigFromFile and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config', 'getConfigFromFile');
    $method_refl->setAccessible(true);

    // Create test instance
    $config = \Mockery::mock('Ponticlaro\Bebop\Cms\Config');

    // Test ::getConfigFromFile
    $returned = $method_refl->invoke($config, '/path/to/theme/config.json');

    // Verify that ::getConfigFromFile returns expected value
    $I->assertEquals([], $returned);
  }
}
