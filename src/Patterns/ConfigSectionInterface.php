<?php

namespace Ponticlaro\Bebop\Cms\Patterns;

interface ConfigSectionInterface {

  /**
   * Instantiates configuration section
   * 
   * @param array $config Configuration array
   */
  public function __construct(array $config = []);

  /**
   * Returns any created configuration items
   * 
   * @return array List of created configuration items
   */
  public function getItems();
} 