<?php

namespace PCC;

use PccPhpSdk\api\ArticlesApi;
use PccPhpSdk\api\Query\Enums\ContentType;
use PccPhpSdk\api\Query\Enums\PublishingLevel;
use PccPhpSdk\api\Response\Article;
use PccPhpSdk\core\PccClient;
use PccPhpSdk\core\PccClientConfig;

class PccSyncManager
{
	/**
	 * @var string $siteId
	 */
	private string $siteId;
	private string $apiKey;

	public function __construct()
	{
		$this->siteId = get_option(PCC_SITE_ID_OPTION_KEY);
		$this->apiKey = get_option(PCC_API_KEY_OPTION_KEY);
	}

	/**
	 * Fetch and store document.
	 *
	 * @param $documentId
	 * @param PublishingLevel $publishingLevel
	 * @param bool $isDraft
	 * @return int
	 */
	public function fetchAndStoreDocument(
		$documentId,
		PublishingLevel $publishingLevel,
		bool $isDraft = false,
	): int {
		$articlesApi = new ArticlesApi($this->pccClient());
		$article = $articlesApi->getArticleById(
			$documentId,
			[
				'id',
				'slug',
				'title',
				'siteId',
				'tags',
				'content',
				'snippet',
				'metadata',
			],
			$publishingLevel,
			ContentType::TREE_PANTHEON_V2
		);

		return $this->storeArticle($article, $isDraft);
	}

	/**
	 * Get PccClient instance.
	 *
	 * @param string|null $pccGrant
	 * @return PccClient
	 */
	public function pccClient(string $pccGrant = null): PccClient
	{
		$args = [$this->siteId, $this->apiKey];
		if ($pccGrant) {
			$args = [$this->siteId, '', null, $pccGrant];
		}

		return new PccClient(new PccClientConfig(...$args));
	}

	/**
	 * Store article.
	 *
	 * @param Article $article
	 * @param bool $isDraft
	 * @return int
	 */
	private function storeArticle(Article $article, bool $isDraft = false)
	{
		$postId = $this->findExistingConnectedPost($article->id);

		return $this->createOrUpdatePost($postId, $article, $isDraft);
	}

