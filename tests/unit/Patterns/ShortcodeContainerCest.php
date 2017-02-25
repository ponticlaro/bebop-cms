<?php
namespace Patterns;

use \UnitTester;
use AspectMock\Test;

class ShortcodeContainerCest
{
  public function _before(UnitTester $I)
  {

  }

  public function _after(UnitTester $I)
  {
    Test::clean();
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainer::__construct
   * 
   * @param UnitTester $I Tester Module
   */
  public function create(UnitTester $I)
  {
    // Create test instance
    $container = new \BebopUnitTests\ShortcodeContainer();

    // Nothing to be tested for now
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainer::getTemplatePath
   * @covers  Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainer::setTemplatePath
   * 
   * @param UnitTester $I Tester Module
   */
  public function setAndGetTemplatePath(UnitTester $I)
  {
    // Mock is_readable to always return true
    Test::func('Ponticlaro\Bebop\Cms\Patterns', 'is_readable', true);

    // Create test instance
    $container = new \BebopUnitTests\ShortcodeContainer();

    // Verify default value for ::getTemplatePath
    $I->assertEquals($container->getTemplatePath(), '/path/to/template.php');

    // Test ::setTemplatePath
    $container->setTemplatePath('/path/to/template_alt.php');

    // Verify updated value for ::getTemplatePath
    $I->assertEquals($container->getTemplatePath(), '/path/to/template_alt.php');

    // Clean test for new mocks
    Test::clean();

    // Mock is_readable to always return false
    Test::func('Ponticlaro\Bebop\Cms\Patterns', 'is_readable', false);

    // Test ::setTemplatePath with unreadable path
    $I->expectException(\Exception::class, function() use($container) {
      $container->setTemplatePath('/path/to/template_alt_alt.php');
    });
    
    // Test ::setTemplatePath with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(\Exception::class, function() use($container, $bad_arg_val) {
        $container->setTemplatePath($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainer::register
   * 
   * @param UnitTester $I Tester Module
   */
  public function register(UnitTester $I)
  {
    // Mock Shortcode
    $shortcode_mock = Test::double('Ponticlaro\Bebop\Cms\Shortcode', [
      '__construct'     => null,
      'setDefaultAttrs' => true,
    ]);

    // Create test instance
    $container = new \BebopUnitTests\ShortcodeContainer();

    // Test ::register
    $container->register();

    // Get ::id property and make it accessible
    $prop_refl = new \ReflectionProperty('BebopUnitTests\ShortcodeContainer', 'id');
    $prop_refl->setAccessible(true);

    // Verify Shortcode::__construct was properly invoked
    $shortcode_mock->verifyInvokedOnce('__construct', [
      $prop_refl->getValue($container),
      [
        $container,
        'render'
      ]
    ]);

    // Verify Shortcode::setDefaultAttrs was properly invoked
    $shortcode_mock->verifyInvokedOnce('setDefaultAttrs', [
      [
        'key_1' => 'value_1',
        'key_2' => 'value_2',
      ]
    ]);
  }
}
