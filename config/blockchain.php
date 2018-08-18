<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Blockchain Wallet Identifier
|--------------------------------------------------------------------------
| The one you use for login (Wallet ID).
*/

$config['guid'] = '';

/*
|--------------------------------------------------------------------------
| Main password
|--------------------------------------------------------------------------
*/

$config['main_password'] = '';

/*
|--------------------------------------------------------------------------
| Second password
|--------------------------------------------------------------------------
| Set this if you enabled double encryption.
*/

$config['second_password'] = '';

/*
|--------------------------------------------------------------------------
| API code
|--------------------------------------------------------------------------
| Required if you are going to use `create_wallet()`.
*/

$config['api_code'] = '';

/*
|--------------------------------------------------------------------------
| Base URL and port
|--------------------------------------------------------------------------
| URL and port that points to the Blockchain Wallet Service.
| Check https://github.com/blockchain/service-my-wallet-v3#installation for installation guide.
*/

$config['base_url'] = 'http://127.0.0.1';
$config['port'] = 3000;