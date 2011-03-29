<?php

/**
 * YouTube: http://code.google.com/apis/youtube/2.0/reference.html
 *
 * @package default
 * @author Rob DiCiuccio
 */
class YouTube extends BaseAPI {

	public function init_related($nlp_obj) {

		$this->http_method = 'GET';

		// set API url
		$this->api_url = 'http://gdata.youtube.com/feeds/api/videos';

		$key_terms = array();
		foreach($nlp_obj->getEntities() as $term) {
			$key_terms[] = '"' . urlencode($term['name']) . '"';
			if(count($key_terms)==3) break;        // use top few entities only
		}
		$query = implode($key_terms, '|');  // OR

		// set API arguments
		$this->api_args = array(
			'alt' => 'jsonc',
			'max-results' => 10,
			'q' => $query,
			'v' => 2,
			'category' => !empty($GLOBALS['api_config']['YouTube']['category']) ? $GLOBALS['api_config']['YouTube']['category'] : ''	// multiples comma separated
		);
	}

	/**
	 * process & return related content
	 *
	 * @return array
	 */
	public function getRelated() {

		foreach($this->data['data']['items'] as $r) {

			$rel = array(
				'title' => $r['title'],
				'score' => '',
				'url' 	=> $r['player']['default'],
				'date' 	=> $r['uploaded'],
				'descr' => $r['description'],
				'source'=> $r['uploader']
			);
			$this->related[] = $rel;
		}

		return $this->related;
	}


}
