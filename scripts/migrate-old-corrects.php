<?php
/**
 * Perform migration of old correct question answer labels to new ones
 *
 * @package Questions
 */

if (php_sapi_name() !== 'cli') {
  throw new Exception('This script must be run from the CLI.');
}

// Configure with "main site". Needed so subsite_manager can identify our instance.

// Production
//$_SERVER["HTTP_HOST"] = "www.pleio.nl";
//$_SERVER["HTTPS"] = true;

// Development
$_SERVER["HTTP_HOST"] = "pleio.localhost.nl";

require_once(dirname(dirname(dirname(__FILE__))) . "/../engine/start.php");
$ia = elgg_set_ignore_access(true);

$sites = subsite_manager_get_subsites(0);
$site_guids = array();
foreach ($sites as $site) {
  array_push($site_guids, $site->guid);
}

$options = array(
  'metadata_names' => array('correct_answer'),
  'metadata_values' => array('1'),
  'sites_guids' => $site_guids
);

$answers = new ElggBatch('elgg_get_entities_from_metadata', $options);

foreach($answers as $answer) { 
  $question = $answer->getContainerEntity();
  
  if (add_entity_relationship($question->guid, "correctAnswer", $answer->guid)) {
    unset($answer->correct_answer);
    $answer->save();    
    
    echo "[Answer] With GUID " . $answer->guid . " updated.\n";    
  }
}

elgg_set_ignore_access($ia);