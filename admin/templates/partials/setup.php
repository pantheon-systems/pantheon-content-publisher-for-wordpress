<div class="pcc-content">
	<?php
	require 'pages-header.php';
	?>
	<div class="page-content">
		<div class="welcome-page">
			<div class="page-grid">
				<div class="col-span-7">
					<h1 class="page-header"><?php echo esc_html('Set-up Pantheon Content Publisher for Google Docs') ?></h1>
					<p class="page-description"><?php echo esc_html('A publishing tool that connects Google Docs to your WordPress website in real time.') ?></p>
					<button id="pcc-app-authenticate" class="primary-button"><?php echo esc_html('Sign in with Google') ?></button>
				</div>
				<div class="col-span-5 justify-self-end">
					<img src="<?php echo PCC_PLUGIN_DIR_URL . 'assets/images/welcome-icon.png' ?>" alt="Pantheon Logo">
				</div>
			</div>
			<?php
			require 'pages-footer.php';
			?>
		</div>
	</div>
</div>



