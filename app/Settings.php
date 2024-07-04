<?php

/**
 * Configure Admin Dashboard Settings UI, logic and assets.
 *
 * @package PCC
 */

namespace PCC;

use PccPhpSdk\api\Query\Enums\PublishingLevel;

use function add_action;
use function filemtime;
use function wp_enqueue_script;

use const PCC_HANDLE;
use const PCC_INTEGRATION_POST_TYPE_OPTION_KEY;
use const PCC_PLUGIN_DIR;
use const PCC_PLUGIN_DIR_URL;
use const PCC_WEBSOCKET_PORT;
use const PCC_WEBSOCKET_URI;
use const PCC_WEBSOCKET_HOST;

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols

/**
 * Class Settings
 *
 * @package PCC
 */
class Settings
{
	/**
	 * Pantheon Cloud Status endpoint required by PCC
	 */
	const PCC_STATUS_ENDPOINT = 'api/pantheoncloud/status';

	/**
	 * Publish document endpoint required by PCC
	 */
	const PCC_PUBLISH_DOCUMENT_ENDPOINT = 'api/pantheoncloud/document/';

	/**
	 * Google Docs edit URL.
	 */
	const PCC_DOCUMENT_EDIT_URL = 'https://docs.google.com/document/d/%s/edit';

	private $pages = [
		'connected-collection'    => PCC_PLUGIN_DIR . 'admin/templates/partials/connected-collection.php',
		'create-collection'       => PCC_PLUGIN_DIR . 'admin/templates/partials/create-collection.php',
		'disconnect-confirmation' => PCC_PLUGIN_DIR . 'admin/templates/partials/disconnect-confirmation.php',
		'setup'                   => PCC_PLUGIN_DIR . 'admin/templates/partials/setup.php',
	];

	public function __construct()
	{
		$this->addHooks();
		$this->addWPCLICommand();
	}

	/**
	 * Add WP CLI command to run the WebSocket server.
	 *
	 * @return void
	 */
	public function addWPCLICommand()
	{
		if (defined('WP_CLI') && WP_CLI) {
			\WP_CLI::add_command('run_websocket_server', [$this, 'runWebSocketServer']);
		}
	}

	/**
	 * Add required hooks.
	 *
	 * @return void
	 */
	private function addHooks(): void
	{
		add_action('template_redirect', [$this, 'registerPantheonCloudStatusEndpoint']);
		add_action('template_redirect', [$this, 'publishDocuments']);
		add_action('admin_menu', [$this, 'addMenu']);
		add_action(
			'admin_enqueue_scripts',
			[$this, 'enqueueAdminAssets']
		);
		add_action(
			'wp_enqueue_scripts',
			[$this, 'enqueueFrontAssets']
		);
		add_action('admin_menu', [$this, 'pluginAdminNotice']);
		add_filter('post_row_actions', [$this, 'addRowActions'], 10, 2);
		add_filter('page_row_actions', [$this, 'addRowActions'], 10, 2);
		add_action('admin_init', [$this,'preventPostEditing']);
		add_filter('wp_list_table_class_name', [ $this, 'overrideAdminWPPostsTable' ]);
		add_filter('init', [ $this, 'initializeWebSocketServer' ]);
	}

	/**
	 * Publish documents from Google Docs.
	 */
	public function publishDocuments()
	{
//		https://pantheon.xyz/api/pantheoncloud/document/1Lw7UH3Rf77_rzvTTkd_CLPpOMnIfV5qXEZtMsnee3IE/?pccGrant=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE3MTk4NzczMzQsImlhdCI6MTcxOTg1NTczNCwic3ViIjoibWdvdWRhQGNyb3dkZmF2b3JpdGUuY29tIiwic2l0ZSI6InlaYVFyb2FoRU90MlRSZGdZUFhqIiwic2NvcGUiOiJwY2NfZ3JhbnQifQ.J5BU3tPt2VPAqAPjdebod4SRCJAwrtGHQ8o4nl5zqCY&publishingLevel=REALTIME
		global $wp;
		$strLen = strlen(static::PCC_PUBLISH_DOCUMENT_ENDPOINT);
		if (substr($wp->request, 0, $strLen) !== static::PCC_PUBLISH_DOCUMENT_ENDPOINT) {
			return;
		}

		// Publish document
		if (isset($_GET['publishingLevel']) && PublishingLevel::PRODUCTION->value === $_GET['publishingLevel']) {
			$parts = explode('/', $wp->request);
			$documentId = end($parts);
			$pcc = new PccSyncManager();
			$postId = $pcc->fetchAndStoreDocument($documentId);

			wp_redirect(get_permalink($postId));
			exit;
		}

		// Publish document
		if (isset($_GET['pccGrant']) && isset($_GET['publishingLevel']) && PublishingLevel::REALTIME->value === $_GET['publishingLevel']) {
			$parts = explode('/', $wp->request);
			$documentId = end($parts);
			$pcc = new PccSyncManager();
			$url = $pcc->preaprePreviewingURL($documentId, sanitize_text_field($_GET['pccGrant']));

			wp_redirect($url);
			exit;
		}
	}

	/**
	 * Register custom endpoint for Pantheon Cloud Status.
	 * This endpoint is used to check if the site is hosted live.
	 * and checked only one time to show your website on PCC google addon
	 */
	public function registerPantheonCloudStatusEndpoint()
	{
		global $wp;
		if (static::PCC_STATUS_ENDPOINT === $wp->request) {
			$url = rest_url(PCC_API_NAMESPACE . '/' . static::PCC_STATUS_ENDPOINT);

			return wp_redirect($url);
		}
	}

