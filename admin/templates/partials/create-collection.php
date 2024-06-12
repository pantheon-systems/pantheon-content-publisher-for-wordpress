<div class="pcc-content">
	<?php
	require 'header.php';
	?>
	<div class="page-content">
		<div class="create-collection-page">
			<div class="page-grid">
				<div class="col-span-7 justify-self-start">
					<h1 class="page-header mt-[3rem]"><?php esc_html_e('Create your first collection', PCC_HANDLE) ?></h1>
					<p class="page-description text-grey mb-12"><?php esc_html_e('Once you create a collection on Content Publisher, your Google Workplace users will be able to connect Google Docs to that collection and publish them on your website.', PCC_HANDLE) ?></p>
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
					<img src="<?php echo PCC_PLUGIN_DIR_URL . 'assets/images/collection-image.png' ?>" alt="collection-image">
				</div>
			</div>
			<?php
			require 'footer.php';
			?>
		</div>
	</div>
</div>



