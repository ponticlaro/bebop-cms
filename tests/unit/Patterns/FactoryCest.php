<?php
namespace Helpers;

use \UnitTester;
use AspectMock\Test;

class FactoryCest
{
  /**
   * List of mocks
   * 
   * @var array
   */
  private $m = [];

  public function _before(UnitTester $I)
  {

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
  public function verifyDefaultManufacturableClass(UnitTester $I)
  {
    $prop_refl = new \ReflectionProperty('UnitTestFactory', 'manufacturable_class');
    $prop_refl->setAccessible(true);

    $I->assertEquals($prop_refl->getValue(), 'UnitTestFactoryManufacturableClass');
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Patterns\Factory::set
   * 
   * @param UnitTester $I Tester Module
   */
  public function set(UnitTester $I)
  {
    // Test ::set
    \UnitTestFactory::set('unit_test', 'UnitTestFactoryItem');

    // Get manufacturable property and make it accessible
    $prop_refl = new \ReflectionProperty('UnitTestFactory', 'manufacturable');
    $prop_refl->setAccessible(true);
    $prop_value = $prop_refl->getValue();

    $I->assertTrue(isset($prop_value['unit_test']));
    $I->assertEquals($prop_value['unit_test'], 'UnitTestFactoryItem');

    // Test ::set with class that do not extend manufacturable class
    $I->expectException(\Exception::class, function() {
      \UnitTestFactory::set('unit_test_alt', 'InvalidUnitTestFactoryItem');
    });

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
        \UnitTestFactory::set($bad_arg_val[0], $bad_arg_val[1]);
      });
    }  
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Patterns\Factory::remove
   * 
   * @param UnitTester $I Tester Module
   */
  public function remove(UnitTester $I)
  {
    // Get manufacturable property and make it accessible
    $prop_refl = new \ReflectionProperty('UnitTestFactory', 'manufacturable');
    $prop_refl->setAccessible(true);
    $prop_value = $prop_refl->getValue();

    // Set value for test on $manufacturables property
    $prop_value['unit_test'] = 'UnitTestFactoryItem';
    $prop_refl->setValue($prop_value);

    // Test ::remove
    \UnitTestFactory::remove('unit_test');

    // Verify mock manufacturable was removed
    $prop_value = $prop_refl->getValue();

    $I->assertTrue(!isset($prop_value['unit_test']));

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
        \UnitTestFactory::remove($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Patterns\Factory::canManufacture
   * 
   * @param UnitTester $I Tester Module
   */
  public function canManufacture(UnitTester $I)
  {
    // Get manufacturable property and make it accessible
    $prop_refl = new \ReflectionProperty('UnitTestFactory', 'manufacturable');
    $prop_refl->setAccessible(true);
    $prop_value = $prop_refl->getValue();

    // Set value for test on $manufacturables property
    $prop_value['unit_test'] = 'UnitTestFactoryItem';
    $prop_refl->setValue($prop_value);

    // Test ::canManufacture with defined manufacturable id
    $I->assertTrue(\UnitTestFactory::canManufacture('unit_test'));

    // Test ::canManufacture with undefined manufacturable id
    $I->assertFalse(\UnitTestFactory::canManufacture('___undefined_manufacturable_id___'));

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
        \UnitTestFactory::canManufacture($bad_arg_val);
      });
    }    
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Patterns\Factory::getInstanceId
   * 
   * @param UnitTester $I Tester Module
   */
  public function getInstanceId(UnitTester $I)
  {
    // Get manufacturable property and make it accessible
    $prop_refl = new \ReflectionProperty('UnitTestFactory', 'manufacturable');
    $prop_refl->setAccessible(true);
    $prop_value = $prop_refl->getValue();

    // Set value for test on $manufacturables property
    $prop_value['unit_test'] = 'UnitTestFactoryItem';
    $prop_refl->setValue($prop_value);

    // Test ::canManufacture with defined manufacturable id
    $I->assertEquals('unit_test', \UnitTestFactory::getInstanceId(new \UnitTestFactoryItem));

    // Test ::canManufacture with undefined manufacturable id
    $I->assertNull(\UnitTestFactory::getInstanceId('___undefined_manufacturable_id___'));
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Patterns\Factory::create
   * 
   * @param UnitTester $I Tester Module
   */
  public function create(UnitTester $I)
  {
    // Get manufacturable property and make it accessible
    $prop_refl = new \ReflectionProperty('UnitTestFactory', 'manufacturable');
    $prop_refl->setAccessible(true);
    $prop_value = $prop_refl->getValue();

    // Set value for test on $manufacturables property
    $prop_value['unit_test'] = 'UnitTestFactoryItem';
    $prop_refl->setValue($prop_value);

    // Test ::create with defined manufacturable id
    $object = \UnitTestFactory::create('unit_test', [
      'key_1' => 'value_1',
      'key_2' => 'value_2',
    ]);

    // Verify that returned value matches expected object
    $I->assertTrue($object instanceof \UnitTestFactoryItem);

    // Test ::create with undefined manufacturable id
    $object = \UnitTestFactory::create('___undefined_manufacturable_id___');

    // Verify that returned value is null
    $I->assertNull($object);

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
        \UnitTestFactory::create($bad_arg_val);
      });
    }   
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Patterns\Factory::setManufacturableParentClass
   * @covers  Ponticlaro\Bebop\Cms\Patterns\Factory::getManufacturableParentClass
   * 
   * @param UnitTester $I Tester Module
   */
  public function manageManufacturableParentClass(UnitTester $I)
  {
    // Verify default value for ::getManufacturableParentClass
    $I->assertEquals(\UnitTestFactory::getManufacturableParentClass(), 'UnitTestFactoryManufacturableClass');

    // Test ::setManufacturableParentClass
    \UnitTestFactory::setManufacturableParentClass('UnitTestFactoryManufacturableClassAlt');

    // Verify default value for ::getManufacturableParentClass
    $I->assertEquals(\UnitTestFactory::getManufacturableParentClass(), 'UnitTestFactoryManufacturableClassAlt');
  
    // Test ::setManufacturableParentClass with bad arguments
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
        \UnitTestFactory::setManufacturableParentClass($bad_arg_val);
      });
    }   
  }
}
