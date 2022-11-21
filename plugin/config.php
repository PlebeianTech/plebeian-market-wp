<?php

# SETUP

# DEFAULTS
// This two must point to the same address when not in development environment (docker)
const PM_API_URL_DEFAULT = 'https://plebeian.market/api';
const PM_API_URL_BACKEND_DEFAULT = 'https://plebeian.market/api';

# API ROUTES

#  Login
const PM_API_GET_LOGIN_INFO_URL = '/login';
const PM_API_GET_LOGIN_INFO_METHOD = 'GET';

const PM_API_CHECK_LOGIN_URL = '/login?k1=';
const PM_API_CHECK_LOGIN_METHOD = 'GET';

# User management
const PM_API_USER_OPTIONS_URL = '/users/me';
const PM_API_GET_USER_OPTIONS_METHOD = 'GET';
const PM_API_SET_USER_OPTIONS_METHOD = 'PUT';

# BuyNow
const PM_API_LIST_BUYNOW_URL = '/users/me/listings';

const PM_API_GET_BUYNOW_URL = '/listings/{KEY}';
const PM_API_GET_BUYNOW_METHOD = 'GET';

const PM_API_NEW_BUYNOW_URL = '/users/me/listings';
const PM_API_NEW_BUYNOW_METHOD = 'POST';

const PM_API_EDIT_BUYNOW_URL = '/listings/{KEY}';
const PM_API_EDIT_BUYNOW_METHOD = 'PUT';

const PM_API_DELETE_BUYNOW_URL = '/listings/{KEY}';
const PM_API_DELETE_BUYNOW_METHOD = 'DELETE';

const PM_API_START_BUYNOW_URL = '/listings/{KEY}/publish';
const PM_API_START_BUYNOW_METHOD = 'PUT';

const PM_API_BUY_BUYNOW_URL = '/listings/{KEY}/buy';
const PM_API_BUY_BUYNOW_METHOD = 'PUT';

//      media
const PM_API_ADD_MEDIA_BUYNOW_URL = '/listings/{KEY}/media';
const PM_API_ADD_MEDIA_BUYNOW_METHOD = 'POST';

const PM_API_DELETE_MEDIA_BUYNOW_URL = '/listings/{KEY}/media/{HASH}';
const PM_API_DELETE_MEDIA_BUYNOW_METHOD = 'DELETE';

# Auctions
const PM_API_LIST_AUCTIONS_URL = '/users/me/auctions';

const PM_API_DELETE_AUCTIONS_URL = '/auctions/{KEY}';
const PM_API_DELETE_AUCTIONS_METHOD = 'DELETE';

const PM_API_START_AUCTIONS_URL = '/auctions/{KEY}/publish';
const PM_API_START_AUCTIONS_METHOD = 'PUT';

const PM_API_NEW_AUCTIONS_URL = '/users/me/auctions';
const PM_API_NEW_AUCTIONS_METHOD = 'POST';

const PM_API_EDIT_AUCTIONS_URL = '/auctions/{KEY}';
const PM_API_EDIT_AUCTIONS_METHOD = 'PUT';

const PM_API_BID_AUCTIONS_URL = '/auctions/{KEY}/bids';
const PM_API_BID_AUCTIONS_METHOD = 'POST';

//      media
const PM_API_ADD_MEDIA_AUCTION_URL = '/auctions/{KEY}/media';
const PM_API_ADD_MEDIA_AUCTION_METHOD = 'POST';

const PM_API_DELETE_MEDIA_AUCTION_URL = '/auctions/{KEY}/media/{HASH}';
const PM_API_DELETE_MEDIA_AUCTION_METHOD = 'DELETE';



# Util / Misc
const KRAKEN_BTCUSD_API_URL = 'https://api.kraken.com/0/public/Ticker?pair=XBTUSD';
const KRAKEN_BTCUSD_API_CACHETIME = 60;

const FORM_FIELDS_PREFIX = 'plebeian_market_widget_';

const PM_OPTIONS = [
    'plebeian_market_auth_key',
    'plebeian_market_url_connect',

    'plebeian_market_widget_size',
    'plebeian_market_widget_title_fontsize',
    'plebeian_market_widget_description_fontsize',
    'plebeian_market_widget_slideshow_enabled',
    'plebeian_market_widget_slideshow_delay',
    'plebeian_market_widget_show_price_fiat',
    'plebeian_market_widget_show_price_sats',
    'plebeian_market_widget_show_shipping_info',
    'plebeian_market_widget_show_quantity_info',

    'plebeian_market_cutomization_css',
    'plebeian_market_cutomization_js',
];

define("pluginBasePath", plugin_dir_url(__FILE__));

define("DEMO_BUYNOW_PRODUCT", [
    'key' => 'ABCXYZ123',
    'title' => 'INFINITE POTENTIAL',
    'description' => 'It is impossible, even for the most knowledgable, to appreciate the full impact bitcoin will have upon humanity 

More than a change in economics, there will be a fundamental change in the human mindset and our way of life 

As ever, Satoshi will ride the waves of change',
    'available_quantity' => 1,
    'media' => [
        [
            'index' => 1,
            'url' => pluginBasePath . 'common/img/demo_product/picture1.jpeg'
        ],
        [
            'index' => 2,
            'url' => pluginBasePath . 'common/img/demo_product/picture2.jpeg'
        ],
        [
            'index' => 3,
            'url' => pluginBasePath . 'common/img/demo_product/picture3.jpeg'
        ],
        [
            'index' => 4,
            'url' => pluginBasePath . 'common/img/demo_product/picture4.jpeg'
        ]
    ],
    'price_usd' => 500,
    'shipping_from' => 'UK',
    'shipping_domestic_usd' => 25,
    'shipping_worldwide_usd' => 50,
    'seller_nym' => 'chiefmonkey'
]);
