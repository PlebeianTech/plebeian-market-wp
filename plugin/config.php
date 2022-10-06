<?php

# SETUP

# DEFAULTS
// This two must point to the same address when not using docker
//define('PM_API_URL_DEFAULT', 'http://localhost:5000/api');
//define('PM_API_URL_BACKEND_DEFAULT', 'http://api:5000/api');

define('PM_API_URL_DEFAULT', 'https://staging.plebeian.market/api');
define('PM_API_URL_BACKEND_DEFAULT', 'https://staging.plebeian.market/api');

//define('PM_API_URL_DEFAULT', 'https://plebeian.market/api');
//define('PM_API_URL_DEFAULT', 'https://staging.plebeian.market/api');

# ROUTES

#  Login
define('PM_API_GET_LOGIN_INFO_URL', '/login');
define('PM_API_GET_LOGIN_INFO_METHOD', 'GET');

define('PM_API_CHECK_LOGIN_URL', '/login?k1=');
define('PM_API_CHECK_LOGIN_METHOD', 'GET');

# User management
define('PM_API_USER_OPTIONS_URL', '/users/me');
define('PM_API_GET_USER_OPTIONS_METHOD', 'GET');
define('PM_API_SET_USER_OPTIONS_METHOD', 'PUT');

#  BuyNow
define('PM_API_LIST_BUYNOW_URL', '/listings/featured');

define('PM_API_GET_BUYNOW_URL', '/listings/{KEY}');
define('PM_API_GET_BUYNOW_METHOD', 'GET');

define('PM_API_NEW_BUYNOW_URL', '/listings');
define('PM_API_NEW_BUYNOW_METHOD', 'POST');
define('PM_API_NEW_BUYNOW_TWITTER_URL', '/listings/{KEY}/start-twitter');

define('PM_API_EDIT_BUYNOW_URL', '/listings/{KEY}');
define('PM_API_EDIT_BUYNOW_METHOD', 'PUT');

define('PM_API_DELETE_BUYNOW_URL', '/listings/{KEY}');
define('PM_API_DELETE_BUYNOW_METHOD', 'DELETE');

define('PM_API_BUY_BUYNOW_URL', '/listings/{KEY}/buy');
define('PM_API_BUY_BUYNOW_METHOD', 'PUT');

# Util / Misc
define('KRAKEN_BTCUSD_API_URL', 'https://api.kraken.com/0/public/Ticker?pair=XBTUSD');
define('KRAKEN_BTCUSD_API_CACHETIME', 60);