<?php
namespace Config;

use \UnitTester;
use AspectMock\Test;
use Ponticlaro\Bebop\Cms\Config\StyleConfigItem;

class StyleConfigItemCest
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
    $this->m['Script'] = Test::double('Ponticlaro\Bebop\ScriptsLoader\Css\Script', [
      '__construct'         => null,
      'enqueueAsDependency' => null,
      'setAsync'            => null,
      'setDefer'            => null,
    ]);

    // ScriptsHook mock
    $this->m['ScriptsHook'] = Test::double('Ponticlaro\Bebop\ScriptsLoader\Css\ScriptsHook', [
      '__construct' => null,
      'getFile'     => $this->m['Script']->construct('unit_test_handle', '/unit/test/src.css'),
      'register'    => null,
      'deregister'  => null,
      'enqueue'     => null,
      'dequeue'     => null,
    ]);

    // ScriptsHook Instance
    $this->m['ScriptsHook_Instance'] = $this->m['ScriptsHook']->construct('unit_test_id', 'unit_test_hook');

    // ScriptsManager mock
    $this->m['ScriptsManager'] = Test::spec('Ponticlaro\Bebop\ScriptsLoader\Css', [
      '__construct' => null,
      'getHook'     => $this->m['ScriptsHook_Instance']
    ]);

    // ScriptsManager::getInstance mock
    Test::double('Ponticlaro\Bebop\ScriptsLoader\Css', [
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
   * @covers Ponticlaro\Bebop\Cms\Config\StyleConfigItem::__construct
   * @covers Ponticlaro\Bebop\Cms\Config\StyleConfigItem::isValid
   * 
   * @param UnitTester $I Tester Module
   */
  public function minimumValidConfig(UnitTester $I)
  {
    // Create StyleConfigItem mock
    $item_mock = Test::double('Ponticlaro\Bebop\Cms\Config\StyleConfigItem', [
      'setEventEmitter' => null,
    ]);

    // Create test instance
    $item = new StyleConfigItem([
      'handle' => 'unit_test',
    ]);

    // Test ::isValid()
    $I->assertTrue($item->isValid());
  
    // Verify EventEmitter::subscribe was invoked correctly
    $this->m['EventEmitter']->verifyInvokedOnce('subscribe', [
      'cms.config.styles.unit_test',
      [
        $item,
        'consumeEvent'
      ]
    ]);

    // Verify StyleConfigItem::setEventEmitter was invoked correctly
    $item_mock->verifyInvokedOnce('setEventEmitter', [
      $this->m['EventEmitter_Instance']
    ]);
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\StyleConfigItem::handleAction
   * 
   * @param UnitTester $I Tester Module
   */
  public function register(UnitTester $I)
  {
    // Create StyleConfigItem mock
    $item_mock = Test::double('Ponticlaro\Bebop\Cms\Config\StyleConfigItem', [
      'ensureDependenciesAreEnqueued' => null
    ]);

    // Create test instance
    $item = new StyleConfigItem([
      'handle' => 'unit_test_script',
      'src'    => '/unit/test',
      'deps'   => [
        'underscore',
        'backbone',
        'jquery',
      ],
      'version' => 'unit_test_version',
      'media'   => 'screen',
    ]);

    // Get ::handleAction and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config\StyleConfigItem', 'handleAction');
    $method_refl->setAccessible(true);

    // Test ::handleAction
    $method_refl->invokeArgs($item, [
      'unit_test_hook',
      'register',
    ]);

    // Verify that ScriptsHook::getHook is invoked correctly
    $this->m['ScriptsManager']->verifyInvokedOnce('getHook', [
      'unit_test_hook'
    ]);

    // Verify that ::ensureDependenciesAreEnqueued is invoked correctly
    $item_mock->verifyInvokedOnce('ensureDependenciesAreEnqueued', [
      'unit_test_hook'
    ]);

    // Verify that ScriptsHook::getHook is invoked correctly
    $this->m['ScriptsHook']->verifyInvokedOnce('register', [
      'unit_test_script',
      '/unit/test',
      [
        'underscore',
        'backbone',
        'jquery',
      ],
      'unit_test_version',
      'screen',
    ]);
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\StyleConfigItem::handleAction
   * 
   * @param UnitTester $I Tester Module
   */
  public function deregister(UnitTester $I)
  {
    // Create test instance
    $item = new StyleConfigItem([
      'handle' => 'unit_test_script'
    ]);

    // Get ::handleAction and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config\StyleConfigItem', 'handleAction');
    $method_refl->setAccessible(true);

    // Test ::handleAction
    $method_refl->invokeArgs($item, [
      'unit_test_hook',
      'deregister',
    ]);

    // Verify that ScriptsManager::getHook is invoked correctly
    $this->m['ScriptsManager']->verifyInvokedOnce('getHook', [
      'unit_test_hook'
    ]);

    // Verify that ScriptsHook::deregister is invoked correctly
    $this->m['ScriptsHook']->verifyInvokedOnce('deregister');
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\StyleConfigItem::handleAction
   * 
   * @param UnitTester $I Tester Module
   */
  public function enqueue(UnitTester $I)
  {
    // Create StyleConfigItem mock
    $item_mock = Test::double('Ponticlaro\Bebop\Cms\Config\StyleConfigItem', [
      'ensureDependenciesAreEnqueued' => null
    ]);

    // Create test instance
    $item = new StyleConfigItem([
      'handle' => 'unit_test_script'
    ]);

    // Get ::handleAction and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config\StyleConfigItem', 'handleAction');
    $method_refl->setAccessible(true);

    // Test ::handleAction
    $method_refl->invokeArgs($item, [
      'unit_test_hook',
      'enqueue',
    ]);

    // Verify that ScriptsManager::getHook is invoked correctly
    $this->m['ScriptsManager']->verifyInvokedOnce('getHook', [
      'unit_test_hook'
    ]);

    // Verify that ScriptsHook::enqueue is invoked correctly
    $this->m['ScriptsHook']->verifyInvokedOnce('enqueue');
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\StyleConfigItem::handleAction
   * 
   * @param UnitTester $I Tester Module
   */
  public function dequeue(UnitTester $I)
  {
    // Create StyleConfigItem mock
    $item_mock = Test::double('Ponticlaro\Bebop\Cms\Config\StyleConfigItem', [
      'ensureDependenciesAreEnqueued' => null
    ]);

    // Create test instance
    $item = new StyleConfigItem([
      'handle' => 'unit_test_script'
    ]);

    // Get ::handleAction and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config\StyleConfigItem', 'handleAction');
    $method_refl->setAccessible(true);

    // Test ::handleAction
    $method_refl->invokeArgs($item, [
      'unit_test_hook',
      'dequeue',
    ]);

    // Verify that ScriptsManager::getHook is invoked correctly
    $this->m['ScriptsManager']->verifyInvokedOnce('getHook', [
      'unit_test_hook'
    ]);

    // Verify that ScriptsHook::dequeue is invoked correctly
    $this->m['ScriptsHook']->verifyInvokedOnce('dequeue');
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\StyleConfigItem::ensureDependenciesAreEnqueued
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function ensureDependenciesAreEnqueued(UnitTester $I, $scenario)
  {
    // Create test instance
    $item = new StyleConfigItem([
      'handle' => 'unit_test',
      'deps'  => [
        'css_1',
        'css_2',
        'css_3',
      ]
    ]);

    // Get ::setAsync and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Config\StyleConfigItem', 'ensureDependenciesAreEnqueued');
    $method_refl->setAccessible(true);

    // Test ::ensureDependenciesAreEnqueued
    $method_refl->invokeArgs($item, [
      $this->m['ScriptsHook']->construct('unit_test_id', 'unit_test_hook')
    ]);  

    // Verify that EventEmitter::publish is invoked correctly
    $this->m['EventEmitter']->verifyInvokedMultipleTimes('publish', 3);
  }
}
