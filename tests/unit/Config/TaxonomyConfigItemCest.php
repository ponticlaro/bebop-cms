<?php
namespace Config;

use \UnitTester;
use AspectMock\Test;
use Ponticlaro\Bebop\Cms\Config\TaxonomyConfigItem;

class TaxonomyConfigItemCest
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
   * @covers Ponticlaro\Bebop\Cms\Config\TaxonomyConfigItem::getIdKey
   * 
   * @param UnitTester $I Tester Module
   */
  public function getIdKey(UnitTester $I)
  {
    $I->assertEquals(TaxonomyConfigItem::getIdKey(), 'name');
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\TaxonomyConfigItem::isValid
   * 
   * @param UnitTester $I Tester Module
   */
  public function minimumValidConfig(UnitTester $I)
  {
    // Create test instance
    $item = new TaxonomyConfigItem([
      'name'  => 'Unit Test',
      'types' => 'type_1',
    ]);

    // Test ::isValid()
    $I->assertTrue($item->isValid());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\TaxonomyConfigItem::isValid
   * @depends minimumValidConfig
   *
   * @param UnitTester $I Tester Module
   */
  public function validName(UnitTester $I)
  {
    $args_list = [
      'Unit Test', ['Unit Test', 'Unit Tests']
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new TaxonomyConfigItem([
        'name'  => $arg,
        'types' => 'type_1',
      ]);

      // Test ::isValid()
      $I->assertTrue($item->isValid());
    }
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\TaxonomyConfigItem::isValid
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function invalidName(UnitTester $I)
  {
    $args_list = [
      null, false, true, 0, 1, new \stdClass
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new TaxonomyConfigItem([
        'name'  => $arg,
        'types' => 'type_1',
      ]);

      // Test ::isValid()
      $I->assertFalse($item->isValid());
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\TaxonomyConfigItem::isValid
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
      $item = new TaxonomyConfigItem([
        'name'  => 'Unit Test',
        'types' => $arg,
      ]);

      // Test ::isValid()
      $I->assertTrue($item->isValid());
    }
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\TaxonomyConfigItem::isValid
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function invalidTypes(UnitTester $I)
  {
    $args_list = [
      false, true, 0, 1, new \stdClass
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new TaxonomyConfigItem([
        'name'  => 'Unit Test',
        'types' => $arg,
      ]);

      // Test ::isValid()
      $I->assertFalse($item->isValid());
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\TaxonomyConfigItem::build
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function build(UnitTester $I)
  {
    // Create Taxonomy mock
    $tax_mock = Test::double('Ponticlaro\Bebop\Cms\Taxonomy', [
      '__construct'  => null,
      'applyRawArgs' => null,
    ]);

    // Create test instance
    $item = new TaxonomyConfigItem([
      'name'  => 'Unit Test',
      'types' => 'type_1',
    ]);

    // Test ::isValid()
    $item->build();

    // Verify Taxonomy::__construct was invoked correctly
    $tax_mock->verifyInvokedOnce('__construct', [
      $item->get('name'),
      $item->get('types'),
    ]);

    // Verify Taxonomy::applyRawArgs was invoked correctly
    $tax_mock->verifyInvokedOnce('applyRawArgs', [
      $item->getAll()
    ]);
  }
}
