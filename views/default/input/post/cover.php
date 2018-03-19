<?php

$name = elgg_extract('name', $vars);
$value = (array) elgg_extract('value', $vars, []);

$fields = [
	[
		'#label' => elgg_echo('field:cover:file'),
		'#type' => 'file',
		'name' => "{$name}[file]",
		'value' => $value instanceof \hypeJunction\Post\CoverWrapper && $value->getCoverUrl(),
	],
];

$fields[] = [
	'#label' => elgg_echo('field:cover:url'),
	'#type' => 'url',
	'name' => "{$name}[url]",
];

echo elgg_view_field([
	'#type' => 'fieldset',
	'fields' => $fields,
]);
