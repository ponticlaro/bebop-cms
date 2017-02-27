<?php
namespace Config;

use \UnitTester;
use AspectMock\Test;
use Ponticlaro\Bebop\Cms\Config\AdminPageConfigItem;

class AdminPageConfigItemCest
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
   * @covers Ponticlaro\Bebop\Cms\Config\AdminPageConfigItem::getIdKey
   * 
   * @param UnitTester $I Tester Module
   */
  public function getIdKey(UnitTester $I)
  {
    $I->assertEquals(AdminPageConfigItem::getIdKey(), 'title');
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\AdminPageConfigItem::isValid
   * 
   * @param UnitTester $I Tester Module
   */
  public function minimumValidConfig(UnitTester $I)
  {
    // Create test instance
    $item = new AdminPageConfigItem([
      'title' => 'Unit Test',
    ]);

    // Test ::isValid()
    $I->assertTrue($item->isValid());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\AdminPageConfigItem::isValid
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
      $item = new AdminPageConfigItem([
        'title' => $arg,
      ]);

      // Test ::isValid()
      $I->assertTrue($item->isValid());
    }
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\AdminPageConfigItem::isValid
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function invalidTitle(UnitTester $I)
  {
    $args_list = [
      null, false, true, 0, 1, [1], new \stdClass
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new AdminPageConfigItem([
        'title' => $arg,
      ]);

      // Test ::isValid()
      $I->assertFalse($item->isValid());
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\AdminPageConfigItem::build
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function build(UnitTester $I)
  {
    // Create AdminPage mock
    $admin_page_mock = Test::double('Ponticlaro\Bebop\Cms\AdminPage', [
      '__construct' => null,
    ]);

    // Create test instance
    $item = new AdminPageConfigItem([
      'title' => 'Unit Test',
    ]);

    // Test ::isValid()
    $item->build();

    // Verify AdminPage::__construct was invoked correctly
    $admin_page_mock->verifyInvokedOnce('__construct', [
      $item->getAll()
    ]);
  }
}
