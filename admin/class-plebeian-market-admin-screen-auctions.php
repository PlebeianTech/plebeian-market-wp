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

class Plebeian_Market_Admin_Screen_Auctions
{

	static function plebeian_admin_auctions_page_html()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        wp_enqueue_script('plebeian-market-admin-screen-items', PLEBEIAN_MARKET_PLUGIN_BASEPATH . 'admin/js/plebeian-market-admin-screen-items.js', ['jquery', 'plebeian-market-admin'], PLEBEIAN_MARKET_VERSION, false);
        wp_enqueue_script('plebeian-market-admin-screen-auctions', PLEBEIAN_MARKET_PLUGIN_BASEPATH . 'admin/js/plebeian-market-admin-screen-auctions.js', ['jquery', 'plebeian-market-admin', 'plebeian-market-admin-screen-items'], PLEBEIAN_MARKET_VERSION, false);
        ?>

        <script>
            let pluginBasePath = '<?php echo PLEBEIAN_MARKET_PLUGIN_BASEPATH ?>';
        </script>

        <div class="wrap">
            <div id="alertsDiv"></div>

            <h3>List of current Auctions</h3>
        </div>

        <p class="updatedAt" id="tableUpdatedAt"></p>

        <table id="table_items" class="display">
            <thead>
            <tr>
                <th>Key</th>
                <th>Title</th>
                <th>Images</th>
                <th>Duration</th>
                <th>Created at</th>
                <th>Start date</th>
                <th>End date</th>
                <th>Status</th>
                <th>Starting bid</th>
                <th># bids</th>
                <th>Current bid</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <!-- Modal (New/Edit Auction) -->
        <div class="modal fade" id="add-item-modal" tabindex="-1" aria-labelledby="titleModalItemInfo" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="titleModalItemInfo">New Auction product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form class="row g-3 needs-validation" id="itemForm" data-pmtype="auction" novalidate>
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
                                <label for="starting_bid" class="col-form-label">Starting bid (sats - optional):</label>
                                <input type="text" class="form-control fiat-to-btc-source" id="starting_bid" name="starting_bid">
                                <small class="sats_container" id="price_usd_sats"></small>
                            </div>
                            <div class="col-md-6">
                                <label for="reserve_bid" class="col-form-label">Reserve bid (sats - optional):</label>
                                <input type="text" class="form-control" id="reserve_bid" name="reserve_bid">
                                <small class="sats_container" id="price_usd_sats"></small>
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

                            <!-- Auction Duration -->
                            <div class="row mt-3 mb-3">
                                <label for="duration" class="col-form-label">Auction duration:</label>
                                <div class="col-2">
                                    <input type="text" class="form-control" id="duration" name="duration" required>
                                    <div class="invalid-feedback">This field is mandatory. You must set the duration of the auction in hours or days.</div>
                                </div>
                                <div class="col-2">
                                    <label class="visually-hidden" for="duration_unit">Name</label>
                                    <select class="form-control" id="duration_unit" name="duration_unit">
                                        <option value="h">hours</option>
                                        <option value="d">days</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Images -->
                            <div class="col-md-6">
                                <label for="open-media-modal" class="col-form-label">Product images:</label>
                                <input type="button" class="button open-media-button" id="open-media-modal" value="Open Media Library" />
                            </div>

                            <ul id="product-images-container"></ul>
                            <!--
                                https://jeroensormani.com/how-to-include-the-wordpress-media-selector-in-your-plugin/
                                https://codex.wordpress.org/Javascript_Reference/wp.media
                            -->
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary btn-save" id="saveItem">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <?php Plebeian_Market_Admin_Common::plebeian_market_common_admin_code(true);
    }
}
