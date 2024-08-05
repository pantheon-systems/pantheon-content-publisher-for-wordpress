<div class="pcc-content">
	<?php
	require 'header.php';
	?>
	<div class="page-content">
		<?php require PCC_PLUGIN_DIR . 'admin/templates/partials/spinner.php'; ?>
		<div id="pcc-content">
			<div class="create-collection-page">
				<?php require PCC_PLUGIN_DIR . 'admin/templates/partials/error-message.php'; ?>
				<div class="page-grid">
					<div class="col-span-7 justify-self-start">
						<h1 class="page-header mt-[1.875rem]">
							<?php esc_html_e('Create your first content collection', PCC_HANDLE) ?>
						</h1>
						<p class="page-description mb-8">
							<?php esc_html_e('Just one more step!
						A content collection is a set of content for your WordPress site.
						Connected to Google Workspace,
						it helps you organize and manage your site content in Google Docs.', PCC_HANDLE) ?>
						</p>
						<p class="text-with-border mb-[1.875rem]"><?php echo esc_url(site_url()) ?></p>
						<p class="text-lg font-bold mb-[1.25rem]" >
							<?php esc_html_e('Publish your document as:', PCC_HANDLE) ?>
						</p>
						<div class="inputs-container">
							<?php foreach (['post', 'page'] as $postType) : ?>
								<div class="input-wrapper">
									<input class="radio-input" name="post_type" type="radio"
										   value="<?php echo esc_attr($postType)?>"
										   id="radio-<?php echo esc_attr($postType)?>"
										<?php checked(get_option(PCC_INTEGRATION_POST_TYPE_OPTION_KEY), $postType); ?> >
									<label class="text-base" for="radio-<?php echo esc_attr($postType)?>">
										<?php esc_html_e(ucfirst($postType), PCC_HANDLE) ?>
									</label>
								</div>
							<?php endforeach; ?>
						</div>
						<a class="primary-button" id="pcc-create-site" href="#">
							<?php esc_html_e('Create collection', PCC_HANDLE) ?>
						</a>
						<div class="mb-10">
							<a class="secondary-button self-start justify-self-end" id="pcc-disconnect" href="#">
								<?php esc_html_e('Reset your Google Workspace authentication', PCC_HANDLE) ?>
							</a>
						</div>
					</div>
					<div class="col-span-5 justify-self-end">
						<img src="<?php echo esc_url(PCC_PLUGIN_DIR_URL . 'assets/images/collection-image.png') ?>"
							 alt="collection-image"
						>
					</div>
				</div>
				<?php
				require 'footer.php';
				?>
			</div>
		</div>
	</div>
</div>



