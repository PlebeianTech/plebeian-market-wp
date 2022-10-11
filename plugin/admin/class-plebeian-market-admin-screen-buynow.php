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

class Plebeian_Market_Admin_Screen_Buynow
{

	static function plebeian_admin_buynow_page_html()
	{
		if (!current_user_can('manage_options')) {
			return;
		}

		wp_enqueue_script('plebeian-market-admin-screen-options', plugin_dir_url(__FILE__) . 'js/plebeian-market-admin-screen-buynow.js', ['jquery', 'plebeian-market-admin'], PLEBEIAN_MARKET_VERSION, false);
?>

		<script>
			let pluginBasePath = '<?= plugin_dir_url(__FILE__) ?>';

			let requests = {
				pm_api: {
					XAccessToken: '<?= Plebeian_Market_Communications::getXAccessToken() ?>',
					new: {
						url: '<?= Plebeian_Market_Communications::getAPIUrl() . PM_API_NEW_BUYNOW_URL ?>',
						method: '<?= PM_API_NEW_BUYNOW_METHOD ?>'
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
					twitter: {
						url: '<?= Plebeian_Market_Communications::getAPIUrl() . PM_API_NEW_BUYNOW_TWITTER_URL ?>',
						method: 'PUT'
					},
				},
				wordpress_pm_api: {
					ajax_url: '<?= admin_url('admin-ajax.php') ?>',
					nonce: '<?= wp_create_nonce('save_options_nonce') ?>'
				}
			}
		</script>

		<div class="wrap">
			<h3>List of current BuyNow items</h3>
		</div>

		<table id="table_buynow" class="display">
			<thead>
				<tr>
					<th>Key</th>
					<th>Title</th>
					<th>Stock</th>
					<th>Price (USD)</th>
					<th>Images</th>
					<th>Created at</th>
					<th>Start date</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>

		<!-- Modal (New/Edit BuyNow) -->
		<div class="modal fade" id="add-buynow-modal" tabindex="-1" aria-labelledby="titleModalItemInfo" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="titleModalItemInfo">New BuyNow product</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form class="row g-3 needs-validation" id="buyNowForm" novalidate>
							<input type="hidden" class="form-control" id="key" name="key">

							<div class="mb-3">
								<label for="title" class="col-form-label">Title:</label>
								<input type="text" class="form-control" id="title" name="title" required>
								<div class="invalid-feedback">The <b>title</b> field is mandatory.</div>
							</div>
							<div class="mb-3">
								<label for="description" class="col-form-label">Description:</label>
								<textarea class="form-control" id="description" name="description" rows=5 required></textarea>
								<div id="descriptionHelp" class="form-text">Markdown accepted</div>
								<div class="invalid-feedback">You must provide a <b>description</b> for the product.</div>

							</div>
							<div class="col-md-6">
								<label for="price_usd" class="col-form-label">Price ($):</label>
								<input type="text" class="form-control fiat-to-btc-source" id="price_usd" name="price_usd" required>
								<div class="invalid-feedback">You must provide the <b>price</b> of the product.</div>
								<small class="sats_container" id="price_usd_sats"></small>
							</div>
							<div class="col-md-6">
								<label for="available_quantity" class="col-form-label">Available quantity:</label>
								<input type="text" class="form-control" id="available_quantity" name="available_quantity" required>
								<div class="invalid-feedback">You must provice <b>how many units</b> of the product you're selling.</div>
							</div>

							<!-- Shipping -->
							<div class="mb-3">
								<label for="shipping_from" class="col-form-label">Shipping from:</label>
								<input type="text" class="form-control" id="shipping_from" name="shipping_from" placeholder="country or city">
							</div>
							<div class="col-md-6">
								<label for="shipping_domestic_usd" class="col-form-label">Domestic shipping ($):</label>
								<input type="text" class="form-control fiat-to-btc-source" id="shipping_domestic_usd" name="shipping_domestic_usd" required>
								<div class="invalid-feedback">This field is mandatory. If shipping costs not needed, just put 0.</div>
								<small class="sats_container" id="shipping_domestic_usd_sats"></small>
							</div>
							<div class="col-md-6">
								<label for="shipping_worldwide_usd" class="col-form-label">Worldwide shipping ($):</label>
								<input type="text" class="form-control fiat-to-btc-source" id="shipping_worldwide_usd" name="shipping_worldwide_usd" required>
								<div class="invalid-feedback">This field is mandatory. If shipping costs not needed, just put 0.</div>
								<small class="sats_container" id="shipping_worldwide_usd_sats"></small>
							</div>

							<!-- Images -->
							<div class="col-md-6">
								<label for="shipping_worldwide_usd" class="col-form-label">Product images:</label>
								<input type="button" class="button open-media-button" id="open-media-modal" value="Open Media Library" />
							</div>

							<div class='image-preview-wrapper'>
								<img id='image-preview' src='<?= plugin_dir_url(__FILE__) ?>img/plebeian_market_logo.png' width='100' height='100' style='max-height: 100px; width: 100px;'>
							</div>

							<!-- 
								https://jeroensormani.com/how-to-include-the-wordpress-media-selector-in-your-plugin/
								https://codex.wordpress.org/Javascript_Reference/wp.media
							-->
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary btn-save" id="saveBuyNowItem">Save</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal (delete) -->
		<div class="modal fade" id="delete-buynow-modal" tabindex="-1" aria-labelledby="titleModalDelete" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="titleModalDelete">Delete Buy Now product</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body" id="delete-buynow-modal-body"></div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						<button type="button" class="btn btn-primary btn-delete" id="deleteBuyNowItem">Delete</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Notifications -->
		<div class="toast-container position-fixed toastBottom end-0 p-3">
			<div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="6000">
				<div class="toast-header">
					<img src="<?= plugin_dir_url(__FILE__) ?>img/plebeian_market_logo.png" class="rounded me-2 toastImg">
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