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
		<script>
			// Plebeian Market API info
			let plebeian_market_auth_key = '<?= Plebeian_Market_Communications::getXAccessToken() ?>';

			let pluginURL = '<?= admin_url('admin.php?page=plebeian_market') ?>';
			let pluginSetupURL = '<?= admin_url('admin.php?page=plebeian_market_setup') ?>';

			let requests = {
				pm_api: {
					default_timeout: 10000,
					XAccessToken: '<?= Plebeian_Market_Communications::getXAccessToken() ?>',
					new: {
						url: '<?= Plebeian_Market_Communications::getAPIUrl() . PM_API_NEW_BUYNOW_URL ?>',
						method: '<?= PM_API_NEW_BUYNOW_METHOD ?>'
					},
					start: {
						url: '<?= Plebeian_Market_Communications::getAPIUrl() . PM_API_START_BUYNOW_URL ?>',
						method: '<?= PM_API_START_BUYNOW_METHOD ?>'
					},
					edit: {
						url: '<?= Plebeian_Market_Communications::getAPIUrl() . PM_API_EDIT_BUYNOW_URL ?>',
						method: '<?= PM_API_EDIT_BUYNOW_METHOD ?>'
					},
					delete: {
						url: '<?= Plebeian_Market_Communications::getAPIUrl() . PM_API_DELETE_BUYNOW_URL ?>',
						method: '<?= PM_API_DELETE_BUYNOW_METHOD ?>'
					},
					list: {
						url: '<?= Plebeian_Market_Communications::getAPIUrl() . PM_API_LIST_BUYNOW_URL ?>'
					},
					get_login_info: {
						url: '<?= Plebeian_Market_Communications::getAPIUrl() . PM_API_GET_LOGIN_INFO_URL ?>',
						method: '<?= PM_API_GET_LOGIN_INFO_METHOD ?>'
					},
					check_login: {
						url: '<?= Plebeian_Market_Communications::getAPIUrl() . PM_API_CHECK_LOGIN_URL ?>',
						method: '<?= PM_API_CHECK_LOGIN_METHOD ?>'
					},
					user_info: {
						url: '<?= Plebeian_Market_Communications::getAPIUrl() . PM_API_USER_OPTIONS_URL ?>',
						path: '<?= PM_API_USER_OPTIONS_URL ?>',
						getMethod: '<?= PM_API_GET_USER_OPTIONS_METHOD ?>',
						setMethod: '<?= PM_API_SET_USER_OPTIONS_METHOD ?>'
					}
				},
				wordpress_pm_api: {
					ajax_url: '<?= admin_url('admin-ajax.php') ?>',
					nonce: '<?= wp_create_nonce('save_options_nonce') ?>'
				}
			}
		</script>

		<!-- Notifications -->
		<div class="toast-container d-flex justify-content-center align-items-center w-100">
			<div id="liveToast" class="toast " role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
				<div class="toast-header">
					<img src="<?= plugin_dir_url(__DIR__) ?>common/img/plebeian_market_logo.png" class="rounded me-2 toastImg">
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

		<!-- General-Purpose Modal -->
		<div id="gpModal" class="modal fade" role="dialog">
			<div class="modal-dialog modal-lg modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title"></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeGPModal"></button>
					</div>
					<div class="modal-body text-center" id="gp-modal-body"></div>
				</div>
			</div>
		</div>

		<!-- Loading Modal -->
		<div id="loadingModal" class="modal fade" role="dialog" data-bs-backdrop="static">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-body" id="loadingModal-body">
						<div class="d-flex justify-content-center">
							<div class="spinner-border" role="status"></div>
						</div>
						<div class="justify-content-center d-flex loadingModalLoadingText">
							<p>Loading...</p>
						</div>
					</div>
				</div>
			</div>
		</div>
<?php
	}

	static function getHTMLOptions($start = 10, $end = 40, $includeBlank = true)
	{
		$options = '';

		if ($includeBlank) {
			$options .= '<option value=""></option>';
		}

		for ($i = $start; $i <= $end; $i++) {
			$options .= '<option value="' . $i . '">' . $i . '</option>';
		}

		return $options;
	}
}
