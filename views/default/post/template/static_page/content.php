<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggEntity) {
	return;
}

echo elgg_view('page/layouts/elements/header', [
	'title' => $entity->getDisplayName(),
]);

echo elgg_view('post/elements/full', [
	'entity' => $entity,
	'summary' => false,
	'icon' => false,
	'header_params' => [
		'class' => 'hidden',
	],
]);
