<?php
/*
 * REST controller class exposing endpoints for OAuth2 authorization and credentials saving.
 */

namespace PCC;

use WP_REST_Request;
use WP_REST_Response;

use function esc_html__;
use function serialize;

use const PCC_CREDENTIALS_OPTION_KEY;
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
				'route' => '/oauth/redirect',
				'method' => 'GET',
				'callback' => [$this, 'handleOauthRedirect'],
			],
			[
				'route' => '/oauth/credentials',
				'method' => 'POST',
				'callback' => [$this, 'saveCredentials'],
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
	 * Handle OAuth2 redirect.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function handleOauthRedirect(WP_REST_Request $request): WP_REST_Response
	{
		$code = $request->get_param('code');
		if (!$code) {
			return new WP_REST_Response([
				'message' => esc_html__('No authorization code provided', PCC_HANDLE),
			], 400);
		}

		$credentials = $this->getToken($code);
		if (is_wp_error($credentials)) {
			return new WP_REST_Response([
				'message' => esc_html__('Missing authorization token', PCC_HANDLE),
			], 400);
		}

		$jwtPayload = $this->parseJwt($credentials['id_token']);
		$this->persistAuthDetails($credentials);

		return new WP_REST_Response([
			'email' => $jwtPayload['email'],
		], 200);
	}

	/**
	 * @param $code
	 *
	 * @return void
	 */
	private function getToken($code)
	{
		// Implement the function to get the token using the authorization code
		// Example:
		// $response = wp_remote_post('https://example.com/oauth/token', array(...));
		// return json_decode(wp_remote_retrieve_body($response), true);
	}

	/**
	 * @param $jwt
	 *
	 * @return void
	 */
	private function parseJwt($jwt)
	{
		// Implement the function to parse the JWT
		// Example:
		// list($header, $payload, $signature) = explode('.', $jwt);
		// return json_decode(base64_decode($payload), true);
	}

	/**
	 * @param $credentials
	 *
	 * @return void
	 */
	private function persistAuthDetails($credentials)
	{
		// Implement the function to persist authentication details
		// Example:
		// update_option('oauth_credentials', $credentials);
	}

	/**
	 * @return true
	 */
	public function permissionCallback()
	{
		rest_cookie_check_errors(null);

		return true;
	}

	/**
	 * Save Credentials into database
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function saveCredentials(WP_REST_Request $request): WP_REST_Response
	{
		if (!current_user_can('manage_options')) {
			return new WP_REST_Response(esc_html__('You are not authorized to perform this action.', PCC_HANDLE), 401);
		}

		$data = $request->get_params();
		// Validate input field
		if (empty($data)) {
			return new WP_REST_Response(
				esc_html__('Validation failed.', PCC_HANDLE),
				400
			);
		}

		return update_option(PCC_CREDENTIALS_OPTION_KEY, serialize($data)) ?
			new WP_REST_Response(
				esc_html__('Credentials saved.', PCC_HANDLE),
				200
			) :
			new WP_REST_Response(
				esc_html__('Failed to save Credentials.', PCC_HANDLE),
				500
			);
	}
}
