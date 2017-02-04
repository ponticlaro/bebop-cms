<?php

// Autoload
require_once __DIR__ .'/../vendor/autoload.php';

// Bootstrap Aspect Mock
$kernel = \AspectMock\Kernel::getInstance();
$kernel->init([
  'debug'        => true,
  'includePaths' => [

    // Modules source code
    __DIR__.'/../src',

    // Source code of other Bebop modules
    __DIR__.'/../vendor/ponticlaro',
  ]
]);