<?php
namespace Patterns;

use \UnitTester;
use AspectMock\Test;
use Ponticlaro\Bebop\Cms\Config\ScriptConfigItem;

class ScriptConfigItemCest
{
  /**
   * List of mocks
   * 
   * @var array
   */
  private $m = [];

  public function _before(UnitTester $I)
  {
    // Utils mock
    $this->m['Utils'] = Test::double('Ponticlaro\Bebop\Common\Utils', [
      'slugify' => function() {
        return strtolower(func_get_arg(0));
      }
    ]);

    // Script mock
    $this->m['Script'] = Test::double('Ponticlaro\Bebop\ScriptsLoader\Js\Script', [
      '__construct'         => null,
      'enqueueAsDependency' => null,
      'setAsync'            => null,
      'setDefer'            => null,
    ]);

    // ScriptsHook mock
    $this->m['ScriptsHook'] = Test::double('Ponticlaro\Bebop\ScriptsLoader\Js\ScriptsHook', [
      '__construct' => null,
      'getFile'     => $this->m['Script']->construct('unit_test_handle', '/unit/test/src.js'),
      'register'    => null,
      'deregister'  => null,
      'enqueue'     => null,
      'dequeue'     => null,
    ]);

    // ScriptsManager mock
    $this->m['ScriptsManager'] = Test::spec('Ponticlaro\Bebop\ScriptsLoader\Js', [
      '__construct' => null,
      'getHook'     => $this->m['ScriptsHook']->construct('unit_test_id', 'unit_test_hook')
    ]);

    // ScriptsManager::getInstance mock
    Test::double('Ponticlaro\Bebop\ScriptsLoader\Js', [
      'getInstance' => $this->m['ScriptsManager']->construct(),
    ]);

    // EventEmitter mock
    $this->m['EventEmitter'] = Test::double('Ponticlaro\Bebop\Common\EventEmitter', [
      '__construct' => null,
      'publish'     => true,
      'subscribe'   => true,
    ]);

    // Create EventEmitter instance
    $this->m['EventEmitter_Instance'] = $this->m['EventEmitter']->construct();

    // Mock EventEmitter::getInstance and EventEmitter::subscribe
    Test::double('Ponticlaro\Bebop\Common\EventEmitter', [
      'getInstance' => $this->m['EventEmitter_Instance'],
      'subscribe'   => $this->m['EventEmitter_Instance'],
    ]);

    // EventMessage mock
    $this->m['EventMessage'] = Test::double('Ponticlaro\Bebop\Common\EventMessage', [
      '__construct' => true,
      'getAction'   => 'enqueue_as_dependency',
      'getData'     => [
        'hooks' => [
          'unit_test_hook_1',
          'unit_test_hook_2',
        ]
      ]
    ]);

    // EventMessage instance
    $this->m['EventMessage_Instance'] = $this->m['EventMessage']->construct('unit_test_action', []);
  }

