<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggEntity) {
	return;
}

$modules = \hypeJunction\Post\Post::instance()->getActiveModules($entity, 'footer');

$view = '';
foreach ($modules as $module => $options) {
	if ($options['view']) {
		$view .= elgg_view($options['view'], $vars);
	} else {
		$view .= elgg_view("post/module/$module", $vars);
	}

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
