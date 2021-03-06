<?php
namespace Config;

use \UnitTester;
use AspectMock\Test;
use Ponticlaro\Bebop\Cms\Config\TypeConfigItem;

class TypeConfigItemCest
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
   * @covers Ponticlaro\Bebop\Cms\Config\TypeConfigItem::getIdKey
   * 
   * @param UnitTester $I Tester Module
   */
  public function getIdKey(UnitTester $I)
  {
    $I->assertEquals(TypeConfigItem::getIdKey(), 'name');
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\TypeConfigItem::getId
   * 
   * @param UnitTester $I Tester Module
   */
  public function getId(UnitTester $I)
  {
    // Mock ConfigItem::__getNormalizedId
    Test::Double('Ponticlaro\Bebop\Cms\Patterns\ConfigItem', [
      '__getNormalizedId' => function() {
        return func_get_arg(0);
      }
    ]);

    // Test when there is the key to get an id from
    $item = new TypeConfigItem([
       TypeConfigItem::getIdKey() => 'config_id',
    ]);

    // Verify that ::getId returns expected value
    $I->assertEquals($item->getId(), 'config_id');

    // Test when there is the key to get an id from, and it is an array
    $item = new TypeConfigItem([
      TypeConfigItem::getIdKey() => [
        'singular',
        'plural',
      ],
    ]);

    // Verify that ::getId returns expected value
    $I->assertEquals($item->getId(), 'singular');

    // Test when there is no key to get an id from
    $item = new TypeConfigItem();

    // Verify that ::getId returns expected value
    $I->assertNull($item->getId());
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\TypeConfigItem::isValid
   * 
   * @param UnitTester $I Tester Module
   */
  public function minimumValidConfig(UnitTester $I)
  {
    // Create test instance
    $item = new TypeConfigItem([
      'name' => 'Unit Test',
    ]);

    // Test ::isValid()
    $I->assertTrue($item->isValid());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\TypeConfigItem::isValid
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
      $item = new TypeConfigItem([
        'name' => $arg,
      ]);

      // Test ::isValid()
      $I->assertTrue($item->isValid());
    }
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\TypeConfigItem::isValid
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
      $item = new TypeConfigItem([
        'name' => $arg,
      ]);

      // Test ::isValid()
      $I->assertFalse($item->isValid());
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\TypeConfigItem::build
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function build(UnitTester $I)
  {
    // Create Type mock
    $type_mock = Test::double('Ponticlaro\Bebop\Cms\PostType', [
      '__construct'  => null,
      'applyRawArgs' => null,
    ]);

    // Create test instance
    $item = new TypeConfigItem([
      'name' => 'Unit Test',
    ]);

    // Test ::isValid()
    $item->build();

    // Verify Type::__construct was invoked correctly
    $type_mock->verifyInvokedOnce('__construct', [
      $item->get('name')
    ]);

    // Verify Type::applyRawArgs was invoked correctly
    $type_mock->verifyInvokedOnce('applyRawArgs', [
      $item->getAll()
    ]);
  }
}
