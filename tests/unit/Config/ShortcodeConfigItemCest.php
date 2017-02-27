<?php
namespace Config;

use \UnitTester;
use AspectMock\Test;
use Ponticlaro\Bebop\Cms\Config\ShortcodeConfigItem;

class ShortcodeConfigItemCest
{
  /**
   * List of mocks
   * 
   * @var array
   */
  private $m = [];

  public function _before(UnitTester $I)
  {
    // Create mocks
    $this->m['Utils'] = Test::double('Ponticlaro\Bebop\Common\Utils', [
      'slugify' => function() {
        return strtolower(func_get_arg(0));
      }
    ]);
  }

  public function _after(UnitTester $I)
  {
    Test::clean();
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\ShortcodeConfigItem::getIdKey
   * 
   * @param UnitTester $I Tester Module
   */
  public function getIdKey(UnitTester $I)
  {
    $I->assertEquals(ShortcodeConfigItem::getIdKey(), 'id');
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\ShortcodeConfigItem::isValid
   * 
   * @param UnitTester $I Tester Module
   */
  public function minimumValidConfig(UnitTester $I)
  {
    // Create test instance
    $item = new ShortcodeConfigItem([
      'id' => 'unit_test',
    ]);

    // Test ::isValid()
    $I->assertTrue($item->isValid());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\ShortcodeConfigItem::isValid
   * @depends minimumValidConfig
   *
   * @param UnitTester $I Tester Module
   */
  public function validId(UnitTester $I)
  {
    $args_list = [
      'unit_test'
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new ShortcodeConfigItem([
        'id' => $arg,
      ]);

      // Test ::isValid()
      $I->assertTrue($item->isValid());
    }
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\ShortcodeConfigItem::isValid
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function invalidId(UnitTester $I)
  {
    $args_list = [
      null, false, true, 0, 1, new \stdClass
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new ShortcodeConfigItem([
        'id' => $arg,
      ]);

      // Test ::isValid()
      $I->assertFalse($item->isValid());
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\ShortcodeConfigItem::isValid
   * @depends minimumValidConfig
   *
   * @param UnitTester $I Tester Module
   */
  public function validClass(UnitTester $I)
  {
    $args_list = [
      'BebopUnitTests\ShortcodeContainer'
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new ShortcodeConfigItem([
        'id'    => 'unit_test',
        'class' => $arg,
      ]);

      // Test ::isValid()
      $I->assertTrue($item->isValid());
    }
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\ShortcodeConfigItem::isValid
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function invalidClass(UnitTester $I)
  {
    $args_list = [
      'BebopUnitTests\_____undefined_unit_test_class_____'
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new ShortcodeConfigItem([
        'id'   => 'unit_test',
        'class' => $arg,
      ]);

      // Test ::isValid()
      $I->assertFalse($item->isValid());
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\ShortcodeConfigItem::build
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function build(UnitTester $I)
  {
    // Create ShortcodeContainer mock
    $shortcode_container_mock = Test::double('BebopUnitTests\ShortcodeContainer', [
      '__construct' => null,
      'register'    => true,
    ]);

    // Create ShortcodeFactory mock
    $shortcode_factory_mock = Test::double('Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory', [
      'canManufacture' => true,
      'create'         => $shortcode_container_mock->construct(),
    ]);

    // Create test instance
    $item = new ShortcodeConfigItem([
      'id' => 'unit_test',
    ]);

    // Test ::isValid()
    $item->build();

    // Verify ShortcodeFactory::canManufacture was invoked correctly
    $shortcode_factory_mock->verifyInvokedOnce('canManufacture', [
      $item->getId()
    ]);

    // Verify ShortcodeFactory::create was invoked correctly
    $shortcode_factory_mock->verifyInvokedOnce('create', [
      $item->getId()
    ]);

    // Verify ShortcodeContainer::register was invoked correctly
    $shortcode_container_mock->verifyInvokedOnce('register');
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\ShortcodeConfigItem::build
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function buildWithOverridenClass(UnitTester $I)
  {
    // Create ShortcodeContainerAlt mock
    $shortcode_container_mock = Test::double('BebopUnitTests\ShortcodeContainerAlt', [
      '__construct' => null,
      'register'    => true,
    ]);

    // Create ShortcodeFactory mock
    $shortcode_factory_mock = Test::double('Ponticlaro\Bebop\Cms\Helpers\ShortcodeFactory', [
      'canManufacture' => true,
      'create'         => $shortcode_container_mock->construct(),
    ]);

    // Create test instance
    $item = new ShortcodeConfigItem([
      'id'    => 'unit_test',
      'class' => 'BebopUnitTests\ShortcodeContainerAlt'
    ]);

    // Test ::isValid()
    $item->build();

    // Verify ShortcodeFactory::canManufacture was invoked correctly
    $shortcode_factory_mock->verifyInvokedOnce('canManufacture', [
      $item->getId()
    ]);

    // Verify ShortcodeFactory::create was invoked correctly
    $shortcode_factory_mock->verifyInvokedOnce('create', [
      $item->getId()
    ]);

    // Verify ShortcodeContainerAlt::register was invoked correctly
    $shortcode_container_mock->verifyInvokedOnce('register');
  }
}
