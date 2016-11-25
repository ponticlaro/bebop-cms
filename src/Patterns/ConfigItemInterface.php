<?php

namespace Ponticlaro\Bebop\Cms\Patterns;

interface ConfigItemInterface {

  public function __construct(array $config = []);
  public function isValid();
  public function set($key, $value);
  public function get($key);
  public function getId();
  public function getRequirements();
  public function getAll();
  public function merge(ConfigItemInterface $config_item);
  public function build();
} 