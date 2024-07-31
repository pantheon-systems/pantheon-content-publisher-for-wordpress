<?php

/*
 * REST controller class exposing endpoints for OAuth2 authorization and credentials saving.
 */

namespace PCC;

use WP_REST_Request;
use WP_REST_Response;

use function esc_html__;

use const PCC_ACCESS_TOKEN_OPTION_KEY;
use const PCC_HANDLE;

/**
 * REST controller class.
 */
class RestController
{
	/**
	 * Class constructor, hooking into the REST API initialization.
	 */
	public function __construct()
	{
		add_action('rest_api_init', [$this, 'registerRoutes']);
	}

	/**
	 * Register REST API routes.
	 *
	 * @return void
	 */
	public function registerRoutes(): void
	{
		$endpoints = [
			[
				'route' => '/oauth/access-token',
				'method' => 'POST',
				'callback' => [$this, 'saveAccessToken'],
			],
			[
				'route' => '/collection',
				'method' => 'POST',
				'callback' => [$this, 'createCollection'],
			],
			[
				'route' => '/site',
				'method' => 'POST',
				'callback' => [$this, 'createOrUpdateSite'],
			],
			[
				'route' => '/api-key',
				'method' => 'POST',
				'callback' => [$this, 'createApiKey'],
			],
			[
				'route' => '/collection',
				'method' => 'PUT',
				'callback' => [$this, 'updateCollection'],
			],
			[
				'route' => '/webhook',
				'method' => 'POST',
				'callback' => [$this, 'handleWebhook'],
			],
			[
				'route' => '/webhook',
				'method' => 'PUT',
				'callback' => [$this, 'registerWebhook'],
			],
			[
				'route' => '/disconnect',
				'method' => 'DELETE',
				'callback' => [$this, 'disconnect'],
			],
			[
				'route' => 'api/pantheoncloud/status',
				'method' => 'GET',
				'callback' => [$this, 'pantheonCloudStatusCheck'],
			],
		];

		foreach ($endpoints as $endpoint) {
			register_rest_route(PCC_API_NAMESPACE, $endpoint['route'], [
				'methods' => $endpoint['method'],
				'callback' => $endpoint['callback'],
				'permission_callback' => [$this, 'permissionCallback'],
			]);
		}
	}

	/**
	 * Public endpoint for to check website publish status.
	 *
	 * @return WP_REST_Response
	 */
	public function pantheonCloudStatusCheck()
	{
		return new WP_REST_Response((object)[]);
	}

	/**
	 * Handle incoming webhook requests.
	 * @return void|WP_REST_Response
	 */
	public function handleWebhook(WP_REST_Request $request)
	{
		if (get_option(PCC_WEBHOOK_SECRET_OPTION_KEY) !== $request->get_header('x-pcc-webhook-secret')) {
			return new WP_REST_Response(
				esc_html__('You are not authorized to perform this action', PCC_HANDLE),
				401
			);
		}

		$event = $request->get_param('event');
		$payload = $request->get_param('payload');
		$isPCCConfiguredCorrectly = (new PccSyncManager())->isPCCConfigured();

		// Bail if current website id is not correctly configured
		if (!$isPCCConfiguredCorrectly) {
			return new WP_REST_Response(
				esc_html__('Website is not correctly configured', PCC_HANDLE),
				500
			);
		}

		if (!is_array($payload) || !isset($payload['articleId']) || empty($payload['articleId'])) {
			return new WP_REST_Response(esc_html__('Invalid article ID in payload', PCC_HANDLE), 400);
		}

		$articleId = sanitize_text_field($payload['articleId']);
		$pccManager = new PccSyncManager();
		switch ($event) {
			case 'article.unpublish':
				$pccManager->unPublishPostByDocumentId($articleId);
				break;
			default:
				return new WP_REST_Response(
					esc_html__('Event type is currently unsupported', PCC_HANDLE),
					200
				);
		}
	}

	/**
	 * @return true
	 */
	public function permissionCallback()
	{
		rest_cookie_check_errors(null);

		return true;
	}

