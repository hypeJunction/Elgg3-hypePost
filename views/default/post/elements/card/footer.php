<?php

$entity = elgg_extract('entity', $vars);

$social = elgg_extract('social', $vars);
if (empty($social) && $social !== false) {
	$social = elgg_view_menu('social', [
		'entity' => $entity,
		'class' => 'elgg-menu-hz'
	]);
}

if ($social) {
	echo elgg_format_element('div', [
		'class' => 'elgg-listing-card-footer',
	], $social);
}