<?php

$entity = elgg_extract('entity', $vars);

$metadata = elgg_extract('metadata', $vars);
if (empty($metadata) && $metadata !== false) {
	$metadata = elgg_view_menu('entity', [
		'entity' => $entity,
	]);
}

$title = elgg_view('object/elements/summary/title', $vars);

echo elgg_format_element('div', [
	'class' => 'elgg-listing-card-header',
], $metadata . $title);
