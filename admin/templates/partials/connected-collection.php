<div class="pcc-content">
	<?php
	require 'header.php';
	?>
	<div class="page-content">
		<div class="connected-collection-page">
			<div class="py-2.5">
				<h3 class="text-grey font-bold text-sm mb-[0.5rem]"><?php esc_html_e('Connected content collection', PCC_HANDLE) ?></h3>
				<div class="page-grid mb-6">
					<h1 class="page-header col-span-8 justify-self-start break-all"><?php echo esc_url(site_url()) ?></h1>
					<a class="secondary-button self-start col-span-4 justify-self-end" href="<?php echo esc_url(add_query_arg(['page' => PCC_HANDLE, 'view' => 'disconnect-confirmation'], admin_url())) ?>">
						<span><?php esc_html_e('Disconnect collection', PCC_HANDLE) ?></span></a>
				</div>
			</div>
			<?php
			require 'footer.php';
			?>
		</div>
	</div>
</div>