	/**
	 * @param $value
	 * @return int|null
	 */
	public function findExistingConnectedPost($value)
	{
		global $wpdb;

		$post_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %s LIMIT 1",
				PCC_CONTENT_META_KEY,
				$value
			)
		);

		return $post_id ? (int)$post_id : null;
	}

	/**
	 * Create or update post.
	 *
	 * @param $postId
	 * @param Article $article
	 * @param bool $isDraft
	 * @return int post id
	 */
	private function createOrUpdatePost($postId, Article $article, bool $isDraft = false)
	{
		$data = [
			'post_title' => $article->title,
			'post_content' => $article->content,
			'post_status' => $isDraft ? 'draft' : 'publish',
			'post_name' => $article->slug,
			'post_type' => $this->getIntegrationPostType(),
		];
		if (!$postId) {
			$postId = wp_insert_post($data);
			update_post_meta($postId, PCC_CONTENT_META_KEY, $article->id);
			$this->syncPostMetaAndTags($postId, $article);
			return $postId;
		}

		$data['ID'] = $postId;
		wp_update_post($data);
		$this->syncPostMetaAndTags($postId, $article);
		return $postId;
	}

	/**
	 * Update post tags.
	 *
	 * @param $postId
	 * @param Article $article
	 */
	private function syncPostMetaAndTags($postId, Article $article): void
	{
		if (isset($article->tags) && is_array($article->tags)) {
			wp_set_post_terms($postId, $article->tags, 'post_tag', false);
		}

		if (!isset($article->metadata)) {
			return;
		}

		$this->setPostFeatureImage($postId, $article);
		wp_set_post_categories($postId, $this->findArticleCategories($article));

		// Check if Yoast SEO is installed and active.
		$activePlugins = apply_filters('active_plugins', get_option('active_plugins'));
		if (in_array('wordpress-seo/wp-seo.php', $activePlugins)) {
			if (!get_post_meta($postId, '_yoast_wpseo_metadesc', true)) {
				update_post_meta($postId, '_yoast_wpseo_metadesc', $article->metadata['description']);
			}
		}
	}

	/**
	 * Set the post feature image.
	 *
	 * @param $postId
	 * @param Article $article
	 */
	private function setPostFeatureImage($postId, Article $article)
	{
		if (!isset($article->metadata['FeaturedImage'])) {
			return;
		}

		// If the feature image is empty, delete the existing thumbnail.
		$featuredImageURL = $article->metadata['FeaturedImage'] ?? '';
		if ('' === $featuredImageURL) {
			delete_post_thumbnail($postId);
			return;
		}

		// Check if there was an existing image.
		// @TODO refactor this condition to search for Post id using the feature image URL
		if ($existingImageId = get_post_thumbnail_id($postId)) {
			$existingUrl = get_post_meta($existingImageId, 'pcc_feature_image_url', true);
			if ($existingUrl === $featuredImageURL) {
				return;
			}
		}

		// Ensure media_sideload_image function is available.
		if (!function_exists('media_sideload_image')) {
			require_once(ABSPATH . 'wp-admin/includes/media.php');
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			require_once(ABSPATH . 'wp-admin/includes/image.php');
		}

		// Download and attach the new image.
		$imageId = \media_sideload_image($featuredImageURL, $postId, null, 'id');

		if (is_int($imageId)) {
			update_post_meta($imageId, 'pcc_feature_image_url', $featuredImageURL);
			// Set as the featured image.
			set_post_thumbnail($postId, $imageId);
		}

		return;
	}

	private function findArticleCategories(Article $article): array
	{
		$categories = isset($article->metadata['Categories']) ? explode(',', (string) $article->metadata['Categories']) : [];
		$categories = array_filter($categories);
		if (!$categories) {
			return [];
		}

		return $this->findOrCreateCategories($categories);
	}

	/**
	 * Check if a category exists by name.
	 * If the category does not exist, create it.
	 *
	 * @param array $categories array of categories names to check or create.
	 *
	 * @return array The categories IDs.
	 */
	private function findOrCreateCategories(array $categories): array
	{
		$ids = [];
		if (!function_exists('wp_insert_category')) {
			require_once(ABSPATH . 'wp-admin/includes/taxonomy.php');
		}

		foreach ($categories as $category) {
			$categoryId = (int) get_cat_ID($category);
			if (0 === $categoryId) {
				$newCategory = wp_insert_category([
					'cat_name' => $category,
				]);

				if (!is_wp_error($newCategory)) {
					$categoryId = $newCategory;
				}
			}
			$ids[] = $categoryId;
		}

		return $ids;
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
	 * Store articles from PCC to WordPress.
	 */
	public function storeArticles()
	{
		if (!$this->getIntegrationPostType()) {
			return;
		}
		$articlesApi = new ArticlesApi($this->pccClient());
		$articles = $articlesApi->getAllArticles();
		/** @var Article $article */
		foreach ($articles->articles as $article) {
			$this->storeArticle($article);
		}
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

	/**
	 * Get preview link.
	 * @param string $documentId
	 * @param $postId
	 * @return string
	 */
	public function preparePreviewingURL(string $documentId, $postId = null): string
	{
		$postId = $postId ?: $this->findExistingConnectedPost($documentId);
		return add_query_arg(
			[
				'preview' => 'google_document',
				'publishing_level' => PublishingLevel::REALTIME->value,
				'document_id' => $documentId,
			],
			get_permalink($postId)
		);
	}

	/**
	 * Disconnect PCC.
	 */
	public function disconnect()
	{
		delete_option(PCC_ACCESS_TOKEN_OPTION_KEY);
		delete_option(PCC_SITE_ID_OPTION_KEY);
		delete_option(PCC_ENCODED_SITE_URL_OPTION_KEY);
		delete_option(PCC_INTEGRATION_POST_TYPE_OPTION_KEY);
		delete_option(PCC_WEBHOOK_SECRET_OPTION_KEY);
		delete_option(PCC_API_KEY_OPTION_KEY);

		$this->removeMetaDataFromPosts();
	}

	/**
	 * Remove all saved meta from posts
	 *
	 * @return void
	 */
	private function removeMetaDataFromPosts()
	{
		global $wpdb;
		// Delete all post meta entries with the key 'terminate'
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s",
				PCC_CONTENT_META_KEY
			)
		);
	}

	/**
	 * Check if PCC is configured.
	 *
	 * @return bool
	 */
	public function isPCCConfigured(): bool
	{
		$accessToken = get_option(PCC_ACCESS_TOKEN_OPTION_KEY);
		$siteId = get_option(PCC_SITE_ID_OPTION_KEY);
		$encodedSiteURL = get_option(PCC_ENCODED_SITE_URL_OPTION_KEY);
		$apiKey = get_option(PCC_API_KEY_OPTION_KEY);
		return true;
		if (!$accessToken || !$siteId || !$apiKey || !$encodedSiteURL) {
			return false;
		}

		$currentHashedSiteURL = md5(wp_parse_url(site_url())['host']);
		if ($encodedSiteURL === $currentHashedSiteURL) {
			return true;
		}

		return false;
	}
}
