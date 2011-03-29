<?php

/**
 * BaseAPI
 *
 * @package default
 * @author Rob DiCiuccio
 */
class BaseAPI {

	protected $source_text;
	protected $api_url;	
	protected $api_args = array();
	
	protected $raw_result;
	protected $data = array();
	
	protected $entities = array();		// element array keys: 'text' (string), 'score' (number), 'disambiugation' (array)
	protected $related = array();		// element array keys: 'title' (string), 'score' (number), 'url' (string), 'date' (string)

	protected $curl_info = array();

	protected $http_method = 'POST';


	public function getText() { return $this->source_text; }
	public function getURL() { return $this->api_url; }
	public function getArgs() { return $this->api_args; }
	public function getRawResult() { return $this->raw_result; }
	public function getData() { return $this->data; }
	public function getCurlInfo() { return $this->curl_info; }
	
	/**
	 * query
	 *
	 * @return void
	 */
	public function query() {

		if($this->http_method == 'GET') {
			$ch = curl_init($this->api_url.'?'.http_build_query($this->api_args));
		} else {
			$ch = curl_init($this->api_url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->api_args));
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$this->raw_result = curl_exec($ch);
		$this->curl_info = curl_getinfo($ch);
		curl_close($ch);
				
		$this->postProcess();
		
	}
	
	/**
	 * post-process query result
	 *
	 * @return void
	 */
	protected function postProcess() {
		
		$this->data = json_decode($this->raw_result, true);
		
	}
	
	/**
	 * process & return entities
	 *
	 * @return array
	 */
	public function getEntities() {
		return $this->entities;
	}
	
	/**
	 * process & return related content
	 *
	 * @return array
	 */
	public function getRelated() {
		return $this->related;
	}
	
}