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

$model = \hypeJunction\Post\Model::instance();
/* @var $model \hypeJunction\Post\Model */

if (!isset($vars['context'])) {
	$vars['context'] = \hypeJunction\Fields\Field::CONTEXT_EDIT_FORM;
}

$form_vars = $model->getFormVars($entity, $vars);

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
	'filter_id' => 'post/edit',
	'target' => $entity,
	'class' => 'elgg-layout-post-wrapper',
]);

echo elgg_view_page($entity->getDisplayName(), $layout);
