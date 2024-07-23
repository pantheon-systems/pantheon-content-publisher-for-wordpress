<div class="pcc-content notice pcc-notice is-dismissible">
	<div class="page-content">
		<div class="continue-setup">
			<div class="header">
				<img src="<?php
				echo esc_url(PCC_PLUGIN_DIR_URL . 'assets/images/pantheon-logo.svg') ?>"
					 alt="Pantheon Logo">
				<span class="header-title">
					<?php
					esc_html_e('Content Publisher', PCC_HANDLE) ?>
				</span>
			</div>
			<div class="">
				<p class="page-description">
					<?php
					esc_html_e(
						'Plugin installed! To create and publish content,
					complete the setup by connecting Google Workspace to your WordPress site.',
						PCC_HANDLE
					) ?>
				</p>
				<a href="<?php
				echo esc_url(add_query_arg(['page' => PCC_HANDLE], admin_url('admin.php'))) ?>"
				   class="primary-button me-auto"><?php
					esc_html_e('Complete setup', PCC_HANDLE) ?>
				</a>
			</div>
		</div>
	</div>
</div>
