<?php

namespace Helpers;

use \UnitTester;
use AspectMock\Test;
use Ponticlaro\Bebop\Cms\Helpers\MetaboxData;

class MetaboxDataCest
{
  /**
   * List of mock instances
   * 
   * @var array
   */
  private $m = [];

  public function _before(UnitTester $I)
  {
    // Mocks
    $this->m['maybe_unserialize'] = Test::func('Ponticlaro\Bebop\Cms\Helpers', 'maybe_unserialize', function() {
      return func_get_arg(0);
    });
  }

  public function _after(UnitTester $I)
  {
    Test::clean();
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Helpers\MetaboxData::get
   * 
   * @param UnitTester $I Tester Module
   */
  public function getSingleContainingSingleValue(UnitTester $I)
  {
    $data = new MetaboxData([
      'key' => 'value'
    ]);

    $I->assertEquals($data->get('key', true), 'value');
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Helpers\MetaboxData::get
   * 
   * @param UnitTester $I Tester Module
   */
  public function getSingleContainingArrayValue(UnitTester $I)
  {
    $data = new MetaboxData([
      'key' => [
        'value_1',
        'value_2',
      ]
    ]);

    $I->assertEquals($data->get('key', true), 'value_1');

    // Verify that maybe_unserialize was invoked
    $this->m['maybe_unserialize']->verifyInvokedOnce(['value_1']);
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Helpers\MetaboxData::get
   * 
   * @param UnitTester $I Tester Module
   */
  public function getEmptyValue(UnitTester $I)
  {
    $data = new MetaboxData([
      'key' => []
    ]);

    $I->assertEquals($data->get('key', true), '');
    $I->assertEquals($data->get('key'), []);
  }
}
