<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggEntity) {
	return;
}

echo elgg_view('post/elements/full', [
	'entity' => $entity,
	'summary' => false,
	'body' => $body,
	'icon' => false,
	'header_params' => [
		'class' => 'hidden',
	],
]);
