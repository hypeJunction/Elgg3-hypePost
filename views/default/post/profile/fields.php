<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggEntity) {
	return;
}

$fields = elgg()->{'posts.model'}->getProfileFields($entity, $vars);

$output = '';

foreach ($fields as $field) {
	$output .= elgg_view("post/output/field", $field);
}

if (empty($output)) {
	return;
}

echo elgg_view('post/module', [
	'title' => elgg_echo('post:profile:fields'),
	'body' => $output,
	'collapsed' => false,
	'class' => 'post-profile-fields',
]);
