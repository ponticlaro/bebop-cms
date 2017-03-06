<?php
namespace Config;

use \UnitTester;
use AspectMock\Test;
use Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection;

class ScriptsConfigSectionCest
{
  public function _before(UnitTester $I)
  {
  }

  public function _after(UnitTester $I)
  {
    Test::clean();
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection::getSortedConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function getSortedConfig(UnitTester $I)
  {
    // Create test instance
    $section = new ScriptsConfigSection();

    // Get ::sortConfig and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection', 'getSortedConfig');
    $method_refl->setAccessible(true);

    // Test ::getSortedConfig
    $sorted_config = $method_refl->invokeArgs(null, [
      [
        'deregister' => ['dummy_deregister_data'],
        'dequeue'    => ['dummy_dequeue_data'],
        'register'   => ['dummy_register_data'],
        'enqueue'    => ['dummy_enqueue_data'],
      ]
    ]);

    // Verify that returned data matches expected value
    $I->assertEquals($sorted_config, [
      'register'   => ['dummy_register_data'],
      'enqueue'    => ['dummy_enqueue_data'],
      'deregister' => ['dummy_deregister_data'],
      'dequeue'    => ['dummy_dequeue_data'],
    ]);
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection::handleAction
   * 
   * @param UnitTester $I Tester Module
   */
  public function handleRegisterAction(UnitTester $I)
  {
    // Create ScriptsConfigSection mock
    $section_mock = Test::double('Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection', [
      'handleItemRegistration' => null,
    ]);

    // Create test instance
    $section = new ScriptsConfigSection();

    // Get ::handleAction and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection', 'handleAction');
    $method_refl->setAccessible(true);

    // Test ::handleAction
    $method_refl->invokeArgs($section, [
      'register',
      [
        [
          'handle' => 'unit_test_handle',
          'src'    => '/unit/test',
          'deps'   => [
            'dep_1',
            'dep_2',
          ],
        ],
        [
          'handle' => 'unit_test_handle_2',
          'src'    => '/unit/test_2',
          'deps'   => [
            'dep_3',
            'dep_4',
          ],
        ]
      ]
    ]);

    // Verify ::handleRegistration was invoked correctly
    $section_mock->verifyInvokedOnce('handleItemRegistration', [
      [
        'handle' => 'unit_test_handle',
        'src'    => '/unit/test',
        'deps'   => [
          'dep_1',
          'dep_2',
        ]
      ]
    ]);

    // Verify ::handleRegistration was invoked correctly
    $section_mock->verifyInvokedOnce('handleItemRegistration', [
      [
        'handle' => 'unit_test_handle_2',
        'src'    => '/unit/test_2',
        'deps'   => [
          'dep_3',
          'dep_4',
        ]
      ]
    ]);
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection::handleAction
   * 
   * @param UnitTester $I Tester Module
   */
  public function handleDeregisterAction(UnitTester $I)
  {
    // Create ScriptsConfigSection mock
    $section_mock = Test::double('Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection', [
      'handleActionOnHook' => null,
    ]);

    // Create test instance
    $section = new ScriptsConfigSection();

    // Get ::handleAction and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection', 'handleAction');
    $method_refl->setAccessible(true);

    // Test ::handleAction
    $method_refl->invokeArgs($section, [
      'deregister',
      [
        'unit_test_hook_1' => [
          'script_1',
          'script_2'
        ],
        'unit_test_hook_2' => [
          'script_3',
          'script_4'
        ]
      ]
    ]);

    // Verify ::handleActionOnHook was invoked correctly
    $section_mock->verifyInvokedOnce('handleActionOnHook', [
      'deregister',
      'unit_test_hook_1',
      [
        'script_1',
        'script_2'
      ]
    ]);

    // Verify ::handleActionOnHook was invoked correctly
    $section_mock->verifyInvokedOnce('handleActionOnHook', [
      'deregister',
      'unit_test_hook_2',
      [
        'script_3',
        'script_4'
      ]
    ]);
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection::handleAction
   * 
   * @param UnitTester $I Tester Module
   */
  public function handleEnqueueAction(UnitTester $I)
  {
    // Create ScriptsConfigSection mock
    $section_mock = Test::double('Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection', [
      'handleActionOnHook' => null,
    ]);

    // Create test instance
    $section = new ScriptsConfigSection();

    // Get ::handleAction and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection', 'handleAction');
    $method_refl->setAccessible(true);

    // Test ::handleAction
    $method_refl->invokeArgs($section, [
      'enqueue',
      [
        'unit_test_hook_1' => [
          'script_1',
          'script_2'
        ],
        'unit_test_hook_2' => [
          'script_3',
          'script_4'
        ]
      ]
    ]);

    // Verify ::handleActionOnHook was invoked correctly
    $section_mock->verifyInvokedOnce('handleActionOnHook', [
      'enqueue',
      'unit_test_hook_1',
      [
        'script_1',
        'script_2'
      ]
    ]);

    // Verify ::handleActionOnHook was invoked correctly
    $section_mock->verifyInvokedOnce('handleActionOnHook', [
      'enqueue',
      'unit_test_hook_2',
      [
        'script_3',
        'script_4'
      ]
    ]);
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection::handleAction
   * 
   * @param UnitTester $I Tester Module
   */
  public function handleDequeueAction(UnitTester $I)
  {
    // Create ScriptsConfigSection mock
    $section_mock = Test::double('Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection', [
      'handleActionOnHook' => null,
    ]);

    // Create test instance
    $section = new ScriptsConfigSection();

    // Get ::handleAction and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection', 'handleAction');
    $method_refl->setAccessible(true);

    // Test ::handleAction
    $method_refl->invokeArgs($section, [
      'dequeue',
      [
        'unit_test_hook_1' => [
          'script_1',
          'script_2'
        ],
        'unit_test_hook_2' => [
          'script_3',
          'script_4'
        ]
      ]
    ]);

    // Verify ::handleActionOnHook was invoked correctly
    $section_mock->verifyInvokedOnce('handleActionOnHook', [
      'dequeue',
      'unit_test_hook_1',
      [
        'script_1',
        'script_2'
      ]
    ]);

    // Verify ::handleActionOnHook was invoked correctly
    $section_mock->verifyInvokedOnce('handleActionOnHook', [
      'dequeue',
      'unit_test_hook_2',
      [
        'script_3',
        'script_4'
      ]
    ]);
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection::handleItemRegistration
   * 
   * @param UnitTester $I Tester Module
   */
  public function handleItemRegistration(UnitTester $I)
  {
    // Create test instance
    $section = new ScriptsConfigSection();

    // Get items property and make it accessible
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection', 'items');
    $prop_refl->setAccessible(true);

    // Get ::handleItemRegistration and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection', 'handleItemRegistration');
    $method_refl->setAccessible(true);

    // Test ::handleItemRegistration with config without 'handle'
    $method_refl->invoke($section, []);

    // Verify items property matches expected value
    $I->assertEquals($prop_refl->getValue($section), []);

    // Test ::handleItemRegistration
    $method_refl->invokeArgs($section, [
      [
        'handle' => 'unit_test_handle',
        'src'    => '/unit/test',
        'deps'   => [
          'dep_1',
          'dep_2',
        ]
      ]
    ]);

    // Verify items property matches expected value
    $I->assertEquals($prop_refl->getValue($section), [
      'unit_test_handle' => [
        'handle' => 'unit_test_handle',
        'src'    => '/unit/test',
        'deps'   => [
          'dep_1',
          'dep_2',
        ]
      ]
    ]);

    // Test ::handleItemRegistration, making sure registration overwriting is working
    $method_refl->invokeArgs($section, [
      [
        'handle' => 'unit_test_handle',
        'src'    => '/unit/test_alt',
        'deps'   => [
          'dep_3',
          'dep_4',
        ]
      ]
    ]);

    // Verify items property matches expected value
    $I->assertEquals($prop_refl->getValue($section), [
      'unit_test_handle' => [
        'handle' => 'unit_test_handle',
        'src'    => '/unit/test_alt',
        'deps'   => [
          'dep_3',
          'dep_4',
        ]
      ]
    ]);
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection::handleActionOnHook
   * 
   * @param UnitTester $I Tester Module
   */
  public function handleActionOnHook(UnitTester $I)
  {
    // Create ScriptsConfigSection mock
    $section_mock = Test::double('Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection', [
      'addActionToHook' => function() {
        return func_get_arg(0);
      },
    ]);

    // Create test instance
    $section = new ScriptsConfigSection();

    // Get ::handleActionOnHook and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection', 'handleActionOnHook');
    $method_refl->setAccessible(true);

    // Test ::handleItemRegistration
    $method_refl->invokeArgs($section, [
      'enqueue',
      'unit_test_hook',
      [
        'unit_test_handle_1',
        'unit_test_handle_2'
      ]
    ]);

    // Verify ::addActionToHook was invoked correctly
    $section_mock->verifyInvokedOnce('addActionToHook', [
      [
        'handle' => 'unit_test_handle_1'
      ],
      'unit_test_hook',
      'register',
    ]);

    $section_mock->verifyInvokedOnce('addActionToHook', [
      [
        'handle' => 'unit_test_handle_1'
      ],
      'unit_test_hook',
      'enqueue',
    ]);

    $section_mock->verifyInvokedOnce('addActionToHook', [
      [
        'handle' => 'unit_test_handle_2'
      ],
      'unit_test_hook',
      'register',
    ]);

    $section_mock->verifyInvokedOnce('addActionToHook', [
      [
        'handle' => 'unit_test_handle_2'
      ],
      'unit_test_hook',
      'enqueue',
    ]);

    // Get items property and make it accessible
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection', 'items');
    $prop_refl->setAccessible(true);

    // Verify items property matches expected value
    $I->assertEquals($prop_refl->getValue($section), [
      'unit_test_handle_1' => [
        'handle' => 'unit_test_handle_1'
      ],
      'unit_test_handle_2' => [
        'handle' => 'unit_test_handle_2'
      ]
    ]);

    // Test ::handleActionOnHook
    $bad_args = [
      [null, 'string'],
      [false, 'string'],
      [true, 'string'],
      [0, 'string'],
      [1, 'string'],
      [[1], 'string'],
      [new \stdClass, 'string'],
      ['string', null],
      ['string', false],
      ['string', true],
      ['string', 0],
      ['string', 1],
      ['string', [1]],
      ['string', new \stdClass],
    ];

    foreach ($bad_args as $bad_args_set) {

      // Check if exception is thrown with bad arguments
      $I->expectException(\Exception::class, function() use($method_refl, $section, $bad_args_set) {
        $method_refl->invokeArgs($section, $bad_args_set);
      });
    }  
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection::addActionToHook
   * 
   * @param UnitTester $I Tester Module
   */
  public function addActionToHook(UnitTester $I)
  {
    // Create test instance
    $section = new ScriptsConfigSection();

    // Get ::addActionToHook and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection', 'addActionToHook');
    $method_refl->setAccessible(true);

    // Define start config
    $config = [
      'handle' => 'unit_test_handle_1'
    ];

    // Test ::addActionToHook by adding register action on 'unit_test_hook_1'
    $config = $method_refl->invokeArgs($section, [
      $config,
      'unit_test_hook_1',
      'register'
    ]);

    // Test ::addActionToHook by adding enqueue action on 'unit_test_hook_1'
    $config = $method_refl->invokeArgs($section, [
      $config,
      'unit_test_hook_1',
      'enqueue'
    ]);

    // Test ::addActionToHook by adding deregister action on 'unit_test_hook_2'
    $config = $method_refl->invokeArgs($section, [
      $config,
      'unit_test_hook_2',
      'deregister'
    ]);

    // Test ::addActionToHook by adding deregister action on 'unit_test_hook_3'
    $config = $method_refl->invokeArgs($section, [
      $config,
      'unit_test_hook_3',
      'dequeue'
    ]);

    // Verify that final config matches expected value
    $I->assertEquals($config, [
      'handle' => 'unit_test_handle_1',
      'hooks' => [
        'unit_test_hook_1' => [
          'register',
          'enqueue',
        ],
        'unit_test_hook_2' => [
          'deregister'
        ],
        'unit_test_hook_3' => [
          'dequeue'
        ],
      ]
    ]);
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection::getItems
   * 
   * @param UnitTester $I Tester Module
   */
  public function getItems(UnitTester $I)
  {
    // Create ScriptsConfigSection mock
    $section_mock = Test::double('Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection', [
      'getSortedConfig' => function($config) {
        return $config;
      },
      'handleAction' => null
    ]);

    // Set config
    $config = [
      'register' => [
        [
          'handle' => 'unit_test_handle',
          'src'    => '/unit/test',
          'deps'   => [
            'dep_1',
            'dep_2',
          ]
        ]
      ],
      'enqueue' => [
        'unit_test_hook_1' => [
          'unit_test_script_1',
          'unit_test_script_2',
        ],
        'unit_test_hook_2' => [
          'unit_test_script_1',
          'unit_test_script_2',
        ]
      ],
      'deregister' => [
        'unit_test_hook_1' => [
          'unit_test_script_1',
          'unit_test_script_2',
        ],
        'unit_test_hook_2' => [
          'unit_test_script_1',
          'unit_test_script_2',
        ]
      ],
      'dequeue' => [
        'unit_test_hook_1' => [
          'unit_test_script_1',
          'unit_test_script_2',
        ],
        'unit_test_hook_2' => [
          'unit_test_script_1',
          'unit_test_script_2',
        ]
      ]
    ];

    // Create test instance
    $section = new ScriptsConfigSection($config);

    // Test ::getItems
    $section->getItems();

    // Verify that ::getSortedConfig was invoked correctly
    $section_mock->verifyInvokedOnce('getSortedConfig', [
      $config
    ]);

    // Verify that ::handleAction was invoked correctly
    $section_mock->verifyInvokedOnce('handleAction', [
      'register',
      $config['register']
    ]);

    $section_mock->verifyInvokedOnce('handleAction', [
      'enqueue',
      $config['enqueue']
    ]);

    $section_mock->verifyInvokedOnce('handleAction', [
      'deregister',
      $config['deregister']
    ]);

    $section_mock->verifyInvokedOnce('handleAction', [
      'dequeue',
      $config['dequeue']
    ]);

    // Get items property and make it accessible
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection', 'items');
    $prop_refl->setAccessible(true);

    // Verify items property matches expected value
    $I->assertEquals($prop_refl->getValue($section), []);
  }
}
