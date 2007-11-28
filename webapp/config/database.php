<?php defined('SYSPATH') or die('No direct script access.');
/*
 * File: Database
 *  Database connection settings, defined as arrays, or "groups". If no group
 *  name is used when loading the database library, the group named "default"
 *  will be used.
 *  
 *  Each group can be connected to independantly, and multiple groups can be
 *  connected at once.
 *
 * Group Options:
 *  show_errors   - Enable or disable database exceptions
 *  benchmark     - Enable or disable database benchmarking
 *  persistent    - Enable or disable a persistent connection
 *  connection    - DSN identifier: driver://user:password@server/database
 *  character_set - Database character set
 *  table_prefix  - Database table prefix
 *  object        - Enable or disable object results
 */
$config['default'] = array
(
    'show_errors'   => TRUE,
    'benchmark'     => TRUE,
    'persistent'    => FALSE,
    'connection'    => 'mysql://root:root@localhost/arag',
    'character_set' => 'utf8',
    'table_prefix'  => 'arag_',
    'object'        => TRUE,
    'cache'         => FALSE    
);
