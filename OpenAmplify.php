<?php

/**
 * OpenAmplify: http://community.openamplify.com/blogs/quickstart/pages/overview.aspx
 *
 * First attempt at site registration failed, password was reset by admin. Initial attempts to use the API resulted in connection timeouts, subsequent requests were very slow [FAIL].
 *
 * @package default
 * @author Rob DiCiuccio
 */
class OpenAmplify extends BaseAPI {
		
	public function init_nlp($text) {

		$this->source_text = $text;     // save text
		
		// set API url
		$this->api_url = 'http://portaltnx20.openamplify.com/AmplifyWeb_v20/AmplifyThis';
		
		// set API arguments
		$this->api_args = array(
			'apiKey' => !empty($GLOBALS['api_config']['OpenAmplify']['apikey']) ? $GLOBALS['api_config']['OpenAmplify']['apikey'] : '',
			'analysis' => 'all',
			'outputFormat' => 'json',
			'InputText' => stripslashes($text)
		);
	}
	
	/**
	 * process & return entities
	 *
	 * @return array
	 */
	public function getEntities() {

		if(empty($this->entities)) {
			foreach($this->data['ns1:AmplifyResponse']['AmplifyReturn']['Topics']['TopTopics'] as $e) {

				$entity = array(
					'name' => $e['Topic']['Name'],
					'score' => $e['Topic']['Value'],
					'disambiguation' => array()
				);
				$this->entities[] = $entity;
			}
		}

		return $this->entities;
	}
	
		
}
