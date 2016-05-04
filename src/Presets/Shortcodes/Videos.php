<?php

namespace Ponticlaro\Bebop\Cms\Preset\Shortcodes;

class Videos extends \Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainerAbstract {

  public function __construct()
  {
    parent::__construct();

    $this->setShortcode('livestream', [$this, 'renderLivestream'], [
      
    ]);

    $this->setShortcode('vimeo', [$this, 'renderVimeo'], [
      
    ]);

    $this->setShortcode('youtube', [$this, 'renderYoutube'], [
      
    ]);
  }

  public function renderLivestream($attrs, $content = null)
  {

  }

  public function renderVimeo($attrs, $content = null)
  {

  }

  public function renderYoutube($attrs, $content = null)
  {

  }
}