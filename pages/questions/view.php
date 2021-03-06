<?php
/**
 * View a question page
 *
 * @package ElggQuestions
 */

$guid = (int) get_input('guid');
$question = get_entity($guid);

// make sure we have access
if (empty($question)) {
  register_error(elgg_echo('noaccess'));
  $_SESSION['last_forward_from'] = current_page_url();
  forward('');
}

// make sure we have a question
if (!elgg_instanceof($question, "object", "question")) {
	register_error(elgg_echo("ClassException:ClassnameNotClass", array($guid, elgg_echo("item:object:question"))));
	forward(REFERER);
}

// set page owner
elgg_set_page_owner_guid($question->getContainerGUID());
$page_owner = $question->getContainerEntity();

// set breadcrumb
if ($workflow == true) {
  elgg_push_breadcrumb(elgg_echo("questions:workflow"), "questions/workflow");
}

if (elgg_instanceof($page_owner, 'group')) {
  $base_url = "questions/group/$page_owner->guid/";

  if ($workflow == true) {
    $url = $base_url . "workflow";
  } else {
    $url = $base_url . "all";
  }

  elgg_push_breadcrumb($page_owner->name, $url);
}

if ($workflow == true) {
  include("view/workflow.php");
} else {
  include("view/frontend.php");
}

$content = $overview . $content;

$body = elgg_view_layout('content', array(
  'title' => $title_icon . $title,
  'content' => $content,
  'filter' => '',
));

echo elgg_view_page($title, $body);
