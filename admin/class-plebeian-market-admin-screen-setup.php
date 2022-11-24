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
            PLEBEIAN_MARKET_PLUGIN_BASEPATH . 'admin/js/plebeian-market-admin-screen-setup.js',
			['jquery'],
			PLEBEIAN_MARKET_VERSION,
			false
		);

		wp_enqueue_script(
			'plebeian-market-auth-js',
            PLEBEIAN_MARKET_PLUGIN_BASEPATH . 'common/js/plebeian-market-auth.js',
			['jquery', 'bootstrap-js', 'plebeian-market-js'],
			PLEBEIAN_MARKET_VERSION,
			false
		);
?>
		<div class="wrap">

			<div id="alertsDiv"></div>

			<form class="row g-3 col-md-6 needs-validation" id="setupForm" novalidate>

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
					See advanced options <small>(for self-soverign set-ups)</small>
				</a>
				<div class="collapse" id="customizationAdvanced">
					<div class="mb-3">
						<label for="pmURL" class="form-label">Plebeian Market API URL:</label><span class="badge text-bg-success" style="margin-left: 10px;">Optional</span>
						<input type="text" id="pmURL" class="form-control" aria-describedby="pmURLHelpBlock" placeholder="<?php echo Plebeian_Market_Communications::getAPIUrl() ?>">
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

					<div class="col-12">
						<button class="btn btn-primary" type="submit" id="testConnectionAndParams">Test Parameters</button>
						<p>You must test if the changes to the connection works fine with the values provided before being able to save the new values.</p>
					</div>

					<?php
					$adminKey = Plebeian_Market_Communications::getXAccessToken();
					if ($adminKey !== false && $adminKey !== '') { ?>
						<div id="logoutButtonDiv">
							<a id="logoutButton">Logout from Plebeian Market API</a>
						</div>
					<?php
					}
					?>
				</div>

				<div class="col-12">
					<button class="btn btn-success" type="submit" id="saveUserOptions">Save changes</button>
				</div>
			</form>

		</div>
<?php
		Plebeian_Market_Admin_Common::plebeian_common_admin_code();
	}
}
