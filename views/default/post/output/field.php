<?php

$entity = elgg_extract('entity', $vars);
$label = elgg_extract('#label', $vars);
$type = elgg_extract('#type', $vars);
$name = elgg_extract('name', $vars);
$value = elgg_extract('value', $vars);

if (!$value) {
	return;
}

if (!elgg_view_exists("output/$type")) {
	if (is_array($vars['value'])) {
		$vars['value'] = implode(', ', $vars['value']);
	}
	$value = elgg_view('output/text', $vars);
} else {
	$value = elgg_view("output/$type", $vars);
}

if (!$value) {
	return;
}

if ($label) {
	$label = elgg_format_element('div', [
		'class' => 'post-field-label',
	], $label);
}

$value = elgg_format_element('div', [
	'class' => 'post-field-value',
], $value);

echo elgg_format_element('div', [
	'class' => 'post-field-output',
	'data-guid' => $entity->guid,
	'data-name' => $name,
	'data-type' => $type,
], $label . $value);
