<?php

/**
 * OpenCalais
 *
 * @package default
 * @author Rob DiCiuccio
 */
class OpenCalais extends BaseAPI {

	public function init_nlp($text) {

		$this->source_text = $text;     // save text
		
		// set API url
		$this->api_url = 'http://api.opencalais.com/enlighten/rest/';
		
		$paramsXml = <<< EOF
<c:params xmlns:c="http://s.opencalais.com/1/pred/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
	<c:processingDirectives c:contentType="TEXT/RAW" c:outputFormat="Application/JSON" c:enableMetadataType="GenericRelations,SocialTags"></c:processingDirectives>
	<c:userDirectives />
	<c:externalMetadata />
</c:params>
EOF;
		
		// set API arguments
		$this->api_args = array(
			'licenseID' => !empty($GLOBALS['api_config']['OpenCalais']['licenseID']) ? $GLOBALS['api_config']['OpenCalais']['licenseID'] : '',
			'paramsXML' => $paramsXml,
			'content' => stripslashes($text)
		);
	}
	
	
	/**
	 * post-process query result
	 *
	 * @return void
	 */
	protected function postProcess() {
		
		// $this->data = $this->raw_result;		// skip
		
		$this->data = array();	// reset
		$this->fixJson($this->raw_result);
		
	}
	
	
	/**
	 * process & return entities
	 *
	 * @return array
	 */
	public function getEntities() {

		if(empty($this->entities)) {
			foreach($this->data['entities'] as $eKey=>$eData) {

				$urls = array();
				if(array_key_exists('resolutions', $eData)) {
					foreach($eData['resolutions'] as $t) {
							$urls[] = $t['id'];
					}
				}
				$entity = array(
					'name' => $eData['name'],
					'score' => $eData['relevance'],
					'disambiguation' => $urls
				);
				$this->entities[] = $entity;
			}
		}

		return $this->entities;
	}



	/**
	 * fixJson
	 *
	 * OpenCalais returns JSON that requires some processing, per 
	 * http://opencalais.com/documentation/calais-web-service-api/interpreting-api-response/opencalais-json-output-format
	 *
	 * @param string $json 
	 * @return array mapped from fixed JSON format
	 */
	
	protected function fixJson($json) {
		foreach (json_decode($json, true) as $elemKey => $elemValue) {
			
			$group = @$elemValue['_typeGroup'];
			
			if (!is_null($group)) {
				if(!array_key_exists($group, $this->data)) $this->data[$group] = array();
				$this->data[$group][$elemKey] = $elemValue;
			} else {
				$this->data[] = $elemValue;
			}
		}
	}

	
}
