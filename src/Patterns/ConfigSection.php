<?php

namespace Ponticlaro\Bebop\Cms\Patterns;

use Ponticlaro\Bebop\Common\Collection;
use Ponticlaro\Bebop\Common\Utils;

abstract class ConfigSection implements ConfigSectionInterface {

  /**
   * Contains configuration data
   * 
   * @var object Ponticlaro\Bebop\Common\Collection
   */
  protected $config;

  /**
   * Instantiates configuration section
   * 
   * @param array $config Configuration array
   */
  public function __construct(array $config = [])
  {
    $this->config = new Collection($config);
  }

  /**
   * Returns any created configuration items
   * 
   * @return array List of created configuration items
   */
  abstract public function getItems();
} 