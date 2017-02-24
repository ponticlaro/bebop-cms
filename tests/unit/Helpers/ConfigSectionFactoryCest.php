<?php
namespace Helpers;

use \UnitTester;
use AspectMock\Test;
use Ponticlaro\Bebop\Cms\Helpers\ConfigSectionFactory;

class ConfigSectionFactoryCest
{
  public function _before(UnitTester $I)
  {

  }

  public function _after(UnitTester $I)
  {

  }

  /**
   * @author cristianobaptista
   * 
   * @param UnitTester $I Tester Module
   */
  public function verifyDefaultManufacturableClass(UnitTester $I)
  {
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Helpers\ConfigSectionFactory', 'manufacturable_class');
    $prop_refl->setAccessible(true);
    
    $I->assertEquals($prop_refl->getValue(), 'Ponticlaro\Bebop\Cms\Patterns\ConfigSection');
  }

  /**
   * @author cristianobaptista
   * 
   * @param UnitTester $I Tester Module
   */
  public function verifyDefaultManufacturables(UnitTester $I)
  {
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Helpers\ConfigSectionFactory', 'manufacturable');
    $prop_refl->setAccessible(true);
    $prop_value = $prop_refl->getValue();

    $I->assertEquals(array_keys($prop_value), [
      'scripts',
      'styles',
    ]);
  }
}
