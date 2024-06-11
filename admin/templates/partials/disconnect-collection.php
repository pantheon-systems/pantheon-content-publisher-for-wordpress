<div class="pcc-content">
	<?php
	require 'pages-header.php';
	?>
	<div class="page-content">
		<div class="connected-collection-page">
			<div class="py-4">
				<h3 class="text-grey font-bold text-sm mb-[0.8rem]">Connected collection</h3>
				<div class="page-grid mb-12">
					<h1 class="page-header col-span-8 justify-self-start break-all">https//my.specialsite.from.pcx.commy.specialsite.from.pcx.comy.specialsite.from.pcx.comy.specialsite.from.pcx.comy.specialsite.from.pcx.comy.specialsite.from.pcx.co</h1>
					<a class="secondary-button self-start col-span-4 justify-self-end" href="<?php echo esc_url(add_query_arg(['page' => PCC_HANDLE, 'view' => 'confirm-disconnect'], admin_url())) ?>">
						<span>Disconnect collection</span></a>
				</div>
			</div>
			<?php
			require 'pages-footer.php';
			?>
		</div>
	</div>
</div>



