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

$model = \hypeJunction\Post\Model::instance();
/* @var $model \hypeJunction\Post\Model */

if (!isset($vars['context'])) {
	$vars['context'] = \hypeJunction\Fields\Field::CONTEXT_CREATE_FORM;
}

$form_vars = $model->getFormVars($entity, $vars);

$content = elgg_view_form('post/save', [
	'enctype' => 'multipart/form-data',
	'class' => 'post-form',
	'novalidate' => true,
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
	'class' => 'elgg-layout-post-wrapper',
]);

echo elgg_view_page(null, $layout);
