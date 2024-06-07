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
use const PCC_PLUGIN_DIR;
use const PCC_PLUGIN_DIR_URL;

/**
 * Class Settings
 *
 * @package PCC
 */
class Settings
{
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
	}

	/**
	 * Register settings page.
	 *
	 * @return void
	 */
	public function addMenu(): void
	{
		add_menu_page(
			esc_html__('PCC', PCC_HANDLE),
			esc_html__('PCC', PCC_HANDLE),
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
		$code = isset($_GET['code']) ? sanitize_text_field($_GET['code']) : null;
		$credentials = $this->getCredentials();
		?>
		<div id="pcc-app">
			<?php if($code): ?>
				<img src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/0.16.1/images/loader-large.gif" alt="spinner"
				class="src">
			<?php endif; ?>

			<?php if(!$credentials): ?>
				<button id="pcc-app-authenticate">Authenticate</button>
			<?php endif; ?>

			<?php if($credentials): ?>
				<button id="pcc-app-list-file">List files</button>
			<?php endif; ?>

		</div>
		<?php
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
				'rest_url' => get_rest_url(get_current_blog_id(),PCC_API_NAMESPACE),
				'nonce' => wp_create_nonce('wp_rest'),
				'plugin_main_page' => menu_page_url(PCC_HANDLE, false),
			] + $this->getCredentials()
		);
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

}
