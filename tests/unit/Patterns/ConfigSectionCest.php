<?php
namespace Patterns;

use \UnitTester;
use AspectMock\Test;

class ConfigSectionCest
{
    public function _before(UnitTester $I)
    {

    }

    public function _after(UnitTester $I)
    {
      Test::clean();
    }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Patterns\ConfigSection::__construct
   * 
   * @param UnitTester $I Tester Module
   */
    public function create(UnitTester $I)
    {
      // Collection mock
      $coll_mock = Test::double('Ponticlaro\Bebop\Common\Collection', [
        '__construct' => null
      ]);

      // Test ::__construct
      new \BebopUnitTests\ConfigSection([
        'key_1' => 'value_1',
        'key_2' => 'value_2',
      ]);

      // Verify that Collection::__construct is invoked correctly
      $coll_mock->verifyInvokedOnce('__construct', [
        [
          'key_1' => 'value_1',
          'key_2' => 'value_2',
        ]
      ]);
    }
}
