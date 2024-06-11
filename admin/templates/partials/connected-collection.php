<div class="pcc-content">
	<?php
		require 'pages-header.php';
	?>
	<div class="page-content">
		<div class="connected-collection-page">
			<div class="py-4">
				<h3 class="text-grey font-bold text-sm mb-[0.8rem]">Connected collection</h3>
				<div class="page-grid mb-12">
					<h1 class="page-header col-span-7 justify-self-start break-all">https//my.specialsite.from.pcx.commy.specialsite.from.pcx.comy.specialsite.from.pcx.comy.specialsite.from.pcx.comy.specialsite.from.pcx.comy.specialsite.from.pcx.co</h1>
					<a class="secondary-button self-start col-span-5 justify-self-end flex items-center gap-2" href=""><span><img
									src="<?php echo PCC_PLUGIN_DIR_URL . 'assets/images/get-back.svg' ?>"
									alt="Pantheon Logo"></span> <span>Change connected collection</span></a>
				</div>
				<div class="divider-border"></div>
				<p class="text-lg font-bold mt-12 mb-8">Publish your document as:</p>
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
				<a class="primary-button" href="">Save Configuration</a>
			</div>
			<?php
			require 'pages-footer.php';
			?>
		</div>
	</div>
</div>



