<?php

/**
 * Social Actions via Zemanta: http://developer.zemanta.com/wiki/examples/socialactions
 *
 * @package default
 * @author Rob DiCiuccio
 */
class ZemantaSocialActions extends BaseAPI {
		
	protected $zemanta_api_key = '';							// API KEY HERE ###
	
	public function init_related($nlp_obj) {

		// set API url
		$this->api_url = 'http://api.zemanta.com/services/rest/0.0/';

		// set API arguments
		$this->api_args = array(
			'method' => 'zemanta.suggest',
			'api_key' => $this->zemanta_api_key,
			'text' => stripslashes($nlp_obj->getText()),
			'sourcefeed_ids' => '14451 2127',
			'personal_scope' => 1,
			'articles_highlight' => 1,
			'return_images' => 0,
			//'articles_limit' => 5,
			'format' => 'json'
		);

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
				'descr' => @$r['text_highlight'],
				'source'=> ''
			);
			$this->related[] = $rel;
		}
		
		return $this->related;
	}
		
}
