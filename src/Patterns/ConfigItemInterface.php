<?php

namespace Ponticlaro\Bebop\Cms\Patterns;

interface ConfigItemInterface {

  public function __construct(array $config = []);
  public function isValid();
  public function set($key, $value);
  public function get($key);
  public function remove($key);
  public function getUniqueId();
  public function getId();
  public function getPresetId();
  public function getRequirements();
  public function getAll();
  public function merge(ConfigItem $config_item);
  public function build();
} 