<?php

namespace Ponticlaro\Bebop\Cms\Preset\Shortcodes;

class Forms extends \Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainerAbstract {

  public function __construct()
  {
    parent::__construct();

    $this->setShortcode('formstack', [$this, 'renderFormstack'], [
      
    ]);

    $this->setShortcode('pardot_form', [$this, 'renderPardotForm'], [
      
    ]);
  }

  public function renderFormstack($attrs, $content = null)
  {

  }

  public function renderPardotForm($attrs, $content = null)
  {

  }
}