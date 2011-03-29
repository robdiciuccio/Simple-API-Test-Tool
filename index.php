<?php

/**
 * Simple Entity Extraction & Content API Test Tool
 * http://blog.viewchange.org/2010/05/entity-extraction-content-api-evaluation/
 *
 * @author Rob DiCiuccio, http://www.definitionstudio.com
 */

// enable error display
ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once('krumo/class.krumo.php');

// define APIs
$services = array('OpenCalais', 'Zemanta', 'Evri', 'AlchemyAPI', 'OpenAmplify', 'Yahoo', 'DBpediaSpotlight');
$content_services = array('Zemanta', 'Daylife', 'YahooBOSS', 'Bing', 'YouTube', 'Truveo', 'Vimeo','SocialActions','ZemantaSocialActions');

// load config
$config_file = 'config.php';
if(file_exists($config_file) && is_readable($config_file)) {
	include_once($config_file);
} else {
	$config_error = "Failed to load config file.";
}
              
// require models
require_once('BaseAPI.php');
foreach($services as $model) {
	require_once($model.'.php');
}
foreach($content_services as $model) {
	require_once($model.'.php');
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<title>API Test</title>
	
</head>

<body>

	<?php if(!empty($config_error)) echo "<div style=\"color:red\">{$config_error}</div>"; ?>
	
	<?php

	// ENTITIES
	if(!empty($_POST['content'])) {

		$time_start = microtime(true);
		
		$api = new $_POST['api'];
		// init NLP
		$api->init_nlp($_POST['content']);
		
		echo '<h3>ENTITIES: Submitting to ' . get_class($api) . '...</h3>';
		echo '<p style="font-style:italic">API URL: ' . $api->getURL() . '</p>';
		echo '<p>API Arguments:</p><pre>';
		print_r($api->getArgs());
		echo '</pre>';
		
		$api->query();
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		
		$curl_info = $api->getCurlInfo();

		echo '<p style="font-weight:bold">Raw Result (HTTP code: ' . $curl_info['http_code'] . '):</p>';
		krumo($api->getRawResult());
		
		echo '<p style="font-weight:bold">Parsed Result:</p>';
		krumo($api->getData());
		
		echo '<p style="font-weight:bold">ENTITIES:</p>';
		$entities = $api->getEntities();
		foreach($entities as $entity) {
			echo 'Name: <strong>' . $entity['name'] . '</strong> [score: ' . $entity['score'] . ']<br />';
			echo 'Linked Data: <br />';
			echo '<ul>';
			foreach($entity['disambiguation'] as $d) {
				echo '<li>' . $d . '</li>';
			}
			echo '</ul>';
		}
		echo "<p>Query took $time seconds</p>";
		
		echo '<hr />';
	}

	// RELATED CONTENT
	if(!empty($_POST['content_api'])) {

		$time_start = microtime(true);

		$content_api = new $_POST['content_api'];

		// init content (pass entity API)
		$content_api->init_related($api);

		echo '<h3>RELATED CONTENT: Submitting to ' . get_class($content_api) . '...</h3>';
		echo '<p style="font-style:italic">API URL: ' . $content_api->getURL() . '</p>';
		echo '<p>API Arguments:</p><pre>';
		print_r($content_api->getArgs());
		echo '</pre>';

		$content_api->query();

		$time_end = microtime(true);
		$time = $time_end - $time_start;

		$curl_info = $content_api->getCurlInfo();

		echo '<p style="font-weight:bold">Raw Result (HTTP code: ' . $curl_info['http_code'] . '):</p>';
		echo '<pre>';
		echo 'Query: ' . $curl_info['url'];
		echo '</pre>';
		krumo($content_api->getRawResult());

		echo '<p style="font-weight:bold">Parsed Result:</p>';
		krumo($content_api->getData());

		echo '<p style="font-weight:bold">RELATED CONTENT:</p>';
		$related = $content_api->getRelated();
		foreach($related as $r) {
			echo '<p>';
			echo 'Name: <a href="' . $r['url'] . '" target="_blank">' . $r['title'] . '</a> [score: ' . $r['score'] . ']<br />';
			echo 'Publish Date: ' . $r['date'] . '<br />';
			echo 'Description: ' . $r['descr'] . '<br />';
			echo 'Source: ' . $r['source'] . '<br />';
			echo '</p>';
		}
		echo "<p>Query took $time seconds</p>";

		echo '<hr />';
	}
	?>

	<form method="post">
	
		<p>
			<label>Entity API:</label>
			<select name="api">
				<?php foreach($services as $s) { ?>
				<option value="<?=$s?>"<?php if(@$_POST['api']==$s) echo ' selected="selected"';?>><?=$s?></option>
				<?php } ?>
			</select>

			<label>--> Content API:</label>
			<select name="content_api">
				<option value="">None</option>
				<?php foreach($content_services as $s) { ?>
				<option value="<?=$s?>"<?php if(@$_POST['content_api']==$s) echo ' selected="selected"';?>><?=$s?></option>
				<?php } ?>
			</select>
		</p>
		<label>Text:</label>
		<textarea name="content" style="width:500px;height:100px;"><?php if(!empty($_POST['content'])) echo stripslashes($_POST['content']); ?></textarea>
		<input type="submit" value="submit" />
	
	</form>


</body>
</html>
