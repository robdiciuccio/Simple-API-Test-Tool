<?php

/**
 * Bing: http://www.bing.com/developers/
 *
 * @package default
 * @author Rob DiCiuccio
 */
class Bing extends BaseAPI {

	public function init_related($nlp_obj) {

		$this->http_method = 'GET';

		// set API url
		$this->api_url = 'http://api.bing.net/json.aspx';

		$key_terms = array();
		foreach($nlp_obj->getEntities() as $term) {
			$key_terms[] = '"' . $term['name'] . '"';
			if(count($key_terms)==2) break;        // use top few entities only
		}
		$query = implode($key_terms, ' AND ');


		// set API arguments
		$this->api_args = array(
			'AppId' => !empty($GLOBALS['api_config']['Bing']['AppId']) ? $GLOBALS['api_config']['Bing']['AppId'] : '',
			'Sources' => !empty($GLOBALS['api_config']['Bing']['Sources']) ? $GLOBALS['api_config']['Bing']['Sources'] : 'Video',   // 'Video' OR 'News'
			'News.Count' => 10,
			'Video.Count' => 10,
			'Query' => $query
		);
	}

	/**
	 * process & return related content
	 *
	 * @return array
	 */
	public function getRelated() {

		if($this->api_args['Sources']=='News') {
			foreach($this->data['SearchResponse']['News']['Results'] as $r) {

				$rel = array(
					'title' => $r['Title'],
					'score' => '',
					'url' 	=> $r['Url'],
					'date' 	=> $r['Date'],
					'descr' => $r['Snippet'],
					'source'=> $r['Source']
				);
				$this->related[] = $rel;
			}
		} else {
			// Video
			foreach($this->data['SearchResponse']['Video']['Results'] as $r) {

				$rel = array(
					'title' => $r['Title'],
					'score' => '',
					'url' 	=> $r['PlayUrl'],
					'date' 	=> '',
					'descr' => '',
					'source'=> $r['SourceTitle']
				);
				$this->related[] = $rel;
			}
		}

		return $this->related;
	}


}
