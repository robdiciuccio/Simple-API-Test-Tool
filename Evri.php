<?php

/**
 * Evri
 *
 * @package default
 * @author Rob DiCiuccio
 */
class Evri extends BaseAPI {
		
	public function init_nlp($text) {

		$this->source_text = $text;     // save text
		
		// set API url
		$this->api_url = 'http://api.evri.com/v1/media/entities.json';
		
		// set API arguments
		$this->api_args = array(
			'uri' => 'http://localhost?nc='.time(),     // results seem to cache or return empty if url is not unique
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
			foreach($this->data['evriThing']['graph']['entities']['entity'] as $e) {

				$urls = array();

				$entity = array(
					'name' => $e['name']['$'],
					'score' => $e['@score'],
					'disambiguation' => array('http://www.evri.com'.$e['@href'])
				);
				$this->entities[] = $entity;
			}
		}

		return $this->entities;
	}
	
	
}
