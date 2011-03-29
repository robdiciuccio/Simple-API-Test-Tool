<?php

/**
 * Zemanta: http://developer.zemanta.com/docs/Home
 *
 * @package default
 * @author Rob DiCiuccio
 */
class Zemanta extends BaseAPI {
			
	public function init_nlp($text) {

		$this->source_text = $text;     // save text
		
		// set API url
		$this->api_url = 'http://api.zemanta.com/services/rest/0.0/';
		
		// set API arguments
		$this->api_args = array(
			'method' => 'zemanta.suggest_markup',				// NOTE: there are TWO useful methods here: zemanta.suggest and zemanta.suggest_markup (returns semantic links only)
			'api_key' => !empty($GLOBALS['api_config']['Zemanta']['api_key']) ? $GLOBALS['api_config']['Zemanta']['api_key'] : '',
			'text' => stripslashes($text),
			'return_rdf_links' => 1,
			//'emphasis' => Keyword::toString($keywords),		// Currenty emphasis only takes one word! Suggestion emailed to CTO to behave more like Daylife
			'return_images' => 0,
			//'return_categories' => 'dmoz',
			'format' => 'json'
		);
	}

	public function init_related($nlp_obj) {

		// set API url
		$this->api_url = 'http://api.zemanta.com/services/rest/0.0/';
		
		// set API arguments
		$this->api_args = array(
			'method' => 'zemanta.suggest',
			'api_key' => !empty($GLOBALS['api_config']['Zemanta']['api_key']) ? $GLOBALS['api_config']['Zemanta']['api_key'] : '',
			'text' => stripslashes($nlp_obj->getText()),
			//'return_rdf_links' => 1,
			'articles_highlight' => 1,
			'return_images' => 0,
			'articles_limit' => 10,
			'format' => 'json'
		);

        }
	
	/**
	 * process & return entities
	 *
	 * @return array
	 */
	public function getEntities() {

		if(empty($this->entities)) {
			foreach($this->data['markup']['links'] as $e) {

				$urls = array();
				foreach($e['target'] as $t) {
					$urls[] = $t['url'];
				}

				$entity = array(
					'name' => $e['anchor'],
					'score' => $e['confidence'],
					'disambiguation' => $urls
				);
				$this->entities[] = $entity;
			}
		}

		return $this->entities;
	}
	
	/**
	 * process & return related content
	 *
	 * @return array
	 */
	public function getRelated() {
		
		foreach($this->data['articles'] as $r) {
						
			$rel = array(
				'title' => $r['title'],
				'score' => $r['confidence'],
				'url' 	=> $r['url'],
				'date' 	=> $r['published_datetime'],
				'descr' => $r['text_highlight'],
				'source'=> ''
			);
			$this->related[] = $rel;
		}
		
		return $this->related;
	}
		
}
