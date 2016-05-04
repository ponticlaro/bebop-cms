<?php

namespace Ponticlaro\Bebop\Cms\Preset\Shortcodes;

class Social extends \Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainerAbstract {

  public function __construct()
  {
    parent::__construct();

    $this->setShortcode('facebook_post', [$this, 'renderFacebookPost'], [
      
    ]);

    $this->setShortcode('tweet', [$this, 'renderTweet'], [
      
    ]);
  }

  public function renderFacebookPost($attrs, $content = null)
  {

  }

  public function renderTweet($attrs, $content = null)
  {

  }
}