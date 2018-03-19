<?php

$entity = elgg_extract('entity', $vars);
$fields = elgg_extract('fields', $vars, []);

$view_fields = function (array $fields) {
	$output = '';
	foreach ($fields as $field) {
		$output .= elgg_view_field($field);
	}

	return $output;
};

$filter = function (array $fields, $section) {
	return array_filter($fields, function ($field) use ($section) {
		return elgg_extract('#section', $field, 'content') === $section;
	});
};

$layout_content = '';
$header = $view_fields($filter($fields, 'header'));
if ($header) {
	$layout_content .= elgg_format_element('div', [
		'class' => 'elgg-grid post-form-header elgg-fields',
	], $header);
}

$content = $view_fields($filter($fields, 'content'));
if ($content) {
	$layout_content .= elgg_format_element('div', [
		'class' => 'elgg-grid post-form-main elgg-fields',
	], $content);
}

$footer = $view_fields($filter($fields, 'footer'));
if ($footer) {
	$layout_content .= elgg_format_element('div', [
		'class' => 'elgg-grid elgg-fields',
	], $footer);
}

$sidebar = $view_fields($filter($fields, 'sidebar'));
if ($sidebar) {
	$sidebar = elgg_format_element('div', [
		'class' => 'elgg-grid post-form-main elgg-fields',
	], $sidebar);
} else {
	$sidebar = false;
}

$actions = $filter($fields, 'actions');
foreach ($actions as $action) {
	elgg_register_menu_item('form:actions', [
		'name' => elgg_extract('name', $action),
		'href' => false,
		'text' => elgg_view_field($action),
		'priority' => elgg_extract('#priority', $action),
	]);

	elgg_register_menu_item('title', [
		'name' => elgg_extract('name', $action),
		'href' => false,
		'text' => elgg_view_field($action),
		'priority' => elgg_extract('#priority', $action),
	]);
}

$type = elgg_echo("item:$entity->type:$entity->subtype");

$layout_footer = elgg_view('post/elements/form_footer', $vars);
$layout_footer .= elgg_view_menu('form:actions', [
	'class' => 'elgg-menu-hz',
]);

$layout_footer = elgg_format_element('div', [
	'class' => 'elgg-form-footer',
], $layout_footer);

echo elgg_view_layout('post', [
	'title' => $entity->guid ? elgg_echo('post:edit', [$type]) : elgg_echo('post:add', [$type]),
	'content' => $layout_content,
	'sidebar' => $sidebar ? : false,
	'footer' => $layout_footer,
	'filter_id' => "edit:$entity->type:$entity->subtype",
	'filter_value' => 'default',
]);

?>
<script>
	require(['forms/post/save']);
</script>
