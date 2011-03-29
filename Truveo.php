<?php

/**
 * Truveo: http://developer.truveo.com
 *
 * @package default
 * @author Rob DiCiuccio
 */
class Truveo extends BaseAPI {

	public function init_related($nlp_obj) {

		$this->http_method = 'GET';

		// set API url
		$this->api_url = 'http://xml.truveo.com/apiv3';

		// generate query
		$key_terms = array();
		foreach($nlp_obj->getEntities() as $term) {
			$key_terms[] = '"' . $term['name'] . '"';
			if(count($key_terms)==3) break;            // use top few entities only
		}
		$query = implode($key_terms, ' OR ');       // AND category:news

		// set API arguments
		$this->api_args = array(
			'appid' => !empty($GLOBALS['api_config']['Truveo']['appid']) ? $GLOBALS['api_config']['Truveo']['appid'] : '',
			'query' => $query,
			'method' => 'truveo.videos.getVideos',
			'results' => 10,
			'format' => 'json',
			//'showAdult' => 0
		);

	}


	/**
	 * process & return related content
	 *
	 * @return array
	 */
	public function getRelated() {

		foreach($this->data['response']['data']['results']['videoSet']['videos'] as $r) {

			$rel = array(
				'title' => $r['title'],
				'score' => $r['textRelevancy'],
				'url' 	=> $r['videoUrl'],
				'date' 	=> $r['dateProduced'],
				'descr' => $r['description'],
				'source'=> '<a href="' . $r['channelUrl'] . '" target="_blank">' . $r['channel'] . '</a>'
			);
			$this->related[] = $rel;
		}

		return $this->related;
	}

}
