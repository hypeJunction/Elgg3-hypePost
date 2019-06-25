<?php

$value = elgg_extract('value', $vars);
$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggEntity) {
	return;
}

$modules = \hypeJunction\Post\Post::instance()->getModules($entity);

$options = [];

foreach ($modules as $name => $module) {
	$label = elgg_extract('label', $module, elgg_echo("post:module:$name"));
	$options[$label] = $name;
}

$vars['value'] = array_keys(array_filter($value));
$vars['options'] = $options;
$vars['switch'] = true;

echo elgg_view('input/checkboxes', $vars);

