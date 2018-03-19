<?php

$cover = elgg_extract('cover', $vars);

if (!isset($cover)) {
	$entity = elgg_extract('entity', $vars);
	if ($entity) {
		$fallback = elgg_extract('fallback', $vars, true);
		$cover = elgg()->{'posts.post'}->getCover($entity, $fallback);
	}
}

if (!$cover instanceof \hypeJunction\Post\CoverWrapper) {
	return;
}

$url = $cover->getCoverUrl();
if (!$url) {
	return;
}

$ratio = elgg_extract('ratio', $vars, $cover->ratio ? : 50);
$gravity = $cover->gravity ? : 'center';

$positions = [
	'center' => '50% 50%',
	'north' => '50% 0',
	'east' => '100% 50%',
	'south' => '50% 100%',
	'west' => '0% 50%',
];

echo elgg_format_element('div', [
	'class' => [
		'post-cover',
		'post-cover-image',
	],
	'style' => [
		"background-image: url({$url});",
		"padding-bottom: {$ratio}%;",
		"background-position: {$positions[$gravity]};",
	],
], elgg_view('post/cover_attribution', $vars));
