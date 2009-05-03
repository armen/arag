<?php
$config = array
(
    'allowed_image_extensions' => 'jpg,jpeg,png',

    'default_styles' => array
     (

        'background' => array(
            'selector'      => 'body',
            'property'      => 'background-image',
            'description'   => _("Background"),
            'value'         => '../images/background.png',
            'type'          => 'file'),

        'header' => array(
            'selector'      => '#header',
            'property'      => 'background',
            'description'   => _("Logo"),
            'value'         => '../images/header.jpg',
            'type'          => 'file'),

        'links' => array(
            'selector'      => 'a',
            'property'      => 'color',
            'description'   => _("Links"),
            'value'         => '#000',
            'type'          => 'color'),

        'links_hover' => array(
            'selector'      => 'a:hover',
            'property'      => 'color',
            'description'   => _("Links Hover"),
            'value'         => '#666',
            'type'          => 'color'),

        'topmenu_links' => array(
            'selector'      => '#topmenu a',
            'property'      => 'color',
            'description'   => _("Top Menu Links"),
            'value'         => '#e5e5e5',
            'type'          => 'color'),

        'menu_links' => array(
            'selector'      => '#menu a',
            'property'      => 'color',
            'description'   => _("Menu Links"),
            'value'         => '#eee',
            'type'          => 'color'),

        'footermenu_links' => array(
            'selector'      => '#footer-menu a',
            'property'      => 'color',
            'description'   => _("Footer Links"),
            'value'         => '#6d7481',
            'type'          => 'color'),

        'footer_background' => array(
            'selector'      => '#footer',
            'property'      => 'background',
            'description'   => _("Footer Background"),
            'value'         => '../images/footer_background.png',
            'type'          => 'file'),
     )
);
