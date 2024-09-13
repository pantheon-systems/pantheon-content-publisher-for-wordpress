<div class="pcc-content">
	<?php
	require 'header.php';
	?>
	<div class="page-content">
		<div class="connected-collection-page">
			<?php require PCC_PLUGIN_DIR . 'admin/templates/partials/error-message.php'; ?>
			<div class="py-2.5">
				<h3 class="text-grey font-bold text-sm mb-[0.5rem]">
					<?php esc_html_e('Connected content collection', 'pantheon-content-publisher-for-wordpress') ?>
				</h3>
				<div class="page-grid mb-9">
					<h1 class="page-header col-span-8 justify-self-start break-all">
						<?php echo esc_url(site_url()) ?>
					</h1>
					<a class="secondary-button self-start col-span-4 justify-self-end"
					   href="<?php echo esc_url(add_query_arg([
						   'page' => 'pantheon-content-publisher-for-wordpress',
						   'view' => 'disconnect-confirmation'
					   ], admin_url('admin.php'))) ?>">
						<span>
							<?php esc_html_e('Disconnect collection', 'pantheon-content-publisher-for-wordpress') ?>
						</span>
					</a>
				</div>
				<div>
					<div class="divider-border"></div>
					<p class="text-lg font-bold mt-10 mb-[1.25rem]" >
						<?php esc_html_e('Publish your document as:', 'pantheon-content-publisher-for-wordpress') ?>
					</p>
					<div class="inputs-container">
						<?php foreach (['post', 'page'] as $postType) : ?>
							<div class="input-wrapper">
								<input class="radio-input" name="post_type" type="radio"
									   value="<?php echo esc_attr($postType)?>"
									   id="radio-<?php echo esc_attr($postType)?>"
									<?php checked(get_option(PCC_INTEGRATION_POST_TYPE_OPTION_KEY), $postType); ?> >
								<label class="text-base" for="radio-<?php echo esc_attr($postType)?>">
									<?php esc_html_e(ucfirst($postType), 'pantheon-content-publisher-for-wordpress') ?>
								</label>
							</div>
						<?php endforeach; ?>
					</div>
					<a class="primary-button" id="pcc-update-collection" href="#">
						<?php esc_html_e('Save configuration', 'pantheon-content-publisher-for-wordpress') ?>
					</a>
				</div>
			</div>
			<?php
			require 'footer.php';
			?>
		</div>
	</div>
</div>
