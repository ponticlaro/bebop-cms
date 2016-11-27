<?php

namespace Ponticlaro\Bebop\Cms\Patterns;

interface ConfigSectionInterface {

  public function __construct(array $config = []);
  public function getItems();
} 