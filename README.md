# Codeigniter-blockchain
A simple Codeigniter library to use the Blockchain Wallet API
## Contents

  * [Getting Started](#getting-started)
  * [Documentation](#documentation)
  
 ## Getting Started

To use the blockchain wallet API, make sure you read the [Blockchain API](https://blockchain.info/api) documentations.
You will need to run the Blockchain wallet service, Click [here](https://github.com/blockchain/service-my-wallet-v3) for setup instructions.

Start by completing the following steps:

  1. Copy `/application/libraries/Blockchain.php` to the `/application/libraries` folder.
  2. Load the library using the Codeigniter loader `$this->load->library('blockchain' , $config)`, 
  3. See the [documentation](#documentation) for usage.
  4. And that's it!
	
To use this class outside Codeigniter just remove the following line found in the top:

`defined('BASEPATH') OR exit('No direct script access allowed');`

 ## Documentation
  ### Loading the library
Make sure you followed the steps on [Getting Started](#getting-started) first, after that you can simply load the library using:
`$this->load->library('blockchain' , $config)`

`$config` options are:
  * `guid` - Blockchain Wallet Identifier. (the one used for login - Wallet ID)
  * `main_password` - Main Blockchain password.
  * `second_password` - You need to set this if double encryption is enabled .
  * `api_code` - You API code, required if you are going to use the create_wallet() function.
  * `base_url` - The base url used for the API. (default:http://127.0.0.1)
  * `port` - If you would like to set a custom port. (default:3000)
  
  ### Create wallet
`$this->blockchain->create_wallet($options)`

Options are:
  * `password` - The new wallet password, must be at least 10 charachters.
  * `private_key` - A private key to add to the wallet (Wallet import format preferred). (optional)
  * `email` - An e-mail to associate to the new wallet. (optional)
  * `label` - A label to set for the wallet's first address. (Alphanumeric only) (optional)
 
  ### Send funds
`$this->blockchain->send($to,$amount,$from,$fee)`

Parameters are:
  * `to` - Recipient Bitcoin Address.
  * `amount` - Amount in satoshis.
  * `from` - Send from a specific Bitcoin Address. (Optional)
  * `fee` - Transaction fee value in satoshi. (Must be greater than default fee) (Optional)
  
  ### Send to multiple addresses
`$this->blockchain->send_many($recipients,$from,$fee)`
 
 Parameters are:
  * `recipients` - Recipients Bitcoin Address as an associative array, `$address=>$amount`. (amount in satoshis).
  * `from` - Send from a specific Bitcoin Address. (Optional)
  * `fee` - Transaction fee value in satoshi. (Must be greater than default fee) (Optional)

  ### Get wallet balance
`$this->blockchain->wallet_balance()`

No parameters.

  ### List addresses
`$this->blockchain->wallet_balance()`

No parameters.

  ### Get address balance
`$this->blockchain->wallet_balance($address)`
  
 Parameters are:
  * `address` - The bitcoin address to get it's balance.
  
  ### Create new address
`$this->blockchain->new_address($label)`

 Parameters are:
  * `label` - Label to attach to the new address. (optional)
  
