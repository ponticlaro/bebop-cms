<?php

namespace Ponticlaro\Bebop\Cms\Preset\Shortcodes;

class Maps extends \Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainerAbstract {

  public function __construct()
  {
    parent::__construct();

    $this->setShortcode('google_map', [$this, 'renderGoogleMap'], [
      
    ]);
  }

  public function renderGoogleMap($attrs, $content = null)
  {

  }
}