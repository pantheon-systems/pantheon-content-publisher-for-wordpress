<?php //phpcs:disable Files.SideEffects.FoundWithSymbols

/**
 * Plugin Name: Pantheon Content Cloud for WordPress
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
if (!defined('ABSPATH')) {
	exit;
}

define('PCC_PLUGIN_FILE', __FILE__);
define('PCC_PLUGIN_DIR', plugin_dir_path(PCC_PLUGIN_FILE));
define('PCC_BASENAME', plugin_basename(PCC_PLUGIN_FILE));
define('PCC_PLUGIN_DIR_URL', plugin_dir_url(PCC_PLUGIN_FILE));
define('PCC_HANDLE', 'pcc');
define('PCC_CREDENTIALS_OPTION_KEY', 'pcc_credentials');
define('PCC_API_NAMESPACE', 'pcc/v1');

call_user_func(static function ($rootPath) {
	$autoload = "{$rootPath}vendor/autoload.php";
	if (is_readable($autoload)) {
		require_once $autoload;
	}
	add_action('plugins_loaded', [Plugin::class, 'getInstance'], -10);
}, PCC_PLUGIN_DIR);