	public function createCollection(WP_REST_Request $request): WP_REST_Response
	{
		$siteId = sanitize_text_field($request->get_param('site_id') ?: '');
		if (!$siteId) {
			return new WP_REST_Response([
				'message' => esc_html__('Missing site id', PCC_HANDLE),
			], 400);
		}

		$postType = sanitize_text_field($request->get_param('post_type') ?: '');
		if (!$postType) {
			return new WP_REST_Response([
				'message' => esc_html__('Missing integration post type', PCC_HANDLE),
			], 400);
		}

		update_option(PCC_SITE_ID_OPTION_KEY, $siteId);
		update_option(PCC_INTEGRATION_POST_TYPE_OPTION_KEY, $postType);

		return new WP_REST_Response(esc_html__('Saved!', PCC_HANDLE));
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function createOrUpdateSite(WP_REST_Request $request): WP_REST_Response
	{
		// Check if you are authorized
		if (!current_user_can('manage_options')) {
			return new WP_REST_Response(esc_html__('You are not authorized to perform this action.', PCC_HANDLE), 401);
		}
		// Check access token is set
		if (!get_option(PCC_ACCESS_TOKEN_OPTION_KEY)) {
			return new WP_REST_Response(esc_html__('Access token is not set yet', PCC_HANDLE), 401);
		}

		$siteManager = new PccSiteManager();
		$response = $siteManager->getSiteID();
		if (is_wp_error($response)) {
			return new WP_REST_Response($response->get_error_message(), $response->get_error_code());
		}

		// Update with the site id
		update_option(PCC_SITE_ID_OPTION_KEY, $response);
		update_option(PCC_ENCODED_SITE_URL_OPTION_KEY, md5(wp_parse_url(site_url())['host']));
		return new WP_REST_Response($response);
	}

	/**
	 * Create API key for the site
	 *
	 * @return WP_REST_Response
	 */
	public function registerWebhook(): WP_REST_Response
	{
		// Check access token is set
		if (!get_option(PCC_ACCESS_TOKEN_OPTION_KEY)) {
			return new WP_REST_Response(esc_html__('Access token is not set yet', PCC_HANDLE), 400);
		}

		// Check site id is set
		if (!get_option(PCC_SITE_ID_OPTION_KEY)) {
			return new WP_REST_Response(esc_html__('Site is not created yet', PCC_HANDLE), 400);
		}

		// Check if you are authorized
		if (!current_user_can('manage_options')) {
			return new WP_REST_Response(esc_html__('You are not authorized to perform this action.', PCC_HANDLE), 401);
		}

		$siteManager = new PccSiteManager();
		if ($siteManager->registerWebhook()) {
			return new WP_REST_Response(esc_html__('Webhook registered', PCC_HANDLE));
		}

		return new WP_REST_Response(esc_html__('Error while register webhook', PCC_HANDLE), 400);
	}

	/**
	 * Create Api Key
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function createApiKey(): WP_REST_Response
	{
		// Check site id is set
		if (!get_option(PCC_SITE_ID_OPTION_KEY)) {
			return new WP_REST_Response(esc_html__('Site is not created yet', PCC_HANDLE), 400);
		}

		// Check if you are authorized
		if (!current_user_can('manage_options')) {
			return new WP_REST_Response(esc_html__('You are not authorized to perform this action.', PCC_HANDLE), 401);
		}

		$siteManager = new PccSiteManager();
		$apiKey = $siteManager->createSiteApiKey();
		if ($apiKey) {
			update_option(PCC_API_KEY_OPTION_KEY, $apiKey);
			return new WP_REST_Response(esc_html__('API created', PCC_HANDLE));
		}

		return new WP_REST_Response(esc_html__('Error while creating API key', PCC_HANDLE), 400);
	}

	/**
	 * Update collection settings
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function updateCollection(WP_REST_Request $request): WP_REST_Response
	{
		$siteId = sanitize_text_field($request->get_param('site_id') ?: '');
		if ($siteId) {
			update_option(PCC_SITE_ID_OPTION_KEY, $siteId);
		}

		$postType = sanitize_text_field($request->get_param('post_type') ?: '');
		if ($postType) {
			update_option(PCC_INTEGRATION_POST_TYPE_OPTION_KEY, $postType);
		}

		return new WP_REST_Response(esc_html__('Saved!', PCC_HANDLE));
	}

	/**
	 * Save Credentials into database
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function saveAccessToken(WP_REST_Request $request): WP_REST_Response
	{
		if (!current_user_can('manage_options')) {
			return new WP_REST_Response(esc_html__('You are not authorized to perform this action.', PCC_HANDLE), 401);
		}

		$accessToken = sanitize_text_field($request->get_param('access_token'));

		// Validate input field
		if (empty($accessToken)) {
			return new WP_REST_Response(
				esc_html__('Access Token cannot be empty.', PCC_HANDLE),
				400
			);
		}

		update_option(PCC_ACCESS_TOKEN_OPTION_KEY, $accessToken);
		return new WP_REST_Response(
			esc_html__('Access Token saved.', PCC_HANDLE),
			200
		);
	}

	/**
	 * Delete saved data from the database.
	 *
	 * @return WP_REST_Response
	 */
	public function disconnect()
	{
		if (!current_user_can('manage_options')) {
			return new WP_REST_Response(esc_html__('You are not authorized to perform this action.', PCC_HANDLE), 401);
		}

		// Disconnect the site
		$manager = new PccSyncManager();
		$manager->disconnect();

		return new WP_REST_Response(
			esc_html__('Saved Data deleted.', PCC_HANDLE),
			200
		);
	}
}
