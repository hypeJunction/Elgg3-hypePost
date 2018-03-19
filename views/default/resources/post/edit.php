<?php

$guid = elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid);

$entity = get_entity($guid);
if (!$entity instanceof \ElggEntity) {
	throw new \Elgg\BadRequestException();
}

if (!$entity->canEdit()) {
	throw new \Elgg\EntityPermissionsException();
}

elgg_push_entity_breadcrumbs($entity);
elgg_push_breadcrumb($title);

$content = elgg_view_form('post/save', [
	'enctype' => 'multipart/form-data',
	'class' => 'post-form',
], elgg()->{'posts.model'}->getFormVars($entity, $vars));

if (elgg_is_xhr()) {
	echo $content;
	return;
}

$layout = elgg_view_layout('default', [
	'header' => false,
	'content' => $content,
	'sidebar' => false,
	'filter' => 'post/edit',
	'target' => $entity,
]);

echo elgg_view_page($entity->getDisplayName(), $layout);
