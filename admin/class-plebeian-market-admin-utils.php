<?php

/**
 * The utility / misc classes of the plugin.
 *
 * @link       https://github.com/PlebeianTech
 * @since      1.0.0
 *
 * @package    Plebeian_Market
 * @subpackage Plebeian_Market/admin
 */

class Plebeian_Market_Admin_Utils
{
	public static function plebeian_market_load_options($prefix = null, $remove_prefix = false): array
    {
		$optionsLoaded = [];

		foreach (PLEBEIAN_MARKET_OPTIONS as $option) {
			$optionValue = get_option($option);

			if (strpos($option, $prefix) === 0) {
				if ($optionValue !== false) {
					if ($remove_prefix) {
						$option_without_prefix = substr($option, strlen($prefix));
						$optionsLoaded[$option_without_prefix] = sanitize_text_field($optionValue);
					} else {
						$optionsLoaded[$option] = sanitize_text_field($optionValue);
					}
				}
			} else {
				$optionsLoaded[$option] = sanitize_text_field($optionValue);
			}
		}

		return $optionsLoaded;
	}
}
