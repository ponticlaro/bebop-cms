<?php
namespace Helpers;

use \UnitTester;
use AspectMock\Test;
use Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory;

class ShortcodeFactoryCest
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
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory', 'manufacturable_class');
    $prop_refl->setAccessible(true);

    $I->assertEquals($prop_refl->getValue(), 'Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainerAbstract');
  }

  /**
   * @author cristianobaptista
   * 
   * @param UnitTester $I Tester Module
   */
  public function verifyDefaultManufacturables(UnitTester $I)
  {
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory', 'manufacturable');
    $prop_refl->setAccessible(true);
    $prop_value = $prop_refl->getValue();

    $I->assertEquals(array_keys($prop_value), [
      'image',
      'video', 
      'vimeo',
      'youtube',
    ]);
  }
}
