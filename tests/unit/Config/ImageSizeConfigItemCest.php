<?php
namespace Config;

use \UnitTester;
use AspectMock\Test;
use Ponticlaro\Bebop\Cms\Config\ImageSizeConfigItem;

class ImageSizeConfigItemCest
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
   * @covers Ponticlaro\Bebop\Cms\Config\ImageSizeConfigItem::getIdKey
   * 
   * @param UnitTester $I Tester Module
   */
  public function getIdKey(UnitTester $I)
  {
    $I->assertEquals(ImageSizeConfigItem::getIdKey(), 'name');
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Config\ImageSizeConfigItem::isValid
   * 
   * @param UnitTester $I Tester Module
   */
  public function minimumValidConfig(UnitTester $I)
  {
    // Create test instance
    $item = new ImageSizeConfigItem([
      'name'   => 'unit_test',
      'width'  => 1,
      'height' => 1,
    ]);

    // Test ::isValid()
    $I->assertTrue($item->isValid());
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\ImageSizeConfigItem::isValid
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function validName(UnitTester $I)
  {
    $args_list = [
      'unit_test'
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new ImageSizeConfigItem([
        'name'  => $arg,
        'width' => 1,
      ]);

      // Test ::isValid()
      $I->assertTrue($item->isValid());
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\ImageSizeConfigItem::isValid
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function invalidName(UnitTester $I)
  {
    $args_list = [
      null, 0, 1, [1], new \stdClass
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new ImageSizeConfigItem([
        'name'  => $arg,
        'width' => 1,
      ]);

      // Test ::isValid()
      $I->assertFalse($item->isValid());
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\ImageSizeConfigItem::isValid
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function validWidth(UnitTester $I)
  {
    $args_list = [
      300, '300'
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new ImageSizeConfigItem([
        'name'  => 'unit_test',
        'width' => $arg,
      ]);

      // Test ::isValid()
      $I->assertTrue($item->isValid());
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\ImageSizeConfigItem::isValid
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function invalidWidth(UnitTester $I)
  {
    $args_list = [
      null, false, true, [1], new \stdClass
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new ImageSizeConfigItem([
        'name'  => 'unit_test',
        'width' => $arg,
      ]);

      // Test ::isValid()
      $I->assertFalse($item->isValid());
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\ImageSizeConfigItem::isValid
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function validHeight(UnitTester $I)
  {
    $args_list = [
      300, '300'
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new ImageSizeConfigItem([
        'name'   => 'unit_test',
        'height' => $arg,
      ]);

      // Test ::isValid()
      $I->assertTrue($item->isValid());
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\ImageSizeConfigItem::isValid
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function invalidHeight(UnitTester $I)
  {
    $args_list = [
      null, false, true, [1], new \stdClass
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new ImageSizeConfigItem([
        'name'   => 'unit_test',
        'height' => $arg,
      ]);

      // Test ::isValid()
      $I->assertFalse($item->isValid());
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\ImageSizeConfigItem::isValid
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function validCrop(UnitTester $I)
  {
    $args_list = [
      null, true, ['center', 'center'],
    ];

    foreach ($args_list as $arg) {
      
      // Create test instance
      $item = new ImageSizeConfigItem([
        'name'  => 'unit_test',
        'width' => 1,
        'crop'  => $arg,
      ]);

      // Test ::isValid()
      $I->assertTrue($item->isValid());
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\ImageSizeConfigItem::isValid
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function invalidCrop(UnitTester $I)
  {
    $invalid_args = [
      0, 1, 'string', new \stdClass
    ];

    foreach ($invalid_args as $arg) {
      
      // Create test instance
      $item = new ImageSizeConfigItem([
        'name'  => 'unit_test',
        'width' => 1,
        'crop'  => $arg,
      ]);

      // Test ::isValid()
      $I->assertFalse($item->isValid());
    }
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Config\ImageSizeConfigItem::build
   * @depends minimumValidConfig
   * 
   * @param UnitTester $I Tester Module
   */
  public function build(UnitTester $I)
  {
    // Create add_image_size mock
    $add_image_size_mock = Test::func('Ponticlaro\Bebop\Cms\Config', 'add_image_size', true);

    // Create test instance
    $item = new ImageSizeConfigItem([
      'name'   => 'unit_test',
      'width'  => 300,
      'height' => 150,
      'crop'   => true,
    ]);

    // Test ::isValid()
    $item->build();

    // Verify add_image_size was invoked correctly
    $add_image_size_mock->verifyInvokedOnce([
      $item->get('name'),
      $item->get('width'),
      $item->get('height'),
      $item->get('crop'),
    ]);
  }
}
