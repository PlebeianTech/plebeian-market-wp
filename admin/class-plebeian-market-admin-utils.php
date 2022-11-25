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
	static function plebeian_market_load_options($prefix = null, $remove_prefix = false): array
    {
		$optionsLoaded = [];

		foreach (PLEBEIAN_MARKET_OPTIONS as $option) {
			$optionValue = get_option($option);

			if ($prefix !== null && substr($option, 0, strlen($prefix)) === $prefix) {
				if ($optionValue !== false) {
					if ($remove_prefix) {
						$option_without_prefix = substr($option, strlen($prefix));
						$optionsLoaded[$option_without_prefix] = $optionValue;
					} else {
						$optionsLoaded[$option] = $optionValue;
					}
				}
			} else {
				$optionsLoaded[$option] = $optionValue;
			}
		}

		return $optionsLoaded;
	}
}
