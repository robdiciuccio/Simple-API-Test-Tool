<?php

/**
 * SocialActions v2: http://wiki.socialactions.com/w/page/33195836/Social%C2%A0Actions%C2%A0API+V2%C2%A0-%C2%A0Documentation
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
			'limit' => 10,
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
				'title' => $r['action']['title'] . ' [' . $r['action']['platform_name'] . ']',
				'score' => $r['action']['score'],
				'url' 	=> $r['action']['url'],
				'date' 	=> $r['action']['created_at'],
				'descr' => strip_tags(substr($r['action']['description'],0,250)).'...',
				'source'=> '<a href="' . $r['action']['platform_url'] . '" target="_blank">' . $r['action']['platform_name'] . '</a>'
			);
			$this->related[] = $rel;
		}

		return $this->related;
	}


}
