<?php

$guid = elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid);

$entity = get_entity($guid);
if (!$entity instanceof \ElggEntity) {
	throw new \Elgg\BadRequestException();
}

elgg_register_title_button(null, 'add', $entity->type, $entity->subtype);

elgg_push_entity_breadcrumbs($entity, false);

$content = elgg_view('post/layout', [
	'entity' => $entity,
]);

$layout = elgg_view_layout('default', [
	'header' => false,
	'content' => $content,
	'filter' => false,
	'sidebar' => false,
	'entity' => $entity,
	'class' => 'elgg-layout-post-view',
]);

$shell = $entity->page_shell ? : 'default';

echo elgg_view_page(null, $layout, $shell, [
	'entity' => $entity,
	//'header' => false,
]);
