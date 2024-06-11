<div class="pcc-content">
	<?php
	require 'pages-header.php';
	?>
	<div class="page-content">
		<div class="create-collection-page">
			<div class="page-grid">
				<div class="col-span-7 justify-self-start">
					<h1 class="page-header mt-[3rem]">Create your first collection</h1>
					<p class="page-description text-grey mb-12">Once you create a collection on Content Publisher, your Google Workplace users will be able to connect Google Docs to that collection and publish them on your website.</p>
					<input  type="text" placeholder="prepopulated-wordpress-installation.com" class="input-text-field mb-[3rem]"/>
					<p class="text-lg font-bold mb-8" >Publish your document as:</p>
					<div class="inputs-container">
						<div class="input-wrapper">
							<input class="radio-input" type="radio" id="post">
							<label class="text-base" for="post">Post</label>
						</div>
						<div class="input-wrapper">
							<input class="radio-input" type="radio" id="page">
							<label class="text-base" for="page">Page</label>
						</div>
					</div>
					<a class="primary-button" href="<?php echo esc_url(add_query_arg(['page' => PCC_HANDLE, 'view' => 'disconnect-collection'], admin_url())) ?>">Create Collection</a>
				</div>
				<div class="col-span-5 justify-self-end">
					<img src="<?php echo PCC_PLUGIN_DIR_URL . 'assets/images/collection-image.png' ?>" alt="collection-image">
				</div>
			</div>
			<?php
			require 'pages-footer.php';
			?>
		</div>
	</div>
</div>