  public function _after(UnitTester $I)
  {
    Test::clean();
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem::getIdKey
   * 
   * @param UnitTester $I Tester Module
   */
  public function getIdKey(UnitTester $I)
  {
    // Test ::getIdKey
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem', 'getIdKey');
    
    $I->assertEquals($method_refl->invoke(null), 'handle');
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem::__construct
   * @covers Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem::isValid
   * 
   * @param UnitTester $I Tester Module
   */
  public function minimumValidConfig(UnitTester $I)
  {
    // Create test instance
    $item = new ScriptConfigItem([
      'handle' => 'unit_test',
    ]);

    // Test ::isValid
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem', 'isValid');
    
    $I->assertTrue($method_refl->invoke($item));
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem::isValid
   * @depends minimumValidConfig
   *
   * @param UnitTester $I Tester Module
   */
  public function validHandle(UnitTester $I)
  {
    $args_list = [
      'unit_test',
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new ScriptConfigItem([
        'handle' => $arg,
      ]);

      // Test ::isValid
      $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem', 'isValid');
      
      $I->assertTrue($method_refl->invoke($item));
    }
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem::isValid
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function invalidHandle(UnitTester $I)
  {
    $args_list = [
      null, false, true, 0, 1, [1], new \stdClass
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new ScriptConfigItem([
        'handle' => $arg,
      ]);

      // Test ::isValid
      $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem', 'isValid');
      
      $I->assertFalse($method_refl->invoke($item));
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem::isValid
   * @depends minimumValidConfig
   *
   * @param UnitTester $I Tester Module
   */
  public function validSource(UnitTester $I)
  {
    $args_list = [
      '/unit/test.js',
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new ScriptConfigItem([
        'handle' => 'unit_test',
        'src'    => $arg,
      ]);

      // Test ::isValid
      $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem', 'isValid');
      
      $I->assertTrue($method_refl->invoke($item));
    }
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem::isValid
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function invalidSource(UnitTester $I)
  {
    $args_list = [
      true, 1, [1], new \stdClass
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new ScriptConfigItem([
        'handle' => 'unit_test',
        'src'    => $arg,
      ]);

      // Test ::isValid
      $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem', 'isValid');
      
      $I->assertFalse($method_refl->invoke($item));
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem::isValid
   * @depends minimumValidConfig
   *
   * @param UnitTester $I Tester Module
   */
  public function validHooks(UnitTester $I)
  {
    $args_list = [
      ['front', 'back', 'login'],
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new ScriptConfigItem([
        'handle' => 'unit_test',
        'hooks'  => $arg,
      ]);

      // Test ::isValid
      $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem', 'isValid');
      
      $I->assertTrue($method_refl->invoke($item));
    }
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem::isValid
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function invalidHooks(UnitTester $I)
  {
    $args_list = [
      true, 1, 'string', new \stdClass
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new ScriptConfigItem([
        'handle' => 'unit_test',
        'hooks'  => $arg,
      ]);

      // Test ::isValid
      $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem', 'isValid');
      
      $I->assertFalse($method_refl->invoke($item));
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem::build
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function build(UnitTester $I)
  {
    // Create ScriptConfigItem mock
    $item_mock = Test::double('Ponticlaro\Bebop\Cms\Config\ScriptConfigItem', [
      'handleAction' => true,
    ]);

    // Create test instance
    $item = new ScriptConfigItem([
      'handle' => 'unit_test',
      'hooks' => [
        'unit_test_hook_1' => [
          'register',
          'enqueue',
        ],
        'unit_test_hook_2' => [
          'deregister',
          'dequeue',
        ]
      ]
    ]);

    // Test ::build
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem', 'build');
    $method_refl->invoke($item);

    // Verify ScriptConfigItem::handleAction was invoked correctly
    $item_mock->verifyInvokedOnce('handleAction', [
      'unit_test_hook_1',
      'register',
    ]);

    $item_mock->verifyInvokedOnce('handleAction', [
      'unit_test_hook_1',
      'enqueue',
    ]);

    $item_mock->verifyInvokedOnce('handleAction', [
      'unit_test_hook_2',
      'deregister',
    ]);

    $item_mock->verifyInvokedOnce('handleAction', [
      'unit_test_hook_2',
      'dequeue',
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem::consumeEvent
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function consumeEvent(UnitTester $I)
  {
    // Create ScriptConfigItem mock
    $item_mock = Test::double('Ponticlaro\Bebop\Cms\Config\ScriptConfigItem', [
      'enqueueAsDependency' => null,
    ]);

    // Create test instance
    $item = new ScriptConfigItem([
      'handle' => 'unit_test'
    ]);

    // Test ::consumeEvent
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem', 'consumeEvent');
    $method_refl->invokeArgs($item, [
      $this->m['EventMessage_Instance']
    ]);

    // Verify that ::enqueueAsDependency was invoked correctly
    $item_mock->verifyInvokedOnce('enqueueAsDependency', [
      $this->m['EventMessage_Instance']
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem::enqueueAsDependency
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function enqueueAsDependency(UnitTester $I)
  {
    // Mock ::handleAction
    $item_mock = Test::double('Ponticlaro\Bebop\Cms\Config\ScriptConfigItem', [
      'handleAction' => true
    ]);

    // Create test instance
    $item = new ScriptConfigItem([
      'handle' => 'unit_test',
    ]);

    // Get ::enqueueAsDependency and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem', 'enqueueAsDependency');
    $method_refl->setAccessible(true);
    
    // Test ::enqueueAsDependency
    $method_refl->invokeArgs($item, [
      $this->m['EventMessage']->construct('unit_test_action', [])
    ]);

    // Verify that ::handleAction was invoked correctly
    $item_mock->verifyInvokedOnce('handleAction', [
      'unit_test_hook_1',
      'register',
    ]);

    $item_mock->verifyInvokedOnce('handleAction', [
      'unit_test_hook_1',
      'enqueue',
    ]);
  
    $item_mock->verifyInvokedOnce('handleAction', [
      'unit_test_hook_2',
      'register',
    ]);

    $item_mock->verifyInvokedOnce('handleAction', [
      'unit_test_hook_2',
      'enqueue',
    ]);
  }
}
