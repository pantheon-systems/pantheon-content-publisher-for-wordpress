<?php

namespace PCC;

use PccPhpSdk\api\Response\Article;
use PccPhpSdk\api\ArticlesApi;
use PccPhpSdk\core\PccClient;
use PccPhpSdk\core\PccClientConfig;

class PccSyncManager
{
	/**
	 * @var string $siteId
	 */
	private string $siteId;

	/**
	 * @TODO it shouldn't be hardcoded and to waiting Kevin to create an endpoint to get the token and avoid CLI
	 *
	 * @var string $token
	 */
	private string $token = '5d8d5649-c060-4f29-b267-e11fa1abdf01';

	private PccClient $pccClient;

	public function __construct($siteId)
	{
		// @TODO change it to be dynamic
		$this->siteId = 'yZaQroahEOt2TRdgYPXj';
	}

	/**
	 * Get PccClient instance.
	 *
	 * @return PccClient
	 */
	private function pccClient(): PccClient
	{
		if ($this->pccClient) {
			return $this->pccClient;
		}

		$pccClientConfig = new PccClientConfig(
			$this->siteId,
			$this->token
		);
		$this->pccClient = new PccClient($pccClientConfig);
		return $this->pccClient;
	}

	/**
	 * Store articles from PCC to WordPress.
	 */
	public function storeArticles()
	{
		if (!$this->getIntegrationPostType()) {
			return;
		}
		$articlesApi = new \PccPhpSdk\api\ArticlesApi($this->pccClient());
		$articles = $articlesApi->getAllArticles();
		/** @var Article $article */
		foreach ($articles->articles as $article) {
			$postId = $this->findExistingConnectedPost($article->id);
			$this->createOrUpdatePost($postId, $article);
		}
	}

	private function createOrUpdatePost($postId, Article $article)
	{
		$data = [
			'post_title' => $article->title,
			'post_content' => $article->content,
			'post_status' => 'publish',
			'post_name' => $article->slug,
			'post_type' => $this->getIntegrationPostType(),
		];
		if (!$postId) {
			$data['ID'] = $postId;
			$postId = wp_insert_post($data);
			update_post_meta($postId, PCC_CONTENT_META_KEY, $article->id);
			return;
		}

		$data['ID'] = $postId;
		wp_update_post($data);
	}

	/**
	 * @param $value
	 * @return int|null
	 */
	private function findExistingConnectedPost($value)
	{
		global $wpdb;

		$post_id = $wpdb->get_var($wpdb->prepare(
			"SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %s LIMIT 1",
			PCC_CONTENT_META_KEY,
			$value
		));

		return $post_id ? (int)$post_id : null;
	}

	/**
	 * Get selected integration post type.
	 *
	 * @return false|mixed|null
	 */
	private function getIntegrationPostType()
	{
		return get_option(PCC_INTEGRATION_POST_TYPE_OPTION_KEY);
	}
}
