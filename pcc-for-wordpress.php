<?php

//phpcs:disable Files.SideEffects.FoundWithSymbols

/**
 * Plugin Name: Pantheon Content Publisher
 * Description: Publish WordPress content from Google Docs with Pantheon Content Cloud.
 * Plugin URI: https://pantheon.io
 * Author: Pantheon
 * Author URI: https://pantheon.io
 * Version: 0.0.1
 *
 * @package pantheon\pcc-for-wordpress
 */

namespace PCC;

// Exit if accessed directly.
if (! defined('ABSPATH')) {
	exit;
}

define('PCC_PLUGIN_FILE', __FILE__);
define('PCC_PLUGIN_DIR', plugin_dir_path(PCC_PLUGIN_FILE));
define('PCC_BASENAME', plugin_basename(PCC_PLUGIN_FILE));
define('PCC_PLUGIN_DIR_URL', plugin_dir_url(PCC_PLUGIN_FILE));
define('PCC_HANDLE', 'pcc');
define('PCC_CREDENTIALS_OPTION_KEY', 'pcc_credentials');
define('PCC_SITE_ID_OPTION_KEY', 'pcc_site_id');
define('PCC_INTEGRATION_POST_TYPE_OPTION_KEY', 'pcc_integration_post_type');
define('PCC_API_NAMESPACE', 'pcc/v1');
define('PCC_CONTENT_META_KEY', 'pcc_id');

/**
 * Function to get site URL without a scheme (http:// or https://)
 *
 * @return string Site URL without scheme
 */
function getSiteUrlWithoutScheme()
{
	$site_url = site_url();
	$parsed_url = parse_url($site_url);

	// Check if parse_url() returned false or an invalid array
	if (!$parsed_url || !is_array($parsed_url)) {
		// Handle the case where parse_url() failed
		return 'localhost';
	}

	// Check if the host part exists.
	$host = isset($parsed_url['host']) ? $parsed_url['host'] : 'localhost';

	// Check if the path part exists and prepend it with a slash.
	$path = isset($parsed_url['path']) ? '/' . ltrim($parsed_url['path'], '/') : '';

	// Combine host and path to form the URL without a scheme.
	return $host . $path;
}

define('PCC_WEBSOCKET_HOST', getSiteUrlWithoutScheme());
define('PCC_WEBSOCKET_PORT', 8080);
define('PCC_WEBSOCKET_URI', '/ws');
define('PCC_WEBSOCKET_URL', 'ws://' . PCC_WEBSOCKET_HOST . ':' . PCC_WEBSOCKET_PORT . PCC_WEBSOCKET_URI);
define('PCC_WEBSOCKET_SCHEDULE_EVENT', 'pcc_execute_websocket_server');


call_user_func(static function ($rootPath) {
	$autoload = "{$rootPath}vendor/autoload.php";
	if (is_readable($autoload)) {
		require_once $autoload;
	}
	add_action('plugins_loaded', [Plugin::class, 'getInstance'], -10);
}, PCC_PLUGIN_DIR);
