<?php

namespace BebopUnitTests;

use Ponticlaro\Bebop\Cms\Patterns\ConfigItem as ConfigItemAbstract;
use Ponticlaro\Bebop\Cms\Patterns\ConfigSection as ConfigSectionAbstract;
use Ponticlaro\Bebop\Cms\Patterns\Factory as FactoryAbstract;
use Ponticlaro\Bebop\Cms\Patterns\ScriptConfigItem as ScriptConfigItemAbstract;
use Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainerAbstract;

/**
 * Used by:
 * - Patterns\ConfigItem
 * 
 */
class ConfigItem extends ConfigItemAbstract {

  public function isValid()
  {
    return true;
  }

  public function build() {}
}

/**
 * Used by:
 * - Patterns\ConfigSection
 * 
 */
class ConfigSection extends ConfigSectionAbstract {

  public function getItems() {
    return [];
  }
}

/**
 * Used by:
 * - Patterns\Factory
 * 
 */
class FactoryManufacturableClass {
  public $args = [];
  public function __construct(array $args = null) {
    $this->args = $args;
  }
}

/**
 * Used by:
 * - Patterns\Factory
 *
 */
class FactoryManufacturableClassAlt {
  public function __construct() {}
}

/**
 * Used by:
 * - Patterns\Factory
 * 
 */
class Factory extends FactoryAbstract {
  protected static $manufacturable_class = 'BebopUnitTests\FactoryManufacturableClass';
}

/**
 * Used by:
 * - Patterns\Factory
 * 
 */
class FactoryItem extends FactoryManufacturableClass {}

/**
 * Used by:
 * - Patterns\Factory
 * 
 */
class TestFactoryItem {}

/**
 * Used by:
 * - Patterns\ShortcodeConfigItem
 * - Patterns\ShortcodeContainer
 * 
 */
class ShortcodeContainer extends ShortcodeContainerAbstract {

  protected $id            = 'unit_test';
  protected $template_path = '/path/to/template.php';
  protected $default_attrs = [
    'key_1' => 'value_1',
    'key_2' => 'value_2',
  ];

  public function render($attrs, $content = null, $tag) {}
}

/**
 * Used by:
 * - Patterns\ShortcodeConfigItem
 * 
 */
class ShortcodeContainerAlt extends ShortcodeContainerAbstract {

  protected $id            = 'unit_test';
  protected $template_path = '/path/to/template.php';
  protected $default_attrs = [
    'key_1' => 'value_1',
    'key_2' => 'value_2',
  ];

  public function render($attrs, $content = null, $tag) {}
}

/**
 * Used by:
 * - AdminPage
 * - Metabox
 * 
 * @return void
 */
function sample_control_elements() {

  echo '
  <input type="text" name="text">
  <input type="hidden" name="hidden">
  <input type="checkbox" name="checkbox">
  <input type="checkbox" name="multiple_checkboxes[]">
  <input type="checkbox" name="multiple_checkboxes[]">
  <input type="radio" name="radio">
  <input type="radio" name="multiple_radios">
  <input type="radio" name="multiple_radios">
  <input type="file" name="file">
  <select name="select"></select>
  <select multiple="multiple" name="select_multiple"></select>
  <textarea name="textarea"></textarea>
  ';
}