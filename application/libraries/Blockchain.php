<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Blockchain Wallet API
 *
 * This library allows you to use the Blockchain Wallet API: https://blockchain.info/api/blockchain_wallet_api
 * In order for this library to work, you need to have the Blockchain Wallet Service installed.
 * https://github.com/blockchain/service-my-wallet-v3#getting-started
 *
 * @author	Mehdi Bounya
 * @link	https://github.com/mehdibo/Codeigniter-blockchain
 */

class Blockchain{
	/**
	 * Blockchain wallet ID (Wallet ID)
	 *
	 * @var string
	 */
	protected $guid;

	/**
	 * API code
	 *
	 * API code is required for creating wallets
	 * You can get one here: https://blockchain.info/api/api_create_code
	 * 
	 * @var string
	 */
	protected $api_code;

	/**
	 * Main Blockchain Wallet password
	 *
	 * @var string
	 */
	protected $main_password;

	/**
	 * Second Blockchain Wallet password
	 * 
	 * Only if double encryption is enabled
	 *
	 * @var string
	 */
	protected $second_password;

	/**
	 * Blockchain Wallet Service port
	 * 
	 * The one you passed to the `blockchain-wallet-service start` command
	 *
	 * @var integer
	 */
	protected $port = 3000;

	/**
	 * URL to the Blockchain Wallet Service
	 * 
	 * Usually it's localhost
	 *
	 * @var string
	 */
	protected $base_url = 'http://127.0.0.1';

	/**
	 * Constructor
	 * 
	 * Load config
	 *
	 * @param array $config
	 */
	public function __construct($config)
	{
		// Set config values
		$this->guid = $config['guid'];
		$this->main_password = $config['main_password'];
		// Optional ones
		$this->api_code = ( isset($config['api_code']) ) ? $config['api_code'] : NULL;
		$this->second_password = ( isset($config['second_password']) ) ? $config['second_password'] : NULL;
		$this->base_url = ( isset($config['base_url']) ) ? $config['base_url'] : $this->base_url;
		$this->port = ( isset($config['port']) ) ? $config['port'] : $this->port;

		log_message('info', 'Blockchain Class Initialized');

		// Make sure the base_url doesn't end with a trailing slash
		$this->base_url = rtrim($this->base_url, '/');

		// Check if the Blockchain Wallet service is running
		if ($this->_exec('') === NULL) {
			show_error('Blockchain Wallet: Unable to connect to Blockchain Wallet Service on: '.$this->base_url.':'.$this->port.'');
			log_message('error', "Blockchain: Unable to connect to Blockchain Wallet Service.");
		}
	}

	/**
	 * Create a new wallet
	 *
	 * @param string $password	  The new wallet's password, must be at least 10 characters.
	 * @param string $private_key A private key to add to the wallet (optional)
	 * @param string $email		  An e-mail to associate with the new wallet. (optional)
	 * @param string $label		  A label to set for the wallet's first address. (optional)
	 * 
	 * @return array API's response
	 */	
	public function create_wallet($password, $private_key = NULL, $email = NULL, $label = NULL)
	{
		// Make sure the password is at least 10 chars long
		if(strlen($password) < 10){
			return ['error' => 'Password must be at least 10 characters'];
		}

		// Prepare parameters
		$parameters = [
			'password' => $password,
			'api_code' => $this->api_code,
			'priv' => $private_key,
			'label' => $label,
			'email' => $email
		];

		// Execute
		return $this->_exec('api/v2/create', $parameters);
	}

	
	/**
	 * Send funds
	 *
	 * @param string $to	 Recipient's Bitcoin address.
	 * @param string $amount Amount to send in Satoshis.
	 * @param string $from	 Send from a specific Bitcoin address. (optional)
	 * @param string $fee	 Transaction fee value in satoshi. (Must be greater than default fee) (Optional)
	 * 
	 * @return array API's response
	 */
	public function send($to, $amount, $from = NULL, $fee = NULL)
	{
		// Build parameters
		$parameters = [
			'password' => $this->main_password,
			'to' => $to,
			'amount' => $amount,
			'second_password' => $this->second_password,
			'from' => $from,
			'fee' => $fee
		];

		// Execute
		return $this->_exec('merchant/'.urlencode($this->guid).'/payment', $parameters);
	}

	/**
	 * Send funds to multiple addresses
	 *
	 * @param array $recipients An array of 'address' => 'amount to send in satoshis'.
	 * @param string $from	 Send from a specific Bitcoin address. (optional)
	 * @param string $fee	 Transaction fee value in satoshi. (Must be greater than default fee) (Optional)
	 * 
	 * @return array API's response
	 */
	public function send_many($recipients, $from = NULL, $fee=NULL)
	{
		// Build parameters
		$parameters = [
			'password' => $this->main_password,
			'second_password' => $this->second_password,
			'recipients' => json_encode($recipients),
			'from' => $from,
			'fee' => $fee,
		];

		// Execute
		return $this->_exec('merchant/'.urlencode($this->guid).'/sendmany', $parameters);
	}

	/**
	 * Get wallet's balance
	 *
	 * @return array API's response
	 */
	public function wallet_balance()
	{
		// Build parameters
		$parameters = [
			'password' => $this->main_password,
		];

		// Execute
		return $this->_exec('merchant/'.urlencode($this->guid).'/balance', $parameters);
	}

	/**
	 * List all active addresses
	 *
	 * @return array API's response
	 */
 	public function list_addresses()
	{
		// Build parameters
		$parameters = [
			'password' => $this->main_password,
		];

		// Execute
		return $this->_exec('merchant/'.urlencode($this->guid).'/list', $parameters);
	}

	/**
	 * Get the balance of a specific address
	 *
	 * @param string $address Bitcoin address to lookup
	 * 
	 * @return array API's response
	 */
	public function address_balance($address)
	{
		// Build parameters
		$parameters = [
			'password' => $this->main_password,
			'address' => $address
		];

		// Execute
		return $this->_exec('merchant/'.urlencode($this->guid).'/address_balance', $parameters);
	}

	/**
	 * Generate a new address
	 *
	 * @param string $label The new address's label. (optional)
	 * 
	 * @return array API's response
	 */
	public function new_address($label = NULL)
	{
		// Build parameters
		$parameters = [
			'password' => $this->main_password,
			'second_password' => $this->second_password,
			'label' => $label,
		];

		// Execute
		return $this->_exec('merchant/'.urlencode($this->guid).'/new_address', $parameters);
	}

	/**
	 * Execute an API request
	 *
	 * @param string $endpoint	 API's endpoint (the part after the base_url)
	 * @param array  $parameters Array of GET parameters 'parameter'=>'value'
	 * 
	 * @return array API's decoded response
	 */
	private function _exec($endpoint, $parameters = NULL)
	{
		// Start building URL
		$url = $this->base_url;

		// Add port
		$url .= ':'.$this->port.'/';

		// Add endpint
		$url .= trim($endpoint, '/').'/';

		// Build query
		if(!empty($parameters)){
			$url .= '?'.http_build_query($parameters);
		}

		// Get CURL resource
		$curl = curl_init();
		// Set options
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_URL => $url,
		));

		// Send the request & save response
		$response = curl_exec($curl);

		// Close request to clear up some resources
		curl_close($curl);

		log_message('debug', 'Blockchain: URL executed '.$url);

		// Return the decoded response as an associative array
 		return json_decode($response, TRUE);
	}
}
