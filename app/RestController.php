<?php

namespace PCC;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class RestController
{

	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		add_action('rest_api_init', [$this, 'registerRoutes']);
	}

	public function registerRoutes()
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
	public function handleOauthRedirect(WP_REST_Request $request) {
		if (!current_user_can('manage_options')) {
			return new WP_Error('unauthorized', 'You are not authorized to perform this action.', ['status' => 401]);
		}

		$code = $request->get_param('code');
		if (!$code) {
			return new WP_Error('no_code', 'No authorization code provided', ['status' => 400]);
		}

		// Replace with your logic to get the token and persist details
		$credentials = $this->getToken($code);
		if (is_wp_error($credentials)) {
			return $credentials;
		}

		$jwtPayload = $this->parseJwt($credentials['id_token']);
		$this->persistAuthDetails($credentials);

		return new WP_REST_Response(array(
			'email' => $jwtPayload['email'],
		), 200);
	}

	private function getToken($code) {
		// Implement the function to get the token using the authorization code
		// Example:
		// $response = wp_remote_post('https://example.com/oauth/token', array(...));
		// return json_decode(wp_remote_retrieve_body($response), true);
	}

	private function parseJwt($jwt) {
		// Implement the function to parse the JWT
		// Example:
		// list($header, $payload, $signature) = explode('.', $jwt);
		// return json_decode(base64_decode($payload), true);
	}

	private function persistAuthDetails($credentials) {
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
	 * @return WP_REST_Response|WP_Error
	 */
	public function saveCredentials(WP_REST_Request $request)
	{


		$data = $request->get_json_params();

		// Validate input field
		if (empty($data['data'])) {
			$errors['data'] = __('Data payload is required.', PCC_HANDLE);
		}

		// If there are validation errors, return a response with errors
		if (!empty($errors)) {
			return new WP_Error(
				'invalid_data',
				__('Validation failed.', PCC_HANDLE),
				['status' => 400, 'errors' => $errors]
			);
		}

		// If validation passes, insert data into the database
		$inserted = update_option(PCC_CREDENTIALS_OPTION_KEY, serialize($data['data']));

		if (!$inserted) {
			return new WP_Error(
				'database_error',
				__('Failed to save Credentials!', PCC_HANDLE),
				['status' => 500]
			);
		}

		// If database insertion is successful, send a success response
		$response = [
			'message' => __('Credentials saved!', PCC_HANDLE),
			'data' => $data['data'],
		];

		return new WP_REST_Response($response, 200);
	}
}
