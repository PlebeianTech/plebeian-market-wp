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

class Plebeian_Market_Admin_Screen_Setup
{

	static function plebeian_admin_setup_page_html()
	{
		if (!current_user_can('manage_options')) {
			return;
		}

		wp_enqueue_script(
			'plebeian-market-admin-screen-options',
			plugin_dir_url(__FILE__) . 'js/plebeian-market-admin-screen-setup.js',
			['jquery'],
			PLEBEIAN_MARKET_VERSION,
			false
		);

		wp_enqueue_script(
			'plebeian-market-js',
			plugin_dir_url(__DIR__) . 'common/js/plebeian-market.js',
			['jquery', 'bootstrap-js'],
			PLEBEIAN_MARKET_VERSION,
			false
		);

		wp_enqueue_script(
			'plebeian-market-auth-js',
			plugin_dir_url(__DIR__) . 'common/js/plebeian-market-auth.js',
			['jquery', 'bootstrap-js'],
			PLEBEIAN_MARKET_VERSION,
			false
		);

		$adminKey = get_option('plebeian_market_auth_key');
?>
		<div class="wrap">
			<form class="row g-3 col-md-6 needs-validation" id="setupForm" novalidate>
				<script>
					// Plebeian Market API info
					let requestHostname = '<?= Plebeian_Market_Communications::getAPIUrl() ?>';
					let requestURL = '<?= PM_API_USER_OPTIONS_URL ?>';

					let getRequestMethod = '<?= PM_API_GET_USER_OPTIONS_METHOD ?>';
					let setRequestMethod = '<?= PM_API_SET_USER_OPTIONS_METHOD ?>';

					let requests = {
						pm_api: {
							default_timeout: 10000,
							get_login_info: {
								url: '<?= Plebeian_Market_Communications::getAPIUrl() . PM_API_GET_LOGIN_INFO_URL ?>',
								method: '<?= PM_API_GET_LOGIN_INFO_METHOD ?>'
							},
							check_login: {
								url: '<?= Plebeian_Market_Communications::getAPIUrl() . PM_API_CHECK_LOGIN_URL ?>',
								method: '<?= PM_API_CHECK_LOGIN_METHOD ?>'
							}
						},
						wordpress_pm_api: {
							ajax_url: '<?= admin_url('admin-ajax.php') ?>',
							nonce: '<?= wp_create_nonce('save_options_nonce') ?>'
						}
					}

					let adminKey = '<?= $adminKey ?>';

					let adminURLWithLogin = '<?= admin_url('admin.php?page=plebeian_market') ?>';
					let setupURLWithoutLogin = '<?= admin_url('admin.php?page=plebeian_market') ?>';
				</script>

				<h2>Plebeian Market WordPress plugin setup <span class="badge text-bg-success savedBadge" id="savedContribution" style="display: none;">Saved</span></h2>

				<div class="mb-3">
					<label for="xpubKey" class="form-label">Extended public key (xpub / ypub / zpub)</label>
					<input type="text" id="xpubKey" class="form-control" aria-describedby="xpubKeyHelpBlock" required>
					<div class="invalid-feedback">
						Please enter a valid XPUB/YPUB/ZPUB to be able to sell and auction products.
					</div>
					<div id="xpubKeyHelpBlock" class="form-text">
						Your XPUB/YPUB/ZPUB key is used to generate a new address so each customer pays to a different
						address for you. Most Bitcoin wallets allow you to export your extended key. See more in
						<a class="link" target="_blank" href="https://plebeian.market/faq">our FAQ</a>.
					</div>
				</div>

				<div class="mb-3">
					<label for="sellerEmail" class="form-label">Seller email</label>
					<input type="text" id="sellerEmail" class="form-control" aria-describedby="sellerEmailHelpBlock" required>
					<div class="invalid-feedback">
						The email address cannot be left empty because buyers need a way to contact you.
					</div>
					<div id="sellerEmailHelpBlock" class="form-text">
						This will be shown to buyers once they've paid the item so both of you arrange the shipping.
					</div>
				</div>

				<div class="mb-3">
					<label for="contribution_percent" class="form-label">Value4Value contribution:</label>
					<input type="range" class="form-range" min="0" max="5" step="0.5" id="contribution_percent" name="contribution_percent" onInput="contributionUpdated()">
					<span id="contribution_percent_text">Contributing 2.5%</span>
					<p id="contributionEmoji"></p>
					<div id="pmURLHelpBlock" class="form-text">
						<p>Generosity enables us to continue creating free and open source solutions!<br>
							100% goes to powering the Bitcoin movement!</p>
					</div>
				</div>

				<a data-bs-toggle="collapse" href="#customizationAdvanced" role="button" aria-expanded="false" aria-controls="customizationAdvanced">
					See advanced options for self-soverign set-ups
				</a>
				<div class="collapse" id="customizationAdvanced">
					<div class="mb-3">
						<label for="pmURL" class="form-label">Plebeian Market API URL:</label><span class="badge text-bg-success" style="margin-left: 10px;">Optional</span>
						<input type="text" id="pmURL" class="form-control" aria-describedby="pmURLHelpBlock" placeholder="<?= Plebeian_Market_Communications::getAPIUrl() ?>">
						<div id="pmURLHelpBlock" class="form-text">
							By default, this points to the centralized version of Plebeian Market, but if you want
							to be self-sovereign, soon you'll be able to run your own Plebeian Market in your Bitcoin
							node. When the time comes, you will be able to indicate here the URL of your marketplace.
						</div>
					</div>


					<div class="mb-3">
						<label for="pmAuthKey" class="form-label">Plebeian Market auth key</label>
						<input type="text" id="pmAuthKey" class="form-control" aria-describedby="pmAuthKeyHelpBlock" required>
						<div class="invalid-feedback">
							The auth key shouldn't be empty because it's used to communicate with your Plebeian Market node.
						</div>
						<div id="pmAuthKeyHelpBlock" class="form-text">
							The auth key to manage your Plebeian Market account. This cannot be empty.
						</div>
					</div>
				</div>

				<div class="col-12">
					<button class="btn btn-primary" type="submit" id="testConnectionAndParams">Test Parameters</button>
					<button class="btn btn-success" type="submit" id="saveUserOptions" disabled>Save changes</button>
					<p>You must test if the connection works with the values provided before being able to save the new values.</p>
				</div>
			</form>

			<?php
			if ($adminKey !== false && $adminKey !== '') { ?>
				<a id="logoutButton">Logout</a>
			<?php
			}
			?>


		</div>
<?php
		Plebeian_Market_Admin_Common::plebeian_common_admin_code();
	}
}
