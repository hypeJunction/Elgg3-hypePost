<?php

$entity = elgg_extract('entity', $vars);
$fields = elgg_extract('fields', $vars, []);
$context = elgg_extract('context', $vars);

$view_fields = function (\hypeJunction\Fields\Collection $fields) use ($entity, $context) {
	$output = '';
	foreach ($fields as $field) {
	    /* @var $field \hypeJunction\Fields\FieldInterface */
	    if (!$context) {
			$context = $entity->guid ? \hypeJunction\Fields\Field::CONTEXT_EDIT_FORM : \hypeJunction\Fields\Field::CONTEXT_CREATE_FORM;
		}
		$output .= $field->render($entity, $context);
	}

	return $output;
};

$filter = function (\hypeJunction\Fields\Collection $fields, $section) {
	return $fields->filter(function (\hypeJunction\Fields\FieldInterface $field) use ($section) {
		return $field->section == $section;
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

$layout_content .= elgg_view("forms/edit/$entity->type/$entity->subtype", $vars);

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
    /* @var $action \hypeJunction\Fields\FieldInterface */

    $menu_item = [
		'name' => $action->name,
		'href' => false,
		'text' => $action->render($entity),
		'priority' => $action->priority,
	];

	elgg_register_menu_item('form:actions', $menu_item);
	elgg_register_menu_item('title', $menu_item);
}

$type = elgg_echo("item:$entity->type:$entity->subtype");

$layout_footer = elgg_view('post/elements/form_footer', $vars);
$layout_footer .= elgg_view_menu('form:actions', [
	'class' => 'elgg-menu-hz',
]);

$layout_footer = elgg_format_element('div', [
	'class' => 'elgg-form-footer',
], $layout_footer);

$title = elgg_extract('form_title', $vars);
if (!$title) {
	$title = $entity->guid ? elgg_echo('post:edit', [$type]) : elgg_echo('post:add', [$type]);
}

echo elgg_view_layout('post', array_merge($vars, [
	'title' => $title,
	'content' => $layout_content,
	'sidebar' => $sidebar ? : false,
	'footer' => $layout_footer,
	'filter_id' => "edit:$entity->type:$entity->subtype",
	'filter_value' => 'default',
]));

?>
<script>
	require(['forms/post/save']);
</script>