	/**
	 * Build the Google Docs edit URL.
	 *
	 * @param string $documentId
	 * @return string
	 */
	private function buildEditDocumentURL($documentId)
	{
		return sprintf(self::PCC_DOCUMENT_EDIT_URL, $documentId);
	}

	/**
	 * Prevent editing of PCC posts.
	 *
	 * @return void
	 */
	public function preventPostEditing()
	{
		global $pagenow;
		// Check if the current page is the post/page edit page
		if ($pagenow == 'post.php' && isset($_GET['post']) && 'edit' === strtolower($_GET['action'])) {
			$documentId = get_post_meta(intval($_GET['post']), PCC_CONTENT_META_KEY, true);
			if (! $documentId) {
				return ;
			}

			wp_redirect($this->buildEditDocumentURL($documentId));
			die(200);
		}
	}

	/**
	 * Add PCC actions to quick edit box.
	 *
	 * @param $actions
	 * @param $post
	 * @return array|mixed
	 */
	public function addRowActions($actions, $post)
	{
		$documentId = get_post_meta($post->ID, PCC_CONTENT_META_KEY, true);
		if (! $documentId) {
			return $actions;
		}

		$customActions = array(
			'pcc' => sprintf(
				'<a href="' . $this->buildEditDocumentURL($documentId) . '" class="pcc-sync" data-id="%d" target="_blank">%s</a>',
				$post->ID,
				esc_html__(
					'Edit in Google Docs',
					PCC_HANDLE
				) . '<svg width="12px" height="12px" viewBox="0 0 24 24" style="display:inline">
                    <g stroke-width="2.1" stroke="#666" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="17 13.5 17 19.5 5 19.5 5 7.5 11 7.5"></polyline>
                    <path d="M14,4.5 L20,4.5 L20,10.5 M20,4.5 L11,13.5"></path></g></svg>'
			)
		);

		$actions = array_merge($customActions, $actions);

		if (isset($actions['edit'])) {
			unset($actions['edit']);
		}

		if (isset($actions['inline hide-if-no-js'])) {
			unset($actions['inline hide-if-no-js']);
		}

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
	public function enqueueAdminAssets(): void
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
	 * Enqueue plugin assets on the WP front.
	 *
	 * @return void
	 */
	public function enqueueFrontAssets(): void
	{
		wp_enqueue_script(
			PCC_HANDLE,
			PCC_PLUGIN_DIR_URL . 'dist/pcc-front.js',
			[],
			filemtime(PCC_PLUGIN_DIR . 'assets/js/pcc-front.js'),
			true
		);

		wp_localize_script(
			PCC_HANDLE,
			'PCCFront',
			[
				'rest_url'      => get_rest_url(get_current_blog_id(), PCC_API_NAMESPACE),
				'websocket_url' => PCC_WEBSOCKET_URL,
				'nonce'         => wp_create_nonce('wp_rest'),
			]
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

	/**
	 * Replace WP_Posts_List_Table with Custom_Posts_List_Table.
	 *
	 * @param   string  $className  The list table class to use.
	 *
	 * @return string The custom list table class.
	 */
	public function overrideAdminWPPostsTable($className)
	{
		if ('WP_Posts_List_Table' === $className) {
			return PccPostsListTable::class;
		}

		return $className;
	}

	/**
	 * Initialize and schedule the WebSocket server to run hourly.
	 *
	 * @return void
	 */
	public function initializeWebSocketServer()
	{
		// Schedule the WebSocket server to run hourly if it's not already scheduled.
		if (!wp_next_scheduled(PCC_WEBSOCKET_SCHEDULE_EVENT)) {
			wp_schedule_event(time(), 'hourly', PCC_WEBSOCKET_SCHEDULE_EVENT);
		}

		// Hook the runServer method to the scheduled event.
		add_action(PCC_WEBSOCKET_SCHEDULE_EVENT, [$this, 'runWebSocketServer']);
	}

	/**
	 * Run the WebSocket server.
	 *
	 * @return void
	 */
	public function runWebSocketServer()
	{
		// Check if the server is already running.
		if ($this->isWebSocketServerRunning()) {
			if (defined('WP_CLI') && WP_CLI) {
				\WP_CLI::success('WebSocket server is already running.');
			}

			return;
		}

		// Command to run the WebSocket server in the background with parameters.
		$command = 'php '
				   . PCC_PLUGIN_DIR
				   . 'app/websocket/StartWebSocketServer.php '
				   . PCC_WEBSOCKET_HOST . ' '
				   . PCC_WEBSOCKET_PORT . ' '
				   . PCC_WEBSOCKET_URI . ' > /dev/null 2>&1 &';

		// Execute the command.
		exec($command);

		if (defined('WP_CLI') && WP_CLI) {
			// Display a success message when the server starts successfully.
			\WP_CLI::success('WebSocket server successfully started.');
		}
	}

	/**
	 * Check if the WebSocket server is already running.
	 *
	 * @return bool True if the server is running, false otherwise.
	 */
	private function isWebSocketServerRunning()
	{
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fsockopen
		$connection = @fsockopen(PCC_WEBSOCKET_HOST, PCC_WEBSOCKET_PORT, $errno, $errstr, 2);

		if (is_resource($connection)) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
			fclose($connection);
			return true;
		}

		return false;
	}
}
