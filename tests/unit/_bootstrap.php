<?php

use Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainerAbstract;

class UnitTestShortcodeContainer extends ShortcodeContainerAbstract {

  protected $id = 'unit_test_shortcode';

  public function render($attrs, $content = null, $tag)
  {
    
  }
}

class InvalidUnitTestShortcodeContainer {

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