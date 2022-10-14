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

class Plebeian_Market_Admin_Screen_Auctions {

	static function plebeian_admin_auctions_page_html() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_enqueue_script( 'plebeian-market-admin-screen-options', plugin_dir_url( __FILE__ ) . 'js/plebeian-market-admin-screen-auctions.js', array( 'jquery' ), PLEBEIAN_MARKET_VERSION, false );

		$auctions_body_array = Plebeian_Market_Communications::getFeatured('auctions');
		?>
		<div class="wrap">
			<h3>List of current Auctions</h3>
		</div>

		<table id="table_auctions_listing" class="display">
			<thead>
				<tr>
					<th>Key</th>
					<th>Title</th>
					<th>Bids</th>
					<th>Images</th>
					<th>Created at</th>
					<th>Start date</th>
					<th>End date</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php
					if (count($auctions_body_array) > 0) {
						foreach($auctions_body_array as $auction) {
							// print_r($auction);
							$auction_key = $auction->key;
							$auction_title = $auction->title;
							$auction_bids = $auction->bids;
							$auction_media = $auction->media;
							$auction_first_image = $auction_media[0]->url;
							$auction_created_at = $auction->created_at;
							$auction_start_date = $auction->start_date;
							$auction_end_date = $auction->end_date;
							?>
							<tr>
								<td><?= $auction_key ?></td>
								<td><?= $auction_title ?></td>
								<td><?= count($auction_bids) ?></td>
								<td></td>
								<td><?= $auction_created_at ?></td>
								<td><?= $auction_start_date ?></td>
								<td><?= $auction_end_date ?></td>
								<td>(D) (M)</td>
							</tr>
							<?
						}
					}
					?>

			</tbody>
		</table>
	<?php
	}
}