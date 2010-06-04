<?php

/**
 * Vimeo: http://vimeo.com/api
 *
 * @package default
 * @author Rob DiCiuccio
 */
class Vimeo extends BaseAPI {

	protected $_consumer_key = '';						// API KEY HERE ###
	protected $_consumer_secret = '';

	const API_REST_URL = 'http://vimeo.com/api/rest/v2';

	public function init_related($nlp_obj) {

		$this->http_method = 'GET';

		// set API url
		$this->api_url = self::API_REST_URL;

		// generate query
		$key_terms = array();
		foreach($nlp_obj->getEntities() as $term) {
			$key_terms[] = '"' . $term['name'] . '"';
			if(count($key_terms)==2) break;            // use top few entities only
		}
		$query = implode($key_terms, ' AND ');

		// set API arguments
		$this->api_args = array(
			'method' => 'vimeo.videos.search',
			'full_response' => '1',
			'query' => $query,
			'format' => 'json',
			'per_page' => 5,
			'sort' => 'relevant'
		);

	}


	/**
	 * process & return related content
	 *
	 * @return array
	 */
	public function getRelated() {

		foreach($this->data['videos']['video'] as $r) {

			$rel = array(
				'title' => $r['title'],
				'score' => '',
				'url' 	=> $r['urls']['url'][0]['_content'],
				'date' 	=> $r['upload_date'],
				'descr' => substr($r['description'],0,300),
				'source'=> '<a href="' . $r['owner']['profileurl'] . '" target="_blank">' . $r['owner']['display_name'] . ' (Vimeo)</a>'
			);
			$this->related[] = $rel;
		}

		return $this->related;
	}


	/**
	 * query (OVERRIDE)
	 *
	 * @return void
	 */
	public function query() {

		$this->raw_result = $this->_request($this->api_args['method'],$this->api_args);

		$this->postProcess();

	}
	
	// ======================================================================
	// = code below is based on Vimeo sample code (if I remember correctly) =
	// ======================================================================

	/**
	 * Create the authorization header for a set of params.
	 *
	 * @param array $oauth_params The OAuth parameters for the call.
	 * @return string The OAuth Authorization header.
	 */
	private function _generateAuthHeader($oauth_params) {
		$auth_header = 'Authorization: OAuth realm=""';
		foreach ($oauth_params as $k => $v) {
			$auth_header .= ','.self::url_encode_rfc3986($k).'="'.self::url_encode_rfc3986($v).'"';
		}
		return $auth_header;
	}

	/**
	 * Call an API method.
	 *
	 * @param string $method The method to call.
	 * @param array $call_params The parameters to pass to the method.
	 * @param string $request_method The HTTP request method to use.
	 * @param string $url The base URL to use.
	 * @param boolean $cache Whether or not to cache the response.
	 * @param boolean $use_auth_header Use the OAuth Authorization header to pass the OAuth params.
	 * @return string The response from the method call.
	 */
	private function _request($method, $call_params = array(), $request_method = 'GET', $url = self::API_REST_URL, $cache = true, $use_auth_header = true) {

		// Prepare oauth arguments
		$oauth_params = array(
			'oauth_consumer_key' => $this->_consumer_key,
			'oauth_version' => '1.0',
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_timestamp' => time(),
			'oauth_nonce' => $this->_generateNonce()
		);

		$api_params = array();

		// Merge args
		foreach ($call_params as $k => $v) {
			if (strpos($k, 'oauth_') === 0) {
				$oauth_params[$k] = $v;
			}
			else {
				$api_params[$k] = $v;
			}
		}

		// Generate the signature
		$oauth_params['oauth_signature'] = $this->_generateSignature(array_merge($oauth_params, $api_params), $request_method, $url);

		// Merge all args
		$all_params = array_merge($oauth_params, $api_params);

		// Curl options
		if ($use_auth_header) {
			$params = $api_params;
		}
		else {
			$params = $all_params;
		}

		if (strtoupper($request_method) == 'GET') {
			$curl_url = $url.'?'.http_build_query($params);
			$curl_opts = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 30
			);
		}
		elseif (strtoupper($request_method) == 'POST') {
			$curl_url = $url;
			$curl_opts = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => http_build_query($params)
			);
		}

		// Authorization header
		if ($use_auth_header) {
			$curl_opts[CURLOPT_HTTPHEADER] = array($this->_generateAuthHeader($oauth_params));
                        // DEBUG
                        print_r($curl_opts[CURLOPT_HTTPHEADER]);
		}

		// Call the API
		$curl = curl_init($curl_url);
		curl_setopt_array($curl, $curl_opts);
		$response = curl_exec($curl);

		$this->curl_info = curl_getinfo($curl);

		curl_close($curl);

		return $response;
	}


	// #### OAuth support functions ####

	/**
	 * Generate a nonce for the call.
	 *
	 * @return string The nonce
	 */
	private function _generateNonce() {
		return md5(uniqid(microtime()));
	}

	/**
	 * Generate the OAuth signature.
	 *
	 * @param array $args The full list of args to generate the signature for.
	 * @param string $request_method The request method, either POST or GET.
	 * @param string $url The base URL to use.
	 * @return string The OAuth signature.
	 */
	private function _generateSignature($params, $request_method = 'GET', $url = self::API_REST_URL) {

		uksort($params, 'strcmp');
		$params = self::url_encode_rfc3986($params);

		// Make the base string
		$base_parts = array(
			strtoupper($request_method),
			$url,
			urldecode(http_build_query($params))
		);
		$base_parts = self::url_encode_rfc3986($base_parts);
		$base_string = implode('&', $base_parts);

		// Make the key
		$key_parts = array(
			$this->_consumer_secret,
			''
		);
		$key_parts = self::url_encode_rfc3986($key_parts);
		$key = implode('&', $key_parts);

		// Generate signature
		return base64_encode(hash_hmac('sha1', $base_string, $key, true));
	}

	/**
	 * URL encode a parameter or array of parameters (OAuth compatible)
	 *
	 * @param array/string $input A parameter or set of parameters to encode.
	 */
	public static function url_encode_rfc3986($input) {
		if (is_array($input)) {
			return array_map(array('Vimeo', 'url_encode_rfc3986'), $input);
		}
		elseif (is_scalar($input)) {
			return str_replace(array('+', '%7E'), array(' ', '~'), rawurlencode($input));
		}
		else {
			return '';
		}
	}

}
