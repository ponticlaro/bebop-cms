<?php

namespace Ponticlaro\Bebop\Cms\Preset\Shortcodes;

class Images extends \Ponticlaro\Bebop\Cms\Patterns\ShortcodeContainerAbstract {

	public function __construct()
	{
		parent::__construct();

		$this->setShortcode('image', [$this, 'renderImage'], [
			
		]);
	}

	public function renderImage($attrs, $content = null)
	{

	}
}