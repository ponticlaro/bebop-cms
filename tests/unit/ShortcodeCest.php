<?php

use AspectMock\Test;
use Ponticlaro\Bebop\Cms\Shortcode;

class ShortcodeCest
{
  /**
   * Expected values for the default config of a 'Title' type
   * 
   * @var array
   */
  private $expected_cfg = [
    'tag'                => 'tag',
    'function'           => 'Ponticlaro\Bebop\Cms\callback_mock',
    'attributes'         => [],
    'default_attributes' => []
  ];

  /**
   * List of mock instances
   * 
   * @var array
   */
  private $mocks = [];

  public function _before(UnitTester $I)
  {
    // Mock callback
    $this->mocks['callback'] = Test::func('Ponticlaro\Bebop\Cms', 'callback_mock', true);

    // Mock add_shortcode function
    $this->mocks['add_shortcode'] = Test::func('Ponticlaro\Bebop\Cms', 'add_shortcode', true);
  }

  public function _after(UnitTester $I)
  {
    Test::clean();
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Shortcode::__construct
   * 
   * @param UnitTester $I Tester Module
   */
  public function create(UnitTester $I)
  {
    // Create test instance
    $shortcode = new Shortcode('tag', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Check $shortcode->tag
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Shortcode', 'tag');
    $prop->setAccessible(true);

    $I->assertEquals($prop->getValue($shortcode), $this->expected_cfg['tag']);

    // Check $shortcode->function
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Shortcode', 'function');
    $prop->setAccessible(true);

    $I->assertEquals($prop->getValue($shortcode), $this->expected_cfg['function']);

    // Check $shortcode->attributes
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Shortcode', 'attributes');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($shortcode);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->expected_cfg['attributes']);

    // Check $shortcode->default_attributes
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Shortcode', 'default_attributes');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($shortcode);

    $I->assertTrue($prop_val instanceof \Ponticlaro\Bebop\Common\Collection);
    $I->assertEquals($prop_val->getAll(), $this->expected_cfg['default_attributes']);

    // Verify that add_shortcode is called correctly
    $this->mocks['add_shortcode']->verifyInvokedOnce(['tag', [$shortcode, '__registerShortcode']]);
  
    // Test ::__construct with bad arguments
    $bad_args = [
      [null, null],
      [0, 0],
      [1, 1],
      [[1], [1]],
      [new \stdClass, new \stdClass],
      ['tag', null],
      ['tag', 0],
      ['tag', 1],
      ['tag', [1]],
      ['tag', new \stdClass],
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($bad_arg_val) {
        new Shortcode($bad_arg_val[0], $bad_arg_val[1]);
      });
    }   
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Shortcode::getObjectId
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function getObjectId(UnitTester $I)
  {
    // Create test instance
    $shortcode = new Shortcode('tag', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::getObjectId
    $I->assertEquals($shortcode->getObjectId(), 'tag');
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Shortcode::getObjectType
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function getObjectType(UnitTester $I)
  {
    // Create test instance
    $shortcode = new Shortcode('tag', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Test ::getObjectId
    $I->assertEquals($shortcode->getObjectType(), 'shortcode');
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Shortcode::setDefaultAttr
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setDefaultAttr(UnitTester $I)
  {
    // Create test instance
    $shortcode = new Shortcode('tag', 'Ponticlaro\Bebop\Cms\callback_mock');
    
    // Test ::setDefaultAttr
    $shortcode->setDefaultAttr('key', 'value');

    // Get $shortcode->default_attributes
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Shortcode', 'default_attributes');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($shortcode);

    // Verify updated default_attributes
    $I->assertEquals($prop_val->getAll(), [
      'key' => 'value'
    ]);

    // Test ::setDefaultAttr with bad arguments
    $bad_args = [
      [null, null],
      [0, 0],
      [1, 1],
      [[1], [1]],
      [new \stdClass, new \stdClass],
    ];

    foreach ($bad_args as $bad_arg_val) {

      // Check if exception is thrown with bad arguments
      $I->expectException(Exception::class, function() use($shortcode, $bad_arg_val) {
        $shortcode->setDefaultAttr($bad_arg_val[0], $bad_arg_val[1]);
      });
    }   
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Shortcode::setDefaultAttrs
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function setDefaultAttrs(UnitTester $I)
  {
    // Create test instance
    $shortcode = new Shortcode('tag', 'Ponticlaro\Bebop\Cms\callback_mock');
  
    // Test ::setDefaultAttrs
    $shortcode->setDefaultAttrs([
      'key_1' => 'value_1',
      'key_2' => 'value_2',
    ]);

    // Get $shortcode->default_attributes
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Shortcode', 'default_attributes');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($shortcode);

    // Verify updated default_attributes
    $I->assertEquals($prop_val->getAll(), [
      'key_1' => 'value_1',
      'key_2' => 'value_2',
    ]);
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Shortcode::__registerShortcode
   * @depends create
   * @depends setDefaultAttr
   * 
   * @param UnitTester $I Tester Module
   */
  public function registerShortcode(UnitTester $I, $scenario)
  {
    // Create test instance
    $shortcode = new Shortcode('tag', 'Ponticlaro\Bebop\Cms\callback_mock');
  
    // Setup test data
    $shortcode->setDefaultAttr('key_1', 'value_1');
    $shortcode->setDefaultAttr('key_2', 'value_2');

    $args = [
      'key_1' => 'value_1_1',
      'key_3' => 'value_3',
    ];

    $content = 'Shortcode Content';

    // Test ::__registerShortcode
    $shortcode->__registerShortcode($args, $content, 'tag');

    // Get $shortcode->attributes
    $prop = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Shortcode', 'attributes');
    $prop->setAccessible(true);
    $prop_val = $prop->getValue($shortcode);

    // Verify updated default_attributes
    $I->assertEquals($prop_val->getAll(), [
      'key_1' => 'value_1_1',
      'key_2' => 'value_2',
      'key_3' => 'value_3',
    ]);

    // Verify that the callback is called correctly
    $this->mocks['callback']->verifyInvokedOnce([
      $prop_val,
      $content,
      'tag',
    ]);

    // Create test instance
    $shortcode = new Shortcode('tag', 'Ponticlaro\Bebop\Cms\callback_mock');
  }

  /**
   * @author  cristianobaptista
   * @covers  Ponticlaro\Bebop\Cms\Shortcode::__cleanAttrValue
   * @depends create
   * 
   * @param UnitTester $I Tester Module
   */
  public function cleanAttrValue(UnitTester $I)
  {
    // Create test instance
    $shortcode = new Shortcode('tag', 'Ponticlaro\Bebop\Cms\callback_mock');

    // Get ::__cleanAttrValue reflection
    $method = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Shortcode', '__cleanAttrValue');
    $method->setAccessible(true);

    // Test ::__cleanAttrValue
    $I->assertEquals($method->invoke($shortcode, "'Test'Test'"), 'TestTest');
    $I->assertEquals($method->invoke($shortcode, '"Test"Test"'), 'TestTest');
  }
}