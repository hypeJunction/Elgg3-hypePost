<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggEntity) {
	return;
}

$media = elgg_extract('media', $vars);
if (empty($media) && $media !== false) {
	$cover = elgg()->{'posts.post'}->getCover($entity, true);
	$media = elgg_view('post/cover', [
		'cover' => $cover,
		'ratio' => 50,
	]);
}

if ($media) {
	echo elgg_format_element('div', [
			'class' => 'elgg-listing-card-media',
	], $media);
}
