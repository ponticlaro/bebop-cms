<?php

namespace Ponticlaro\Bebop\Cms\Preset\Shortcodes;

class Calendars extends \Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainerAbstract {

  public function __construct()
  {
    parent::__construct();

    $this->setShortcode('google_calendar', [$this, 'renderGoogleCalendar'], [
      
    ]);
  }

  public function renderGoogleCalendar($attrs, $content = null)
  {

  }
}