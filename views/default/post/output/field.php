<?php

$entity = elgg_extract('entity', $vars);
$label = elgg_extract('#label', $vars);
$type = elgg_extract('#type', $vars);
$name = elgg_extract('name', $vars);
$value = elgg_extract('value', $vars);

if (!$value) {
	return;
}

$prepare_value = function ($value, $params) {
	switch ($params['#type']) {
		case 'select' :
			return $params['options_values'][$value];

		case 'checkboxes' :
		case 'radio' :
			return array_search($value, $params['options']);

		case 'checkbox' :
			return (bool) $params['checked'] ? elgg_echo('option:yes') : elgg_echo('option:no');
	}

	return $value;
};

if (is_array($vars['value'])) {
	$vars['value'] = array_map(function ($value) use ($vars, $prepare_value) {
		return $prepare_value($value, $vars);
	}, $vars['value']);
} else {
	$vars['value'] = $prepare_value($value, $vars);
}

if (!elgg_view_exists("output/$type")) {
	$vars['value'] = implode(', ', $vars['value']);
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
