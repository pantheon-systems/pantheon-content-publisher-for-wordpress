<div class="pcc-content">
	<?php
	require 'pages-header.php';
	?>
	<div class="page-content">
		<div class="disconnect-confirm-page">
			<div class="page-grid">
				<div class="col-span-7">
					<h1 class="page-header">Disconnect collection?</h1>
					<p class="page-description">Are you sure you want to disconnect from this collection? (This is what will happen if you do this)</p>
					<div class="flex gap-6 mt-[3rem]">
						<a class="secondary-button" href="<?php echo esc_url(add_query_arg(['page' => PCC_HANDLE, 'view' => 'disconnect-collection'], admin_url())) ?>">No, remain connected</a>
						<a class="primary-button" id="pcc-disconnect" href="#">Disconnect collection</a>
					</div>
				</div>
				<div class="col-span-5 justify-self-end">
					<img src="<?php echo PCC_PLUGIN_DIR_URL . 'assets/images/Inspection.png' ?>" alt="Inspection images">
				</div>
			</div>
		</div>
	</div>
</div>
