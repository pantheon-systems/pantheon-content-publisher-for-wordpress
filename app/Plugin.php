<?php

/**
 * The plugin first class.
 *
 * @package crowdfavorite\tips
 */

namespace PCC;

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
	 * @var Plugin
	 */
	private static Plugin $instance;

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
	private function init(): void
	{
		add_action(
			'admin_enqueue_scripts',
			[$this, 'enqueueAssets']
		);
	}

	/**
	 * Get instance of the class.
	 *
	 * @access public
	 * @static
	 *
	 * @return Plugin
	 */
	public static function getInstance(): Plugin
	{
		if (!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * `wp_enqueue_scripts` hook.
	 *
	 * @return void
	 */
	public function enqueueAssets(): void
	{
		wp_enqueue_script(
			PCC_HANDLE,
			PCC_PLUGIN_DIR_URL . 'dist/app.js',
			[],
			filemtime(PCC_PLUGIN_DIR . 'dist/app.js'),
			true
		);

		wp_enqueue_style(
			PCC_HANDLE,
			PCC_PLUGIN_DIR_URL . 'dist/app.css',
			[],
			filemtime(PCC_PLUGIN_DIR . 'dist/app.css')
		);
	}
}
