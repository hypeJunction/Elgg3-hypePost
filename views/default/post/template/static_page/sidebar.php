<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggEntity) {
	return;
}

$modules = \hypeJunction\Post\Post::instance()->getModules($entity, 'sidebar');

$view = '';
foreach ($modules as $module => $options) {
	$view .= elgg_view("post/module/$module", $vars);
}

if (!$view) {
	return;
}

echo elgg_format_element('div', [
	'class' => [
		'post-modules',
		'post-position-sidebar',
	],
], $view);
