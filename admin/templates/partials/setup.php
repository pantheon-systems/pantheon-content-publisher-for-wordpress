<div class="pcc-content">
	<?php
	require 'header.php';
	?>
	<div class="page-content">
		<div class="welcome-page">
			<div class="page-grid">
				<div class="col-span-7">
					<h1 class="page-header"><?php esc_html_e('Set-up Pantheon Content Publisher for Google Docs', PCC_HANDLE) ?></h1>
					<p class="page-description"><?php esc_html_e('A publishing tool that connects Google Docs to your WordPress website in real time.', PCC_HANDLE) ?></p>
					<button id="pcc-app-authenticate"
							class="primary-button"><?php esc_html_e('Sign in with Google', PCC_HANDLE) ?></button>
				</div>
				<div class="col-span-5 justify-self-end">
					<img src="<?php echo PCC_PLUGIN_DIR_URL . 'assets/images/welcome-icon.png' ?>" alt="Pantheon Logo">
				</div>
			</div>
			<?php
			require 'footer.php';
			?>
		</div>
	</div>
</div>



