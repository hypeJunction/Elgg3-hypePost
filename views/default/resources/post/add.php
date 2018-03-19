<?php

$type = elgg_extract('type', $vars, 'object');
$subtype = elgg_extract('subtype', $vars);

$class = elgg_get_entity_class($type, $subtype);
if (!$class) {
	throw new \Elgg\BadRequestException();
}

$container_guid = elgg_extract('guid', $vars);
elgg_entity_gatekeeper($container_guid);

$container = get_entity($container_guid);
if (!$container || !$container->canWriteToContainer(0, $type, $subtype)) {
	throw new \Elgg\EntityPermissionsException();
}

$entity = new $class();
if (!$entity instanceof \ElggEntity) {
	throw new \Elgg\BadRequestException();
}

$entity->container_guid = $container->guid;

elgg_push_collection_breadcrumbs($type, $subtype, $container);

$form_vars = elgg()->{'posts.model'}->getFormVars($entity, $vars);

$content = elgg_view_form('post/save', [
	'enctype' => 'multipart/form-data',
	'class' => 'post-form',
], $form_vars);

if (elgg_is_xhr()) {
	echo $content;
	return;
}

$layout = elgg_view_layout('default', [
	'header' => false,
	'content' => $content,
	'sidebar' => false,
	'filter' => 'post/edit',
]);

echo elgg_view_page(null, $layout);
