<?php

namespace Ponticlaro\Bebop\Cms\Helpers;

use Ponticlaro\Bebop\Cms\Patterns\Factory;

class ConfigSectionFactory extends Factory {

  /**
   * Holds the class that manufacturables must extend
   *
   */
  protected static $manufacturable_class = 'Ponticlaro\Bebop\Cms\Patterns\ConfigSection';

  /**
   * List of manufacturable classes
   * 
   * @var array
   */
  protected static $manufacturable = array(
    'scripts' => 'Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection',
    'styles'  => 'Ponticlaro\Bebop\Cms\Config\ScriptsConfigSection',
  );
}