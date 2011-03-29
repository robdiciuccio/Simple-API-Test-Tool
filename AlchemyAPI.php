<?php

/**
 * AlchemyAPI: http://www.alchemyapi.com/api/entity/textc.html
 *
 * @package default
 * @author Rob DiCiuccio
 */
class AlchemyAPI extends BaseAPI {
		
	public function init_nlp($text) {

		$this->source_text = $text;     // save text
		
		// set API url
		$this->api_url = 'http://access.alchemyapi.com/calls/text/TextGetRankedNamedEntities';
		
		// set API arguments
		$this->api_args = array(
			'apikey' => !empty($GLOBALS['api_config']['AlchemyAPI']['apikey']) ? $GLOBALS['api_config']['AlchemyAPI']['apikey'] : '',
			'outputMode' => 'json',
			'disambiguate' => 1,
			'linkedData' => 1,
			'text' => stripslashes($text)
		);
	}
	
	/**
	 * process & return entities
	 *
	 * @return array
	 */
	public function getEntities() {

		if(empty($this->entities)) {
			foreach($this->data['entities'] as $e) {

				$urls = array();
				if(!empty($e['disambiguated'])) {
					foreach($e['disambiguated'] as $dKey=>$dVal) {
						if($dKey != 'name') {
							$urls[] = $dVal . ' [' . $dKey . ']';
						}
					}
				}

				$entity = array(
					'name' => $e['text'],
					'score' => $e['relevance'],
					'disambiguation' => $urls
				);
				$this->entities[] = $entity;
			}
		}

		return $this->entities;
	}
		
}
