<?php
namespace Patterns;

use \UnitTester;
use AspectMock\Test;

class ConfigItemCest
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
   * @covers Ponticlaro\Bebop\Cms\Patterns\ConfigItem::getIdKey
   * 
   * @param UnitTester $I Tester Module
   */
  public function getIdKey(UnitTester $I)
  {
    $I->assertEquals(\BebopUnitTests\ConfigItem::getIdKey(), 'id');
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Patterns\ConfigItem::getUniqueId
   * 
   * @param UnitTester $I Tester Module
   */
  public function getUniqueId(UnitTester $I)
  {
    // Mock ConfigItem::__getNormalizedId
    Test::Double('Ponticlaro\Bebop\Cms\Patterns\ConfigItem', [
      '__getNormalizedId' => function() {
        return func_get_arg(0);
      }
    ]);

    // Test '_id' as the 'unique id'
    $item = new \BebopUnitTests\ConfigItem([
      '_id' => 'unique_id',
      'id'  => 'config_id',
    ]);

    // Verify that ::getUniqueId returns expected value
    $I->assertEquals($item->getUniqueId(), 'unique_id');

    // Test 'id' as the 'unique id'
    $item = new \BebopUnitTests\ConfigItem([
      'id' => 'config_id',
    ]);

    // Verify that ::getUniqueId returns expected value
    $I->assertEquals($item->getUniqueId(), 'config_id');
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Patterns\ConfigItem::getId
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
    $item = new \BebopUnitTests\ConfigItem([
      \BebopUnitTests\ConfigItem::getIdKey() => 'config_id',
    ]);

    // Verify that ::getId returns expected value
    $I->assertEquals($item->getId(), 'config_id');

    // Test when there is no key to get an id from
    $item = new \BebopUnitTests\ConfigItem();

    // Verify that ::getId returns expected value
    $I->assertNull($item->getId());
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Patterns\ConfigItem::getPresetId
   * 
   * @param UnitTester $I Tester Module
   */
  public function getPresetId(UnitTester $I)
  {   
    // Mock ConfigItem::__getNormalizedId
    Test::Double('Ponticlaro\Bebop\Cms\Patterns\ConfigItem', [
      '__getNormalizedId' => function() {
        return func_get_arg(0);
      }
    ]);

    // Test when there is the key to get a preset id from
    $item = new \BebopUnitTests\ConfigItem([
      'preset' => 'preset_id',
    ]);

    // Verify that ::getPresetId returns expected value
    $I->assertEquals($item->getPresetId(), 'preset_id');

    // Test when there is no key to get a preset id from
    $item = new \BebopUnitTests\ConfigItem();

    // Verify that ::getPresetId returns expected value
    $I->assertNull($item->getPresetId());
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Patterns\ConfigItem::getRequirements
   * 
   * @param UnitTester $I Tester Module
   */
  public function getRequirements(UnitTester $I)
  {
    // Create test instance
    $item = new \BebopUnitTests\ConfigItem([
      'requires' => [
        'key_1' => 'value_1',
        'key_2' => 'value_2',
      ]
    ]);

    // Verify that ::getRequirements returns expected value
    $I->assertEquals($item->getRequirements(), [
      'key_1' => 'value_1',
      'key_2' => 'value_2',
    ]);
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Patterns\ConfigItem::merge
   * 
   * @param UnitTester $I Tester Module
   */
  public function merge(UnitTester $I)
  {
    // Create test instance
    $item = new \BebopUnitTests\ConfigItem([
      '_id'      => 'unique_id',
      'id'       => 'config_id',
      'preset'   => 'preset_id',
      'requires' => [
        'key_1'    => 'value_1',
        'key_2'    => 'value_2',
      ],
    ]);

    // Create merge instance
    $merge_item = new \BebopUnitTests\ConfigItem([
      '_id'      => 'unique_id_alt',
      'id'       => 'config_id_alt',
      'preset'   => 'preset_id_alt',
      'requires' => [
        'key_1' => 'value_1_1',
        'key_3' => 'value_3',
      ],
    ]);

    // Test ::__getNormalizedId
    $item->merge($merge_item);

    // Verify that config contains expected value
    $I->assertEquals($item->getAll(), [
      '_id'      => 'unique_id_alt',
      'id'       => 'config_id_alt',
      'preset'   => 'preset_id_alt',
      'requires' => [
        'key_1' => 'value_1_1',
        'key_2' => 'value_2',
        'key_3' => 'value_3',
      ],
    ]);
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Patterns\ConfigItem::__getNormalizedId
   * 
   * @param UnitTester $I Tester Module
   */
  public function getNormalizedId(UnitTester $I)
  {
    // Get ::__getNormalizedId and make it accessible
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Patterns\ConfigItem', '__getNormalizedId');
    $method_refl->setAccessible(true);

    // Test ::__getNormalizedId
    $normalized_id = $method_refl->invoke(null, 'ID.to.Normalize');

    // Verify normalized_id matches expected value
    $I->assertEquals($normalized_id, 'id_to_normalize');
  }
}
