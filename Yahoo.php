<?php

/**
 * Yahoo: http://developer.yahoo.com/search/content/V1/termExtraction.html
 *
 * @package default
 * @author Rob DiCiuccio
 */
class Yahoo extends BaseAPI {
		
	public function init_nlp($text) {

		$this->source_text = $text;     // save text
		
		// set API url
		$this->api_url = 'http://search.yahooapis.com/ContentAnalysisService/V1/termExtraction';
		
		// set API arguments
		$this->api_args = array(
			'appid' => !empty($GLOBALS['api_config']['Yahoo']['appid']) ? $GLOBALS['api_config']['Yahoo']['appid'] : '',
			'output' => 'json',
			'context' => stripslashes($text)
		);
	}
	
	/**
	 * process & return entities
	 *
	 * @return array
	 */
	public function getEntities() {

		if(empty($this->entities)) {
			foreach($this->data['ResultSet']['Result'] as $e) {

				$entity = array(
					'name' => $e,
					'score' => '',
					'disambiguation' => array()
				);
				$this->entities[] = $entity;
			}
		}

		return $this->entities;
	}
	
		
}
