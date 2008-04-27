<?php
/**
 * Name of the front controller for this application. Default: index.php
 *
 * This can be removed by using URL rewriting.
 */
$config['index_page'] = basename($_SERVER['SCRIPT_NAME']);

/*
 * Domain name, with the installation directory. Default: localhost/kohana/
 */
$config['site_domain'] = $_SERVER['SERVER_NAME'].substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], '/'.$config['index_page']));

/**
 * Default protocol used to access the website. Default: http
 */
$config['site_protocol'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';


/**
 * Fake file extension that will be added to all generated URLs. Example: .html
 */
$config['url_suffix'] = '';

/**
 * Enable or disable gzip output compression. This can dramatically decrease
 * server bandwidth usage, at the cost of slightly higher CPU usage. Set to
 * the compression level (1-9) that you want to use, or FALSE to disable.
 *
 * Do not enable this option if you are using output compression in php.ini!
 */
$config['output_compression'] = FALSE;

/**
 * Enable or disable global XSS filtering of GET, POST, and SERVER data. This
 * option also accepts a string to specify a specific XSS filtering tool.
 */
$config['global_xss_filtering'] = TRUE;

/**
 * Enable or disable dynamic setting of configuration options. By default, all
 * configuration options are read-only.
 */
$config['allow_config_set'] = TRUE;

/**
 * Enable or display displaying of Kohana error pages. This will not affect
 * logging. Turning this off will disable ALL error pages.
 */
$config['display_errors'] = TRUE;

/**
 * Enable or display statistics in the final output. Stats are replaced via
 * specific strings, such as {execution_time}.
 *
 * @see http://doc.kohanaphp.com/general/configuration/config
 */
$config['render_stats'] = TRUE;

/**
 * Filename prefixed used to determine extensions. For example, an
 * extension to the Controller class would be named MY_Controller.php.
 */
$config['extension_prefix'] = 'Arag_';

/**
 * Additional resource paths, or "modules". Each path can either be absolute
 * or relative to the docroot. Modules can include any resource that can exist
 * in your application directory, configuration files, controllers, views, etc.
 */
$config['modules'] = array
(
	// MODPATH.'auth',   // Authentication
	// MODPATH.'forge',  // Form generation
	// MODPATH.'kodoc',  // Self-generating documentation
	// MODPATH.'media',  // Media caching and compression
	// MODPATH.'gmaps',  // Google Maps integration
);
