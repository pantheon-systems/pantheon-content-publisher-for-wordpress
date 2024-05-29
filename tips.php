<?php //phpcs:disable Files.SideEffects.FoundWithSymbols

/**
 * Plugin Name: TIPS
 * Description: TIPS is plugin scaffolding.
 * Plugin URI: https://crowdfavorite.com
 * Author: Al Esc
 * Author URI: https://crowdfavorite.com
 * Version: 0.0.1
 *
 * @package crowdfavorite\tips
 */

namespace Tips;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// Constant Definitions.
define('TIPS_PLUGIN_FILE', __FILE__);
define('TIPS_PLUGIN_DIR', plugin_dir_path(TIPS_PLUGIN_FILE));
define('TIPS_BASENAME', plugin_basename(TIPS_PLUGIN_FILE));
define('TIPS_PLUGIN_DIR_URL', plugin_dir_url(TIPS_PLUGIN_FILE));
define('TIPS_HANDLE', 'tips');

// Only require the autoload.php file if it exists.
// If it does not, assume that it is the root project's responsibility to load the necessary files.
call_user_func_array(function ($rootPath) {
	$autoload = "{$rootPath}vendor/autoload.php";
	if (is_readable($autoload)) {
		require_once $autoload;
	}
	add_action('plugins_loaded', ['Tips\Plugin', 'getInstance'], -10);
}, [TIPS_PLUGIN_DIR]);
