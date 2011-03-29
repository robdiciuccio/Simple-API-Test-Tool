<?php

/**
 * AlchemyAPI: http://www.alchemyapi.com/api/entity/textc.html
 *
 * @package default
 * @author Rob DiCiuccio
 */
class DBpediaSpotlight extends BaseAPI {

	public function init_nlp($text) {

		$this->http_method = 'GET';

		$this->source_text = $text;     // save text

		// set API url
		$this->api_url = 'http://spotlight.dbpedia.org/rest/annotate';

		// set API arguments
		$this->api_args = array(
			'text' => stripslashes($text),
			'confidence' => !empty($GLOBALS['api_config']['DBpediaSpotlight']['confidence']) ? $GLOBALS['api_config']['DBpediaSpotlight']['confidence'] : 0.4,
			'support' => !empty($GLOBALS['api_config']['DBpediaSpotlight']['support']) ? $GLOBALS['api_config']['DBpediaSpotlight']['support'] : 20
		);
	}

	/**
	 * process & return entities
	 *
	 * @return array
	 */
	public function getEntities() {

		if(empty($this->entities)) {
			foreach($this->data['Resources'] as $e) {

				$urls = array($e['@URI']);

				$entity = array(
					'name' => $e['@URI'],
					'score' => $e['@similarityScore'],
					'disambiguation' => $urls
				);
				$this->entities[] = $entity;
			}
		}

		return $this->entities;
	}

}
