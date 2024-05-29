<?php

/**
 * The plugin first class.
 *
 * @package crowdfavorite\tips
 */

namespace Tips;

/**
 * The main class
 */
class Plugin
{

	/**
	 * Class instance.
	 *
	 * @access private
	 * @static
	 *
	 * @var Main
	 */
	private static $instance;

	/**
	 * Get instance of the class.
	 *
	 * @access public
	 * @static
	 *
	 * @return Main
	 */
	public static function getInstance()
	{
		if (!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * The main class construct.
	 */
	public function __construct()
	{
		$this->init();
	}

	/**
	 * Initialize the plugin.
	 */
	private function init()
	{
		add_action(
			'admin_enqueue_scripts',
			[$this, 'enqueueAssets']
		);
	}

	/**
	 * `wp_enqueue_scripts` hook.
	 *
	 * @return void
	 */
	public function enqueueAssets()
	{
		wp_enqueue_script(
			TIPS_HANDLE,
			TIPS_PLUGIN_DIR_URL . 'dist/app.js',
			[],
			filemtime(TIPS_PLUGIN_DIR . 'dist/app.js'),
			true
		);

		wp_enqueue_style(
			TIPS_HANDLE,
			TIPS_PLUGIN_DIR_URL . 'dist/app.css',
			[],
			filemtime(TIPS_PLUGIN_DIR . 'dist/app.css')
		);
	}
}
