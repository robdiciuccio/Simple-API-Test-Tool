<?php

/**
 * Daylife: http://cookbook.daylife.com/docs/SearchAPI
 *
 * @package default
 * @author Rob DiCiuccio
 */
class Daylife extends BaseAPI {

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

		$access_key = !empty($GLOBALS['api_config']['Daylife']['access_key']) ? $GLOBALS['api_config']['Daylife']['access_key'] : '';
		$secret_key = !empty($GLOBALS['api_config']['Daylife']['secret_key']) ? $GLOBALS['api_config']['Daylife']['secret_key'] : '';

		// set API arguments
		$this->api_args = array(
			'accesskey' => $access_key,
			'query' => $query,
			'limit' => 10,
			'signature' => hash('md5', $access_key . $secret_key . $query)
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
