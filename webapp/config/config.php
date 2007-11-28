<?php defined('SYSPATH') or die('No direct script access.');

/*
 * File: config.php
 *  This configuration file is unique to every application.
 *
 * Options:
 *  site_domain          - domain and installation directory
 *  site_protocol        - protocol used to access the site, usually HTTP
 *  index_page           - name of the front controller, can be removed with URL rewriting
 *  output_compression   - enable or disable gzip output compression 
 *  url_suffix           - an extension that will be added to all generated URLs
 *  allow_config_set     - enable or disable setting of Config items
 *  global_xss_filtering - enable or disable XSS attack filtering on all user input
 *  extension_prefix     - filename prefix for library extensions
 *  include_paths        - extra Kohana resource paths, see <Kohana.find_file>
 *  autoload             - libraries and models to be loaded with the controller
 */

$config = Array
(
    'site_domain'          => $_SERVER['SERVER_NAME'] . 
                              substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], '/'.basename($_SERVER['SCRIPT_NAME']))),
    'site_protocol'        => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http',
    'index_page'           => 'index.php',
    'output_compression'   => TRUE,    
    'url_suffix'           => '',
    'allow_config_set'     => True,
    'global_xss_filtering' => False,
    'extension_prefix'     => 'Arag_',
    'include_paths'        => Array
    (
    ),
    'autoload'             => Array
    (
        'libraries' => 'session',
        'models'    => '',
    )
);
