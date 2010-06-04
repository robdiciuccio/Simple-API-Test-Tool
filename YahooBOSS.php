<?php

/**
 * Yahoo: http://developer.yahoo.com/search/content/V1/termExtraction.html
 *
 * @package default
 * @author Rob DiCiuccio
 */
class YahooBOSS extends BaseAPI {

	public function init_related($nlp_obj) {

		$this->http_method = 'GET';

		// set API url
		$this->api_url = 'http://boss.yahooapis.com/ysearch/news/v1/';

		// generate query

		$key_terms = array();
		foreach($nlp_obj->getEntities() as $term) {
			$key_terms[] = '"' . $term['name'] . '"';
			if(count($key_terms)==2) break;        // use top few entities only
		}
		$query = implode($key_terms, ' ');

		// append query to api URL (before GET vars)
		$this->api_url .= rawurlencode($query);

		// set API arguments
		$this->api_args = array(
			'appid' => '',						// API KEY HERE ###
			'format' => 'json',
			'count' => 5
		);
	}

	/**
	 * process & return related content
	 *
	 * @return array
	 */
	public function getRelated() {

		foreach($this->data['ysearchresponse']['resultset_news'] as $r) {

			$rel = array(
				'title' => $r['title'],
				'score' => '',
				'url' 	=> $r['url'],
				'date' 	=> $r['date'] . ' ' . $r['time'],
				'descr' => $r['abstract'],
				'source'=> '<a href="' . $r['sourceurl'] . '" target="_blank">' . $r['source'] . '</a>'
			);
			$this->related[] = $rel;
		}

		return $this->related;
	}


}
