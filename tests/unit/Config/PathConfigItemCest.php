<?php
namespace Config;

use \UnitTester;
use AspectMock\Test;
use Ponticlaro\Bebop\Cms\Config\PathConfigItem;

class PathConfigItemCest
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
   * @covers Ponticlaro\Bebop\Cms\Config\PathConfigItem::getIdKey
   * 
   * @param UnitTester $I Tester Module
   */
  public function getIdKey(UnitTester $I)
  {
    $I->assertEquals(PathConfigItem::getIdKey(), 'id');
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\PathConfigItem::isValid
   * 
   * @param UnitTester $I Tester Module
   */
  public function minimumValidConfig(UnitTester $I)
  {
    // Create test instance
    $item = new PathConfigItem([
      'id'   => 'unit_test',
      'path' => '/unit/test',
    ]);

    // Test ::isValid()
    $I->assertTrue($item->isValid());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\PathConfigItem::isValid
   * @depends minimumValidConfig
   *
   * @param UnitTester $I Tester Module
   */
  public function validId(UnitTester $I)
  {
    $args_list = [
      'unit_test',
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new PathConfigItem([
        'id'   => $arg,
        'path' => '/unit/test',
      ]);

      // Test ::isValid()
      $I->assertTrue($item->isValid());
    }
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\PathConfigItem::isValid
   * @depends minimumValidConfig
   *
   * @param UnitTester $I Tester Module
   */
  public function invalidId(UnitTester $I)
  {
    $args_list = [
      null, false, true, 0, 1, [1], new \stdClass
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new PathConfigItem([
        'id'   => $arg,
        'path' => '/unit/test',
      ]);

      // Test ::isValid()
      $I->assertFalse($item->isValid());
    }
  }


  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\PathConfigItem::isValid
   * @depends minimumValidConfig
   *
   * @param UnitTester $I Tester Module
   */
  public function validPath(UnitTester $I)
  {
    $args_list = [
      '/unit/test',
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new PathConfigItem([
        'id'   => 'unit_test',
        'path' => $arg,
      ]);

      // Test ::isValid()
      $I->assertTrue($item->isValid());
    }
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\PathConfigItem::isValid
   * @depends minimumValidConfig
   *
   * @param UnitTester $I Tester Module
   */
  public function invalidPath(UnitTester $I)
  {
    $args_list = [
      null, false, true, 0, 1, [1], new \stdClass
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new PathConfigItem([
        'id'   => 'unit_test',
        'path' => $arg,
      ]);

      // Test ::isValid()
      $I->assertFalse($item->isValid());
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\PathConfigItem::build
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function build(UnitTester $I)
  {
    // Mock PathManager
    $paths_mock = Test::double('Ponticlaro\Bebop\Common\PathManager', [
      '__construct' => null
    ]);

    // Mock PathManager::getInstance
    Test::double('Ponticlaro\Bebop\Common\PathManager', [
      'getInstance' => $paths_mock->construct()
    ]);

    // Create test instance
    $item = new PathConfigItem([
      'id'   => 'unit_test',
      'path' => '/unit/test',
    ]);

    // Test ::isValid()
    $item->build();

    // Verify PathManager::set was invoked correctly
    $paths_mock->verifyInvokedOnce('set', [
      $item->getId(),
      $item->get('path'),
    ]);
  }
}
