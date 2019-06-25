<?php

$value = elgg_extract('value', $vars);
$options = [];

foreach ($value as $name => $module) {
	$label = elgg_extract('label', $vars, elgg_echo("post:module:$name"));
	$options[$label] = $name;
}

$vars['value'] = array_keys(array_filter($value));
$vars['options'] = $options;

echo elgg_view('input/checkboxes', $vars);

