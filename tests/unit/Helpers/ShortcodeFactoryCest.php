<?php
namespace Helpers;

use \UnitTester;
use AspectMock\Test;
use Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory;

class ShortcodeFactoryCest
{
  /**
   * List of mocks
   * 
   * @var array
   */
  private $m = [];

  public function _before(UnitTester $I)
  {
    // Mocks
    $this->m['ShortcodeContainer'] = Test::double('\UnitTestShortcodeContainer', [
      '__construct' => null,
    ]);
  }

  public function _after(UnitTester $I)
  {
    Test::clean();
  }

  /**
   * @author cristianobaptista
   * 
   * @param UnitTester $I Tester Module
   */
  public function checkDefaultManufacturables(UnitTester $I)
  {
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory', 'manufacturable');
    $prop_refl->setAccessible(true);
    $prop_value = $prop_refl->getValue();

    $I->assertEquals(array_keys($prop_value), [
      'facebook_post',
      'faq_list',
      'form',
      'formstack',
      'gallery',
      'google_calendar',
      'google_map',
      'image',
      'pardot_form',
      'quote',
      'soundcloud',
      'tweet',
      'video', 
      'vimeo',
      'youtube',
    ]);
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory::set
   * 
   * @param UnitTester $I Tester Module
   */
  public function set(UnitTester $I)
  {
    // Test ::set
    ShortcodeFactory::set('unit_test_shortcode', 'UnitTestShortcodeContainer');

    // Get manufacturable property and make it accessible
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory', 'manufacturable');
    $prop_refl->setAccessible(true);
    $prop_value = $prop_refl->getValue();

    $I->assertTrue(isset($prop_value['unit_test_shortcode']), 'Manufacturable was added');
    $I->assertEquals($prop_value['unit_test_shortcode'], 'UnitTestShortcodeContainer', 'Manufacturable class matches');

    // Test ::set with bad arguments
    $bad_args = [
      [null, 'string'],
      [0, 'string'],
      [1, 'string'],
      [[1], 'string'],
      [new \stdClass, 'string'],
      ['string', null],
      ['string', 0],
      ['string', 1],
      ['string', [1]],
      ['string', new \stdClass],
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(\Exception::class, function() use($bad_arg_val) {
        ShortcodeFactory::set($bad_arg_val[0], $bad_arg_val[1]);
      });
    }  
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory::remove
   * 
   * @param UnitTester $I Tester Module
   */
  public function remove(UnitTester $I)
  {
    // Get manufacturable property and make it accessible
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory', 'manufacturable');
    $prop_refl->setAccessible(true);
    $prop_value = $prop_refl->getValue();

    // Set value for test on $manufacturables property
    $prop_value['unit_test_shortcode'] = 'UnitTestShortcodeContainer';
    $prop_refl->setValue($prop_value);

    // Test ::remove
    ShortcodeFactory::remove('unit_test_shortcode');

    // Verify mock manufacturable was removed
    $prop_value = $prop_refl->getValue();

    $I->assertTrue(!isset($prop_value['unit_test_shortcode']), 'Manufacturable was removed');

    // Test ::remove with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(\Exception::class, function() use($bad_arg_val) {
        ShortcodeFactory::remove($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory::canManufacture
   * @depends checkDefaultManufacturables
   * 
   * @param UnitTester $I Tester Module
   */
  public function canManufacture(UnitTester $I)
  {
    // Get manufacturable property and make it accessible
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory', 'manufacturable');
    $prop_refl->setAccessible(true);
    $prop_value = $prop_refl->getValue();

    // Set value for test on $manufacturables property
    $prop_value['unit_test_shortcode'] = 'UnitTestShortcodeContainer';
    $prop_refl->setValue($prop_value);

    // Test ::canManufacture with defined manufacturable id
    $I->assertTrue(ShortcodeFactory::canManufacture('unit_test_shortcode'));

    // Test ::canManufacture with undefined manufacturable id
    $I->assertFalse(ShortcodeFactory::canManufacture('___undefined_manufacturable_id___'));

    // Test ::canManufacture with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(\Exception::class, function() use($bad_arg_val) {
        ShortcodeFactory::canManufacture($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory::getInstanceId
   * @depends checkDefaultManufacturables
   * 
   * @param UnitTester $I Tester Module
   */
  public function getInstanceId(UnitTester $I)
  {
    // Create shortcode mock instance
    $mock_shortcode = $this->m['ShortcodeContainer']->construct();

    // Get manufacturable property and make it accessible
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory', 'manufacturable');
    $prop_refl->setAccessible(true);
    $prop_value = $prop_refl->getValue();

    // Set value for test on $manufacturables property
    $prop_value['unit_test_shortcode'] = 'UnitTestShortcodeContainer';
    $prop_refl->setValue($prop_value);

    // Test ::canManufacture with defined manufacturable id
    $I->assertEquals('unit_test_shortcode', ShortcodeFactory::getInstanceId($mock_shortcode));

    // Test ::canManufacture with undefined manufacturable id
    $I->assertNull(ShortcodeFactory::getInstanceId('___undefined_manufacturable_id___'));
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory::create
   * @depends checkDefaultManufacturables
   * 
   * @param UnitTester $I Tester Module
   */
  public function create(UnitTester $I)
  {
    // Create shortcode mock instance
    $shortcode_instance = $this->m['ShortcodeContainer']->construct();

    // Create shortcode factory mock
    $mock_factory = Test::double('Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory', [
      '__createInstance' => $shortcode_instance,
    ]);

    // Get manufacturable property and make it accessible
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory', 'manufacturable');
    $prop_refl->setAccessible(true);
    $prop_value = $prop_refl->getValue();

    // Set value for test on $manufacturables property
    $prop_value['unit_test_shortcode'] = '\UnitTestShortcodeContainer';
    $prop_refl->setValue($prop_value);

    // Test ::create with defined manufacturable id
    $returned = ShortcodeFactory::create('unit_test_shortcode', [
      'key_1' => 'value_1',
      'key_2' => 'value_2',
    ]);

    // Verify that returned value matches expected object
    $I->assertEquals($returned, $shortcode_instance);

    // Verify ::__createInstance was invoked
    $mock_factory->verifyInvokedOnce('__createInstance', [
      $prop_value['unit_test_shortcode'],
      [
        'key_1' => 'value_1',
        'key_2' => 'value_2',
      ]
    ]);

    // Test ::create with undefined manufacturable id
    $returned = ShortcodeFactory::create('___undefined_manufacturable_id___');

    // Verify that returned value is null
    $I->assertNull($returned);

    // Test ::create with bad arguments
    $bad_args = [
      null,
      0,
      1,
      [1],
      new \stdClass
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(\Exception::class, function() use($bad_arg_val) {
        ShortcodeFactory::create($bad_arg_val);
      });
    }   
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory::__createInstance
   * @depends checkDefaultManufacturables
   * 
   * @param UnitTester $I Tester Module
   */
  public function createInstance(UnitTester $I)
  {
    // Get ::__createInstance reflection and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory', '__createInstance');
    $method_refl->setAccessible(true);

    // Test ::__createInstance with valid class
    $object = $method_refl->invokeArgs(null, [
      'UnitTestShortcodeContainer',
    ]);

    $I->assertTrue($object instanceof \UnitTestShortcodeContainer);

    // Test ::__createInstance with valid class
    $object = $method_refl->invokeArgs(null, [
      'InvalidUnitTestShortcodeContainer',
    ]);

    $I->assertNull($object);
  }
}
