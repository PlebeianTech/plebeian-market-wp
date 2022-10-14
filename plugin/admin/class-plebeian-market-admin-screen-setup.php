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

class Plebeian_Market_Admin_Screen_Setup {

	static function plebeian_admin_setup_page_html() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_enqueue_script( 'plebeian-market-admin-screen-options', plugin_dir_url( __FILE__ ) . 'js/plebeian-market-admin-screen-setup.js', array( 'jquery' ), PLEBEIAN_MARKET_VERSION, false );
		?>
		<div class="wrap">
			<!-- <h1><?php echo esc_html( get_admin_page_title() ); ?></h1> -->
			<form class="row g-3 col-md-6 needs-validation" id="setupForm" novalidate>

				<script>
					// Plebeian Market API info
					let requestURL = '<?= Plebeian_Market_Communications::getAPIUrl() . PM_API_USER_OPTIONS_URL ?>';

					let getRequestMethod = '<?= PM_API_GET_USER_OPTIONS_METHOD ?>';
					let setRequestMethod = '<?= PM_API_SET_USER_OPTIONS_METHOD ?>';

					// WordPress API info
					let wp_api_ajax_params = {
						ajax_url: '<?= admin_url('admin-ajax.php') ?>',
						nonce: '<?= wp_create_nonce('save_options_nonce') ?>'
					};

					let settingUpFirstTime = <?= (Plebeian_Market_Communications::getXAccessToken() === false || Plebeian_Market_Communications::getXAccessToken() === '') ? 'true' : 'false' ?>;
				</script>

				<h2>Plebeian Market WordPress plugin setup <span class="badge text-bg-success savedBadge" id="savedContribution" style="display: none;">Saved</span></h2>

				<div class="mb-3">
					<label for="xpubKey" class="form-label">XPUB key</label>
					<input type="text" id="xpubKey" class="form-control" aria-describedby="xpubKeyHelpBlock" required>
					<div class="invalid-feedback">
						Please enter a valid XPUB to be able to sell and auction products.
					</div>
					<div id="xpubKeyHelpBlock" class="form-text">
						Your XPUB key is used to generate a new address so each customer pays to a different
						address of you. Most Bitcoin wallets lets you export your XPUB key. See more here.
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
					<input
						type="range"
						class="form-range"
						min="0"
						max="5"
						step="0.5"
						id="contribution_percent"
						name="contribution_percent"
						onInput="contributionUpdated()"
					>
					<span id="contribution_percent_text">Contributing 2.5%</span>
					<p id="contributionEmoji"></p>
					<div id="pmURLHelpBlock" class="form-text">
						<p>Generosity enables us to continue creating free and open source solutions!<br>
						100% goes to powering the Bitcoin movement!</p>
					</div>
				</div>

				<div class="mb-3">
					<label for="pmURL" class="form-label">Plebeian Market URL:</label><span class="badge text-bg-success" style="margin-left: 10px;">Optional</span>
					<input type="text" id="pmURL" class="form-control" aria-describedby="pmURLHelpBlock" placeholder="<?= Plebeian_Market_Communications::getAPIUrl() ?>">
					<div id="pmURLHelpBlock" class="form-text">
						By default, this points to the centralized version of Plebeian Market, but if you want
						to be self-sovereign, you can run an instance in your own node (see here first). When
						you have your node ready, you can add here the URL provided in the admin panel of your
						Plebeian Market instance. This can be a clearnet or Tor (onion) address.
					</div>
				</div>

				<div class="col-12">
					<button class="btn btn-primary" type="submit" id="testConnectionAndParams">Test Parameters</button>
					<button class="btn btn-success" type="submit" id="saveUserOptions" disabled>Save changes</button>
					<p>You must test if the connection works with the values provided before being able to save the new values.</p>
				</div>
			</form>
			
		</div>


		<!-- Modal -->
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