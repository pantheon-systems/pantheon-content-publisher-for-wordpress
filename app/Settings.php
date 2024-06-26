<?php

/**
 * Configure Admin Dashboard Settings UI, logic and assets.
 *
 * @package PCC
 */

namespace PCC;

use function add_action;
use function filemtime;
use function wp_enqueue_script;

use const PCC_HANDLE;
use const PCC_INTEGRATION_POST_TYPE_OPTION_KEY;
use const PCC_PLUGIN_DIR;
use const PCC_PLUGIN_DIR_URL;

/**
 * Class Settings
 *
 * @package PCC
 */
class Settings
{
	private $pages = [
		'connected-collection'    => PCC_PLUGIN_DIR . 'admin/templates/partials/connected-collection.php',
		'create-collection'       => PCC_PLUGIN_DIR . 'admin/templates/partials/create-collection.php',
		'disconnect-confirmation' => PCC_PLUGIN_DIR . 'admin/templates/partials/disconnect-confirmation.php',
		'setup'                   => PCC_PLUGIN_DIR . 'admin/templates/partials/setup.php',
	];

	public function __construct()
	{
		$this->addHooks();
	}

	/**
	 * Add required hooks.
	 *
	 * @return void
	 */
	private function addHooks(): void
	{
		add_action('admin_menu', [$this, 'addMenu']);
		add_action(
			'admin_enqueue_scripts',
			[$this, 'enqueueAssets']
		);
		add_action('admin_menu', [$this, 'pluginAdminNotice']);
		add_filter('post_row_actions', [$this, 'addRowActions'], 10, 2);
	}

	public function addRowActions($actions, $post)
	{
		$post_type = get_option(PCC_INTEGRATION_POST_TYPE_OPTION_KEY);
		if ($post->post_type !== $post_type) {
			return $actions;
		}
		$pcc_post = get_post_meta($post->ID, PCC_CONTENT_META_KEY, true);
		if (! $pcc_post) {
			return $actions;
		}
		$actions['pcc'] = sprintf(
			'<a href="#" class="pcc-sync" data-id="%d">%s</a>',
			$post->ID,
			esc_html__(
				'Edit in Google Docs',
				PCC_HANDLE
			) . '<svg width="12px" height="12px" viewBox="0 0 24 24" style="display:inline"><g stroke-width="2.1" stroke="#666" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 13.5 17 19.5 5 19.5 5 7.5 11 7.5"></polyline><path d="M14,4.5 L20,4.5 L20,10.5 M20,4.5 L11,13.5"></path></g></svg>'
		);

		return $actions;
	}

	/**
	 * Register settings page.
	 *
	 * @return void
	 */
	public function addMenu(): void
	{
		add_menu_page(
			esc_html__('Pantheon Content Publisher', PCC_HANDLE),
			esc_html__('Pantheon Content Publisher', PCC_HANDLE),
			'manage_options',
			PCC_HANDLE,
			[$this, 'renderSettingsPage'],
			'dashicons-format-aside',
			20
		);
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function renderSettingsPage(): void
	{
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$view = isset($_GET['view']) ? $_GET['view'] : null;
		if ($view && isset($this->pages[$view])) {
			require $this->pages[$view];

			return;
		}

		// Site id is set and Credentials are set
		if ($this->getSiteId() && $this->getCredentials()) {
			require $this->pages['connected-collection'];

			return;
		}
		if ($this->getCredentials()) {
			require $this->pages['create-collection'];

			return;
		}
		require $this->pages['setup'];
	}

	/**
	 * @return false|mixed|null
	 */
	private function getSiteId()
	{
		return get_option(PCC_SITE_ID_OPTION_KEY);
	}

	/**
	 * Get credentials from the database.
	 *
	 * @return array|mixed
	 */
	private function getCredentials()
	{
		$pccCredentials = get_option(PCC_CREDENTIALS_OPTION_KEY);

		return $pccCredentials ? unserialize($pccCredentials) : [];
	}

	/**
	 * Enqueue plugin assets on the WP Admin Dashboard.
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

		wp_localize_script(
			PCC_HANDLE,
			'PCCAdmin',
			[
				'rest_url'         => get_rest_url(get_current_blog_id(), PCC_API_NAMESPACE),
				'nonce'            => wp_create_nonce('wp_rest'),
				'plugin_main_page' => menu_page_url(PCC_HANDLE, false),
				'site_url'         => site_url(),
			] + ['credentials' => $this->getCredentials()]
		);
	}

	/**
	 * Show notification when authentication details are not set or collection not created
	 */
	public function pluginAdminNotice()
	{
		global $pagenow;
		if ($pagenow !== 'plugins.php') {
			return;
		}

		// Show notification when authentication details are not set or collection not created
		if (! $this->getCredentials() || ! $this->getSiteId()) {
			add_action('admin_notices', [$this, 'pluginNotification']);
		}
	}

	/**
	 * Plugin notification to continue setup
	 */
	public function pluginNotification()
	{
		require PCC_PLUGIN_DIR . 'admin/templates/partials/plugin-notification.php';
	}
}
