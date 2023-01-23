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
	public static function plebeian_market_common_admin_code($showConfirmActionItemModal = false): void
    {
        wp_enqueue_script('plebeian-market-auth-js', PLEBEIAN_MARKET_PLUGIN_BASEPATH . 'common/js/plebeian-market-auth.js', ['jquery', 'bootstrap-js', 'plebeian-market-js'], PLEBEIAN_MARKET_VERSION,false);
?>
		<script>
			// Plebeian Market API info
			let plebeian_market_auth_key = '<?php echo Plebeian_Market_Communications::getXAccessToken() ?>';

			let pluginURL = '<?php echo admin_url('admin.php?page=plebeian_market') ?>';
			let pluginSetupURL = '<?php echo admin_url('admin.php?page=plebeian_market_setup') ?>';

			let requests = {
				pm_api: {
					default_timeout: 10000,
					XAccessToken: '<?php echo Plebeian_Market_Communications::getXAccessToken() ?>',
					get_login_info: {
						url: '<?php echo Plebeian_Market_Communications::getAPIUrl() . PLEBEIAN_MARKET_API_GET_LOGIN_INFO_URL ?>',
						method: '<?php echo PLEBEIAN_MARKET_API_GET_LOGIN_INFO_METHOD ?>'
					},
					check_login: {
						url: '<?php echo Plebeian_Market_Communications::getAPIUrl() . PLEBEIAN_MARKET_API_CHECK_LOGIN_URL ?>',
						method: '<?php echo PLEBEIAN_MARKET_API_CHECK_LOGIN_METHOD ?>'
					},
					user_info: {
						url: '<?php echo Plebeian_Market_Communications::getAPIUrl() . PLEBEIAN_MARKET_API_USER_OPTIONS_URL ?>',
						path: '<?php echo PLEBEIAN_MARKET_API_USER_OPTIONS_URL ?>',
						getMethod: '<?php echo PLEBEIAN_MARKET_API_GET_USER_OPTIONS_METHOD ?>',
						setMethod: '<?php echo PLEBEIAN_MARKET_API_SET_USER_OPTIONS_METHOD ?>'
					},
                    buynow: {
                        list: {
                            url: '<?php echo Plebeian_Market_Communications::getAPIUrl() . PLEBEIAN_MARKET_API_LIST_BUYNOW_URL ?>'
                        },
                        new: {
                            url: '<?php echo Plebeian_Market_Communications::getAPIUrl() . PLEBEIAN_MARKET_API_NEW_BUYNOW_URL ?>',
                            method: '<?php echo PLEBEIAN_MARKET_API_NEW_BUYNOW_METHOD ?>'
                        },
                        edit: {
                            url: '<?php echo Plebeian_Market_Communications::getAPIUrl() . PLEBEIAN_MARKET_API_EDIT_BUYNOW_URL ?>',
                            method: '<?php echo PLEBEIAN_MARKET_API_EDIT_BUYNOW_METHOD ?>'
                        },
                        delete: {
                            url: '<?php echo Plebeian_Market_Communications::getAPIUrl() . PLEBEIAN_MARKET_API_DELETE_BUYNOW_URL ?>',
                            method: '<?php echo PLEBEIAN_MARKET_API_DELETE_BUYNOW_METHOD ?>'
                        },
                        publish: {
                            url: '<?php echo Plebeian_Market_Communications::getAPIUrl() . PLEBEIAN_MARKET_API_START_BUYNOW_URL ?>',
                            method: '<?php echo PLEBEIAN_MARKET_API_START_BUYNOW_METHOD ?>'
                        },
                    },
                    auction: {
                        list: {
                            url: '<?php echo Plebeian_Market_Communications::getAPIUrl() . PLEBEIAN_MARKET_API_LIST_AUCTIONS_URL ?>'
                        },
                        new: {
                            url: '<?php echo Plebeian_Market_Communications::getAPIUrl() . PLEBEIAN_MARKET_API_NEW_AUCTIONS_URL ?>',
                            method: '<?php echo PLEBEIAN_MARKET_API_NEW_AUCTIONS_METHOD ?>'
                        },
                        edit: {
                            url: '<?php echo Plebeian_Market_Communications::getAPIUrl() . PLEBEIAN_MARKET_API_EDIT_AUCTIONS_URL ?>',
                            method: '<?php echo PLEBEIAN_MARKET_API_EDIT_AUCTIONS_METHOD ?>'
                        },
                        delete: {
                            url: '<?php echo Plebeian_Market_Communications::getAPIUrl() . PLEBEIAN_MARKET_API_DELETE_AUCTIONS_URL ?>',
                            method: '<?php echo PLEBEIAN_MARKET_API_DELETE_AUCTIONS_METHOD ?>'
                        },
                        publish: {
                            url: '<?php echo Plebeian_Market_Communications::getAPIUrl() . PLEBEIAN_MARKET_API_START_AUCTIONS_URL ?>',
                            method: '<?php echo PLEBEIAN_MARKET_API_START_AUCTIONS_METHOD ?>'
                        },
                    }
				},
				wordpress_pm_api: {
					ajax_url: '<?php echo admin_url('admin-ajax.php') ?>',
					nonce: '<?php echo wp_create_nonce('save_options_nonce') ?>'
				}
			}
		</script>

		<!-- Notifications -->
		<div class="toast-container d-flex justify-content-center align-items-center w-100">
			<div id="liveToast" class="toast " role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
				<div class="toast-header">
					<img src="<?php echo PLEBEIAN_MARKET_PLUGIN_BASEPATH ?>common/img/plebeian_market_logo.png" class="rounded me-2 toastImg">
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
        if ($showConfirmActionItemModal) { ?>
            <!-- Confirm Action Modal (delete/publish/...) -->
            <div class="modal fade" id="confirmActionItemModal" tabindex="-1" aria-labelledby="confirmActionTitleModal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmActionTitleModal">Confirm action</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="confirmActionItemModalBody"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="confirmActionItemButton">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
	}

	public static function getHTMLOptions($start = 10, $end = 40, $includeBlank = true): string
    {
		$options = '';

		if ($includeBlank) {
			$options .= '<option value=""></option>';
		}

		for ($i = $start; $i <= $end; $i++) {
			$options .= '<option value="' . esc_attr($i) . '">' . esc_html($i) . '</option>';
		}

		return $options;
	}
}
