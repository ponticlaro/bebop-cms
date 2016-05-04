<?php

namespace Ponticlaro\Bebop\Cms\Preset\Shortcodes;

class Galleries extends \Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainerAbstract {

  public function __construct()
  {
    parent::__construct();

    $this->setShortcode('gallery', [$this, 'renderGallery'], [
      
    ]);
  }

  public function renderGallery($attrs, $content = null)
  {

  }
}