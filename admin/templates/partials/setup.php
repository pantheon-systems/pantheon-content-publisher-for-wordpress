<div class="pcc-content">
	<?php
	require 'header.php';
	?>
	<div class="page-content">
		<?php require PCC_PLUGIN_DIR . 'admin/templates/partials/spinner.php'; ?>
		<div id="pcc-content">
			<div class="welcome-page">
				<?php require PCC_PLUGIN_DIR . 'admin/templates/partials/error-message.php'; ?>
				<div class="page-grid mt-6">
					<div class="col-span-8">
						<div class="w-[80%]">
							<h1 class="page-header">
								<?php esc_html_e('Connect Google Workspace to your WordPress site', PCC_HANDLE) ?>
							</h1>
							<p class="page-description">
								<?php
								esc_html_e(
									'Effortlessly publish content from Google Docs to your WordPress site using 
									Pantheon Content Publisher.',
									PCC_HANDLE
								) ?>
							</p>
							<div class="mt-8 mb-0.5">
							<span class="font-semibold text-[0.83rem]">
								<?php esc_html_e('Access token', PCC_HANDLE) ?>
							</span>
								<img class="scale-110 ms-1 pb-2.5 inline"
									 src="<?php echo esc_url(PCC_PLUGIN_DIR_URL . 'assets/images/red-dot.svg') ?>"
									 alt="Red Dot Icon">
								<div class="tooltip inline">
									<img class="scale-110 ms-2 pb-1 inline"
										 src="<?php echo
											esc_url(PCC_PLUGIN_DIR_URL . 'assets/images/circle-info.svg') ?>"
										 alt="Circle Info">
									<span class="tooltip-text">
									<?php
									esc_html_e('Enter the digit access token generated from
                                    the Pantheon Content Publisher authentication dashboard', PCC_HANDLE) ?>
								</span>
								</div>

							</div>
							<input type="password"
								   placeholder="***************"
								   id="access-token"
								   name="access_token" class="input-with-border mb-2" required/>
							<button id="pcc-app-authenticate" class="primary-button">
								<?php esc_html_e('Connect', PCC_HANDLE) ?>
							</button>
						</div>
						<p class="text-base mt-8 mb-10">
							<?php
							echo wp_kses_post(
								__(
									'Don’t have a token yet? Go to the
                                        <a class="pantheon-link  hover:text-secondary"
                                        target="_blank" href="https://pcc.pantheon.io/auth">
                                            Pantheon Content Publisher dashboard
                                        </a>to generate one.',
									PCC_HANDLE
								)
							);
							?>
						</p>
					</div>
					<div class="col-span-4 self-start justify-self-end">
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
</div>



