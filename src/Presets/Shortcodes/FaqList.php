<?php

namespace Ponticlaro\Bebop\Cms\Preset\Shortcodes;

class FaqList extends \Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainerAbstract {

  public function __construct()
  {
    parent::__construct();

    $this->setShortcode('faq_list', [$this, 'renderFaqList'], [
      
    ]);
  }

  public function renderFaqList($attrs, $content = null)
  {

  }
}