<?php
namespace Config;

use \UnitTester;
use AspectMock\Test;
use Ponticlaro\Bebop\Cms\Config\MetaboxConfigItem;

class MetaboxConfigItemCest
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
   * @covers Ponticlaro\Bebop\Cms\Config\MetaboxConfigItem::getIdKey
   * 
   * @param UnitTester $I Tester Module
   */
  public function getIdKey(UnitTester $I)
  {
    $I->assertEquals(MetaboxConfigItem::getIdKey(), 'title');
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\MetaboxConfigItem::isValid
   * 
   * @param UnitTester $I Tester Module
   */
  public function minimumValidConfig(UnitTester $I)
  {
    // Create test instance
    $item = new MetaboxConfigItem([
      'title' => 'Unit Test',
      'types' => 'type_1'
    ]);

    // Test ::isValid()
    $I->assertTrue($item->isValid());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\MetaboxConfigItem::isValid
   * @depends minimumValidConfig
   *
   * @param UnitTester $I Tester Module
   */
  public function validTitle(UnitTester $I)
  {
    $args_list = [
      'Unit Test',
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new MetaboxConfigItem([
        'title' => $arg,
        'types' => 'type_1',
      ]);

      // Test ::isValid()
      $I->assertTrue($item->isValid());
    }
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\MetaboxConfigItem::isValid
   * 
   * @param UnitTester $I Tester Module
   */
  public function invalidTitle(UnitTester $I)
  {
    $args_list = [
      null, 0, 1, [1], new \stdClass
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new MetaboxConfigItem([
        'title' => $arg,
        'types' => 'type_1',
      ]);

      // Test ::isValid()
      $I->assertFalse($item->isValid());
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\MetaboxConfigItem::isValid
   * @depends minimumValidConfig
   *
   * @param UnitTester $I Tester Module
   */
  public function validTypes(UnitTester $I)
  {
    $args_list = [
      'type_1', ['type_1', 'type_2']
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new MetaboxConfigItem([
        'title' => 'unit_test',
        'types' => $arg,
      ]);

      // Test ::isValid()
      $I->assertTrue($item->isValid());
    }
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\MetaboxConfigItem::isValid
   * 
   * @param UnitTester $I Tester Module
   */
  public function invalidTypes(UnitTester $I)
  {
    $args_list = [
      null, false, true, 0, 1, new \stdClass
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new MetaboxConfigItem([
        'title' => 'unit_test',
        'types' => $arg,
      ]);

      // Test ::isValid()
      $I->assertFalse($item->isValid());
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\MetaboxConfigItem::build
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function build(UnitTester $I)
  {
    // Create Metabox mock
    $metabox_mock = Test::double('Ponticlaro\Bebop\Cms\Metabox', [
      '__construct' => null,
    ]);

    // Create test instance
    $item = new MetaboxConfigItem([
      'title' => 'Unit Test',
      'types' => 'type_1',
    ]);

    // Test ::isValid()
    $item->build();

    // Verify Metabox::__construct was invoked correctly
    $metabox_mock->verifyInvokedOnce('__construct', [
      $item->getAll()
    ]);
  }
}
