<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggEntity) {
	return;
}

$modules = elgg()->{'posts.post'}->getModules($entity);

$view = '';
foreach ($modules as $module => $options) {
	if (!$options['enabled'] || $options['position'] !== 'footer') {
		continue;
	}

	$view .= elgg_view("post/module/$module", $vars);
}

if (!$view) {
	return;
}

echo elgg_format_element('div', [
	'class' => [
		'post-modules',
		'post-position-footer',
	],
], $view);
