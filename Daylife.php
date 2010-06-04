<?php

/**
 * Daylife: http://cookbook.daylife.com/docs/SearchAPI
 *
 * @package default
 * @author Rob DiCiuccio
 */
class Daylife extends BaseAPI {

	protected $daylife_access_key = '';						// API KEYS HERE ###
	protected $daylife_secret_key = '';


	public function init_related($nlp_obj) {

		$this->http_method = 'GET';

		// set API url
		$this->api_url = 'http://freeapi.daylife.com/jsonrest/publicapi/4.8/search_getRelatedArticles';

		// generate query
		$query = '';
		$needOr = false;
		foreach ($nlp_obj->getEntities() as $e) {
			if ($needOr) $query .= " OR ";
			$query .= "\"{$e['name']}\"^".($e['score']*10);
			$needOr = true;
		}

		// set API arguments
		$this->api_args = array(
			'accesskey' => $this->daylife_access_key,
			'query' => $query,
			'limit' => 5,
			'signature' => hash('md5', $this->daylife_access_key . $this->daylife_secret_key . $query)
		);

	}
	
	
	/**
	 * process & return related content
	 *
	 * @return array
	 */
	public function getRelated() {
		
		foreach($this->data['response']['payload']['article'] as $r) {
						
			$rel = array(
				'title' => $r['headline'],
				'score' => $r['search_score'],
				'url' 	=> $r['url'],
				'date' 	=> $r['timestamp'],
				'descr' => $r['excerpt'],
				'source'=> '<a href="' . $r['source']['url'] . '" target="_blank">' . $r['source']['name'] . '</a>'
			);
			$this->related[] = $rel;
		}
		
		return $this->related;
	}
		
}
