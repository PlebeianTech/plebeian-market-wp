<?php

/**
 * The admin/options screen of the plugin.
 *
 * @link       https://github.com/PlebeianTech
 * @since      1.0.0
 *
 * @package    Plebeian_Market
 * @subpackage Plebeian_Market/admin
 */

class Plebeian_Market_Admin_Common
{

	static function plebeian_common_admin_code()
	{
?>
		<!-- Notifications -->
		<div class="toast-container d-flex justify-content-center align-items-center w-100">
			<div id="liveToast" class="toast " role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
				<div class="toast-header">
					<img src="<?= plugin_dir_url(__DIR__) ?>public/img/plebeian_market_logo.png" class="rounded me-2 toastImg">
					<strong class="me-auto" id="liveToastTitle">Plebeian Market</strong>
					<small id="liveToastSmallWhen">Just now</small>
					<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
				</div>
				<div class="toast-body" id="liveToastBody"></div>
			</div>
		</div>

		<!-- Alert Modal -->
		<div id="alertModal" class="modal fade" role="dialog">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body">
						<p id="alertModalText"></p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
<?php
	}
}
