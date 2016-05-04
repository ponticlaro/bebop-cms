<?php

namespace Ponticlaro\Bebop\Cms\Preset\Shortcodes;

class Quotes extends \Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainerAbstract {

  public function __construct()
  {
    parent::__construct();

    $this->setShortcode('quote', [$this, 'renderQuote'], [
      
    ]);
  }

  public function renderQuote($attrs, $content = null)
  {

  }
}