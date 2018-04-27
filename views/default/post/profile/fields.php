<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggEntity) {
	return;
}

$model = \hypeJunction\Post\Model::instance();
/* @var $model \hypeJunction\Post\Model */

$vars['context'] = \hypeJunction\Fields\Field::CONTEXT_PROFILE;

$fields = $model->getFields($entity, $vars);

$output = '';
foreach ($fields as $field) {
	/* @var $field \hypeJunction\Fields\FieldInterface */
	$output .= $field->output($entity);
}

if (empty($output)) {
	return;
}

echo elgg_view('post/module', [
	'title' => elgg_echo('post:profile:fields'),
	'body' => $output,
	'collapsed' => false,
	'class' => 'post-profile-fields',
]);
