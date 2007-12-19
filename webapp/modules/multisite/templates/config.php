<?php defined('SYSPATH') or die('No direct script access.');

$config['core'] = array
(
    'parent_base_url' => '%parent_base_url%'
);

$config['database'] = array
(
    'show_errors'   => FALSE,
    'benchmark'     => FALSE,
    'persistent'    => FALSE,
    'connection'    => '%dsn%',
    'character_set' => 'utf8',
    'table_prefix'  => '%table_prefix%',
    'object'        => TRUE
);
