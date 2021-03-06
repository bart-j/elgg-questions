<?php
/**
 * View a question, all internal answers
 *
 * @package ElggQuestions
 */

$title = $question->title;
elgg_push_breadcrumb($title);

// build page elements
$title_icon = "";

// switch to go from backend to frontend
$overview = elgg_view('questions/workflow/overview', array('question'=>$question,'full_view'=>true));

$content = elgg_view_entity($question, array('full_view' => true));

// show all answers
$options = array(
  'type' => 'object',
  'subtype' => 'answer',
  'container_guid' => $question->guid,
  'count' => true,
  'limit' => false,
  'pagination' => false,
  'order_by' => 'e.time_created'
);

$answers = elgg_list_entities($options);
$count = elgg_get_entities($options);

if ($count > 0) {
  if ($count == 1) {
    $text = elgg_echo('answer');
  } else {
    $text = elgg_echo('answers');
  }

  $content .= elgg_view_module('info', "{$count} {$text}", $answers, array("class" => "mtm collapsable"));
}

// show all internal answers
$options = array(
  'type' => 'object',
  'subtype' => 'intanswer',
  'container_guid' => $question->guid,
  'count' => true,
  'limit' => false,
  'pagination' => false,
  'order_by' => 'e.time_created'
);

$intanswers = elgg_list_entities($options);
$count = elgg_get_entities($options);

if ($count > 0) {
  if ($count == 1) {
    $text = elgg_echo('questions:workflow:intanswer');
  } else {
    $text = elgg_echo('questions:workflow:intanswers');
  }
  $content .= elgg_view_module('info', "$count " . $text, $intanswers, array("class" => "mtm"));
}

// add form to open internal question or answer form
if ($question->canWriteToContainer(0, 'object', 'answer')) {
  if ($question->isWorkflowOpen()) {
    if ($question->workflowManager) {
      $form = elgg_view_form('object/intanswer/add', array(), array(
          'container_guid' => $question->guid, 
          'current_phase_guid' => $question->currentPhase
      ));
    } else {
      $form = elgg_view_form('object/question/claim', null, array(
        'question' => $question
      ));
    }
    $content .= elgg_view_module('info', elgg_echo('questions:workflow:addyours', array($question->getCurrentWorkflowPhase()->name)), $form);
  } else {
    $open_workflow = elgg_view("questions/workflow/open", array('question'=>$question));
    $content .= elgg_view_module('info', elgg_echo('questions:workflow:open:title'), $open_workflow);
  }
}