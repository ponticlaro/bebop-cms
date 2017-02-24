<?php
namespace Helpers;

use \UnitTester;
use AspectMock\Test;
use Ponticlaro\Bebop\Cms\Helpers\ConfigItemFactory;

class ConfigItemFactoryCest
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
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Helpers\ConfigItemFactory', 'manufacturable_class');
    $prop_refl->setAccessible(true);

    $I->assertEquals($prop_refl->getValue(), 'Ponticlaro\Bebop\Cms\Patterns\ConfigItem');
  }

  /**
   * @author cristianobaptista
   * 
   * @param UnitTester $I Tester Module
   */
  public function verifyDefaultManufacturables(UnitTester $I)
  {
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Helpers\ConfigItemFactory', 'manufacturable');
    $prop_refl->setAccessible(true);
    $prop_value = $prop_refl->getValue();

    $I->assertEquals(array_keys($prop_value), [
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
    ]);
  }
}
