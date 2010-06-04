<?php

/**
 * SocialActions: http://socialactions.pbworks.com/Social%C2%A0Actions%C2%A0API%C2%A0-%C2%A0Documentation
 *
 * @package default
 * @author Rob DiCiuccio
 */
class SocialActions extends BaseAPI {

	public function init_related($nlp_obj) {

		$this->http_method = 'GET';

		// set API url
		$this->api_url = 'http://search.socialactions.com/actions.json';

		$key_terms = array();
		foreach($nlp_obj->getEntities() as $term) {
			$key_terms[] = '"' . $term['name'] . '"';
			if(count($key_terms)==3) break;        // use top few entities only
		}
		$query = implode($key_terms, ' ');  // space separated


		// set API arguments
		$this->api_args = array(
			'match' => 'any',
			'limit' => 5,
			'q' => $query
		);
	}

	/**
	 * process & return related content
	 *
	 * @return array
	 */
	public function getRelated() {

		foreach($this->data as $r) {

			$rel = array(
				'title' => $r['title'] . ' [' . $r['platform_name'] . ']',
				'score' => '',
				'url' 	=> $r['url'],
				'date' 	=> '',
				'descr' => strip_tags(substr($r['description'],0,250)).'...',
				'source'=> 'Social Actions'
			);
			$this->related[] = $rel;
		}

		return $this->related;
	}


}
