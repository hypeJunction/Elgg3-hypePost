<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggEntity) {
	return;
}

$owner = $entity->getOwnerEntity();
if (!$owner) {
	return;
}

if ($owner->description) {
	$value = $owner->description;
} else {
	$annotations = $owner->getAnnotations([
		'annotation_names' => "profile:description",
		'limit' => false,
	]);

	$values = array_map(function (ElggAnnotation $a) {
		return $a->value;
	}, $annotations);

	$value = array_shift($values);
}

$description = elgg_view('output/longtext', [
	'value' => $value,
]);

$user_hover = elgg()->menus->getMenu('user_hover', [
	'entity' => $owner,
]);

$actions = $user_hover->getSection('action', []);

$menu = elgg_view_menu('post:author', [
	'entity' => $owner,
	'items' => $actions,
]);

$output = elgg_view('object/elements/summary', [
	'entity' => $owner,
	'tags' => false,
	'subtitle' => false,
	'content' => $description . $menu,
	'icon' => elgg_view_entity_icon($owner, 'medium'),
]);


if (empty($output)) {
	return;
}

echo elgg_view('post/module', [
	'title' => elgg_echo('post:profile:author'),
	'body' => $output,
	'collapsed' => false,
	'class' => 'post-profile-author',
]);
