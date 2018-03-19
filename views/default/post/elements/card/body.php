<?php

$extensions = elgg_view('object/elements/card/extend', $vars);
$content = elgg_view('object/elements/summary/content', $vars);

$byline = elgg_view('object/elements/imprint/byline', $vars);

$icon = elgg_extract('icon', $vars);
$byline = elgg_view_image_block($icon, $byline, [
	'class' => 'elgg-listing-card-byline',
]);

$imprint = elgg_view('object/elements/summary/subtitle', array_merge($vars, [
	'byline' => false,
]));

echo elgg_format_element('div', [
	'class' => 'elgg-listing-card-body',
], $content . $extensions . $byline . $imprint);
