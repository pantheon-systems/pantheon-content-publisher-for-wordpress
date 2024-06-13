<div class="pcc-content">
	<?php
	require 'header.php';
	?>
	<div class="page-content">
		<div class="create-collection-page">
			<div class="page-grid">
				<div class="col-span-7 justify-self-start">
					<h1 class="page-header mt-[3rem]"><?php esc_html_e('Create your first content collection', PCC_HANDLE) ?></h1>
					<p class="page-description mb-12"><?php esc_html_e('Just one more step! A content collection is a set of content for your WordPress site. Connected to Google Workspace, it helps you organize and manage your site content in Google Docs.', PCC_HANDLE) ?></p>
					<p class="text-with-border mb-[3rem]"><?php echo esc_url(site_url()) ?></p>
					<p class="text-lg font-bold mb-8" ><?php esc_html_e('Publish your document as:', PCC_HANDLE) ?></p>
					<div class="inputs-container">
						<div class="input-wrapper">
							<input class="radio-input" type="radio" id="post">
							<label class="text-base" for="post"><?php esc_html_e('Post', PCC_HANDLE) ?></label>
						</div>
						<div class="input-wrapper">
							<input class="radio-input" type="radio" id="page">
							<label class="text-base" for="page"><?php esc_html_e('Page', PCC_HANDLE) ?></label>
						</div>
					</div>
					<a class="primary-button" id="pcc-create-site" href="#"><?php esc_html_e('Create Collection', PCC_HANDLE) ?></a>
				</div>
				<div class="col-span-5 justify-self-end">
					<img src="<?php echo esc_url(PCC_PLUGIN_DIR_URL . 'assets/images/collection-image.png') ?>" alt="collection-image">
				</div>
			</div>
			<?php
			require 'footer.php';
			?>
		</div>
	</div>
</div>



