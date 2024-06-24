<div id="pcc-error-message" class="pcc-error-message hidden flex justify-between">
	<div class="flex items-center gap-2.5">
		<img src="<?php echo esc_url(PCC_PLUGIN_DIR_URL . 'assets/images/diamond-exclamation.png') ?>"
			 alt="Diamond exclamation icon">
		<p id="pcc-error-text" class="text-sm text-black"></p>
	</div>
	<div class="flex items-center gap-4">
<!--		<div>-->
<!--			<a class="secondary-button" href="">--><?php
//				esc_html_e(
//					'Action',
//					PCC_HANDLE
//				)
//				?><!--</a>-->
<!--		</div>-->
		<button id="pcc-error-close-button">
			<img src="<?php echo esc_url(PCC_PLUGIN_DIR_URL . 'assets/images/close-icon.png') ?>"
				 alt="Close icon">
		</button>
	</div>
</div>
