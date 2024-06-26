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

	private $pccClient;

	public function __construct($siteId = null)
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

	public function fetchAndStoreDocument($documentId)
	{
		$articlesApi = new ArticlesApi($this->pccClient());
		$article = $articlesApi->getArticleById($documentId);

		return $this->storeArticle($article);
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
			$this->storeArticle($article);
		}
	}

	/**
	 * Store article.
	 *
	 * @param Article $article
	 * @return int
	 */
	private function storeArticle(Article $article)
	{
		$postId = $this->findExistingConnectedPost($article->id);

		return $this->createOrUpdatePost($postId, $article);
	}

	/**
	 * Create or update post.
	 *
	 * @param $postId
	 * @param Article $article
	 * @return int post id
	 */
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
			return $postId;
		}

		$data['ID'] = $postId;
		wp_update_post($data);
		return $postId;
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

	/**
	 * Publish post by document id.
	 *
	 * @param $documentId
	 * @return void
	 */
	public function unPublishPostByDocumentId($documentId)
	{
		$postId = $this->findExistingConnectedPost($documentId);
		if (!$postId) {
			return;
		}

		wp_update_post([
			'ID' => $postId,
			'post_status' => 'draft',
		]);
	}
}