<?php
namespace Config;

use \UnitTester;
use AspectMock\Test;
use Ponticlaro\Bebop\Cms\Config\UrlConfigItem;

class UrlConfigItemCest
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
   * @covers Ponticlaro\Bebop\Cms\Config\UrlConfigItem::getIdKey
   * 
   * @param UnitTester $I Tester Module
   */
  public function getIdKey(UnitTester $I)
  {
    $I->assertEquals(UrlConfigItem::getIdKey(), 'id');
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\UrlConfigItem::isValid
   * 
   * @param UnitTester $I Tester Module
   */
  public function minimumValidConfig(UnitTester $I)
  {
    // Create test instance
    $item = new UrlConfigItem([
      'id'  => 'unit_test',
      'url' => '/unit/test',
    ]);

    // Test ::isValid()
    $I->assertTrue($item->isValid());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\UrlConfigItem::isValid
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
      $item = new UrlConfigItem([
        'id'  => $arg,
        'url' => '/unit/test',
      ]);

      // Test ::isValid()
      $I->assertTrue($item->isValid());
    }
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\UrlConfigItem::isValid
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
      $item = new UrlConfigItem([
        'id'  => $arg,
        'url' => '/unit/test',
      ]);

      // Test ::isValid()
      $I->assertFalse($item->isValid());
    }
  }


  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\UrlConfigItem::isValid
   * @depends minimumValidConfig
   *
   * @param UnitTester $I Tester Module
   */
  public function validUrl(UnitTester $I)
  {
    $args_list = [
      '/unit/test',
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new UrlConfigItem([
        'id'  => 'unit_test',
        'url' => $arg,
      ]);

      // Test ::isValid()
      $I->assertTrue($item->isValid());
    }
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\UrlConfigItem::isValid
   * @depends minimumValidConfig
   *
   * @param UnitTester $I Tester Module
   */
  public function invalidUrl(UnitTester $I)
  {
    $args_list = [
      null, false, true, 0, 1, [1], new \stdClass
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new UrlConfigItem([
        'id'  => 'unit_test',
        'url' => $arg,
      ]);

      // Test ::isValid()
      $I->assertFalse($item->isValid());
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\UrlConfigItem::build
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function build(UnitTester $I)
  {
    // Mock UrlManager
    $urls_mock = Test::double('Ponticlaro\Bebop\Common\UrlManager', [
      '__construct' => null,
      'set'         => null,
    ]);

    // Mock UrlManager::getInstance
    Test::double('Ponticlaro\Bebop\Common\UrlManager', [
      'getInstance' => $urls_mock->construct()
    ]);

    // Create test instance
    $item = new UrlConfigItem([
      'id'  => 'url_config_item_unit_test',
      'url' => '/url/config/item/unit/test',
    ]);

    // Test ::isValid()
    $item->build();

    // Verify UrlManager::set was invoked correctly
    $urls_mock->verifyInvokedOnce('set', [
      $item->getId(),
      $item->get('url'),
    ]);
  }
}
