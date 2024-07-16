<div class="pcc-content">
	<?php
	require 'header.php';
	?>
	<div class="page-content">
		<div class="welcome-page">
			<?php require PCC_PLUGIN_DIR . 'admin/templates/partials/error-message.php'; ?>
			<div class="page-grid">
				<div class="col-span-7">
					<h1 class="page-header mt-6">
						<?php esc_html_e('Connect Google Workspace to your WordPress site', PCC_HANDLE) ?>
					</h1>
					<p class="page-description">
						<?php
						esc_html_e(
							'Effortlessly publish content from Google Docs to your WordPress site using the Pantheon
                            Content Publisher.',
							PCC_HANDLE
						) ?>
					</p>
					<div class="mt-8 mb-1.5">
						<span class="font-bold text-sm"><?php esc_html_e('Access token', PCC_HANDLE) ?></span>
						<img class="scale-110 -ms-1 pb-2 inline" src="<?php echo esc_url(PCC_PLUGIN_DIR_URL . 'assets/images/red-dot.svg') ?>"
							 alt="Red Dot Icon">
						<img class="scale-110 ms-2 inline" src="<?php echo esc_url(PCC_PLUGIN_DIR_URL . 'assets/images/circle-info.svg') ?>"
							 alt="Circle Info">
					</div>
					<input type="password" id="access-token" name="access_token" class="input-with-border mb-2" placeholder="***************" required/>
					<button id="pcc-app-authenticate" class="primary-button">
						<?php esc_html_e('Connect', PCC_HANDLE) ?>
					</button>
					<p class="text-base mt-8 mb-10">
						<?php
						echo wp_kses_post(
							__(
								'Don’t have a token yet? Go to the
                                        <a class="underline text-light-blue" href="#">
                                            Pantheon Content Publisher dashboard
                                        </a>to generate one.',
								PCC_HANDLE
							)
						);
						?>
					</p>
				</div>
				<div class="col-span-5 justify-self-end">
					<img src="<?php echo esc_url(PCC_PLUGIN_DIR_URL . 'assets/images/multi-icons.png') ?>"
						 alt="Pantheon Logo">
				</div>
			</div>
			<?php
			require 'footer.php';
			?>
		</div>
	</div>
</div>



