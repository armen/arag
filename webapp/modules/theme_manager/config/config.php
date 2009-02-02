<?php
$config = array
(
    'allowed_image_extensions' => 'jpg,jpeg,png',

    'default_styles' => array
     (
        'v' => array(
            'selector'      => 'img#logo',
            'property'      => 'background',
            'description'   => _("Logo"),
            'value'         => 'path_to_logo_file',
            'type'          => 'file'),

        'w' => array(
            'selector'      => 'body',
            'property'      => 'color',
            'description'   => _("Color of body"),
            'value'         => 'white',
            'type'          => 'color'),

        'x' => array(
            'selector'      => 'p',
            'property'      => 'color',
            'description'   => _("Color of p elements"),
            'value'         => 'black',
            'type'          => 'color'),

        'y' => array(
            'selector'      => 'p',
            'property'      => 'background',
            'description'   => _("Background of p elements"),
            'value'         => '',
            'type'          => 'file'),
        'z' => array(
            'selector'      => 'li',
            'property'      => 'color',
            'description'   => _("Color of li elements"),
            'value'         => 'yellow',
            'type'          => 'color'),
     )
);
