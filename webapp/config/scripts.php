<?php

$config = array
(
    'scripts/mootools/core.js'         => Null,
    'scripts/mootools/log.js'          => 'scripts/mootools/core.js',
    'scripts/mootools/more.js'         => 'scripts/mootools/core.js',
    'scripts/mootools/class.js'        => 'scripts/mootools/more.js',
    'scripts/mootools/native.js'       => Array('scripts/mootools/class.js', 'scripts/mootools/localization.js'),
    'scripts/mootools/element.js'      => 'scripts/mootools/more.js',
    'scripts/mootools/form.js'         => Array('scripts/mootools/element.js', 'scripts/mootools/native.js', 'scripts/mootools/class.js', 'scripts/mootools/localization.js'),
    'scripts/mootools/fx.js'           => 'scripts/mootools/element.js',
    'scripts/mootools/drag.js'         => 'scripts/mootools/class.js',
    'scripts/mootools/request.js'      => Array('scripts/mootools/class.js', 'scripts/mootools/log.js'),
    'scripts/mootools/utilities.js'    => Array('scripts/mootools/element.js', 'scripts/mootools/class.js'),
    'scripts/mootools/interface.js'    => 'scripts/mootools/more.js',
    'scripts/mootools/localization.js' => 'scripts/mootools/more.js',
);
