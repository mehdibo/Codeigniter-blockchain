<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CodeIgniter Blockchain Class
 *
 * Provides easy way to use the Blockchain api
 *
 * @author		Mehdi Bounya
 * @link		https://github.com/mehdibo/Codeigniter-blockchain
 */

class Blockchain{
	protected $guid; // Blockchain wallet identifier (Wallet ID)
	protected $api_code; // API code, required for creating wallets
	protected $main_password; // Main Blockchain Wallet password
	protected $second_password; // Second Blockchain Wallet password if double encryption is enabled
	protected $port = 3000; // Blockchain Wallet service port
	protected $base_url = 'http://127.0.0.1'; // Base url to connect to the Blockchain Wallet service

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

		// Check if the Blockchain Wallet service is running
		if ($this->execute($this->base_url.':'.$this->port) === NULL) {
			show_error('Blockchain: Unable to connect to Blockchain Wallet service on: '.$this->base_url.':'.$this->port.'');
			log_message('error', "Blockchain: Unable to connect to Blockchain Wallet service.");
		}
	}

	// Create a wallet
	public function create_wallet($options)
	{
		// Get the base url
		$url=$this->base_url;

		// Add the port
		$url.=':'.$this->port.'/';

		// Add the api url
		$url.='api/v2/create';

		// Add options
		// password
		$url.='?password='.$options['password'];

		// api_code
		$url.='&api_code='.$this->api_code;

		// private key (optional)
		if (isset($options['private_key'])) {
			$url.='&priv='.$options['private_key'];
		}

		// label (optional)
		if (isset($options['label'])) {
			$url.='&label='.$options['label'];
		}

		// email (optional)
		if (isset($options['email'])) {
			$url.='&email='.$options['email'];
		}

		// Execute
		return $this->execute($url);
	 }

	// Send funds
	public function send($to,$amount,$from=NULL,$fee=NULL)
	{
		// Get the base url
		$url=$this->base_url;

		// Add the port
		$url.=':'.$this->port.'/';

		// Add the api url
		$url.='merchant/'.$this->guid.'/payment';

		// Add options
		// password
		$url.='?password='.$this->main_password;

		// second password
		if (!empty($this->second_password)) {
			$url.='&second_password='.$this->second_password;
		}

		// Recipient Bitcoin Address
		$url.='&to='.$to;

		// Amount in satoshi
		$url.='&amount='.$amount;

		// From Bitcoin address
		if (!empty($from)) {
			$url.='&from='.$from;
		}

		// Transaction fee in satoshi
		if (!empty($fee)) {
			$url.='&fee='.$fee;
		}

		// Execute
		return $this->execute($url);
	}

	public function send_many($recipients,$from=NULL,$fee=NULL)
	{
		// Get the base url
		$url=$this->base_url;

		// Add the port
		$url.=':'.$this->port.'/';

		// Add the api url
		$url.='merchant/'.$this->guid.'/sendmany';

		// Add options
		// password
		$url.='?password='.$this->main_password;

		// second password
		if (!empty($this->second_password)) {
			$url.='&second_password='.$this->second_password;
		}

		// Recipients Bitcoin Address
		$url.='&recipients='.urlencode(json_encode($recipients));

		// From Bitcoin address
		if (!empty($from)) {
			$url.='&from='.$from;
		}

		// Transaction fee in satoshi
		if (!empty($fee)) {
			$url.='&fee='.$fee;
		}

		// Execute
		return $this->execute($url);
	}

	// Get balance
	public function wallet_balance()
	{
		// Get the base url
		$url=$this->base_url;

		// Add the port
		$url.=':'.$this->port.'/';

		// Add the api url
		$url.='merchant/'.$this->guid.'/balance';

		// Add options
		// password
		$url.='?password='.$this->main_password;

		// Execute
		return $this->execute($url);
	}

 	public function list_addresses()
	{
		// Get the base url
		$url=$this->base_url;

		// Add the port
		$url.=':'.$this->port.'/';

		// Add the api url
		$url.='merchant/'.$this->guid.'/list';

		// Add options
		// password
		$url.='?password='.$this->main_password;

		// Execute
		return $this->execute($url);
	}

	public function address_balance($address)
	{
		// Get the base url
		$url=$this->base_url;

		// Add the port
		$url.=':'.$this->port.'/';

		// Add the api url
		$url.='merchant/'.$this->guid.'/address_balance';

		// Add options
		// password
		$url.='?password='.$this->main_password;

		// address
		$url.='&address='.$address;

		// Execute
		return $this->execute($url);
	}

	public function new_address($label=NULL)
	{
		// Get the base url
		$url=$this->base_url;

		// Add the port
		$url.=':'.$this->port.'/';

		// Add the api url
		$url.='merchant/'.$this->guid.'/new_address';

		// Add options
		// password
		$url.='?password='.$this->main_password;

		// second_password
		if (!empty($this->second_password)) {
			$url.='&second_password='.$this->second_password;
		}

		// label
		if (!empty($label)) {
			$url.='&label='.$label;
		}

		// Execute
		return $this->execute($url);

	}

	public function execute($url)
	{
		// Get CURL resource
		$curl = curl_init();
		// Set options
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_URL => $url,
			// CURLOPT_SSL_VERIFYPEER => FALSE,
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
