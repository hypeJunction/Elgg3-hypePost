<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggEntity) {
	return;
}

$items = elgg()->menus->getUnpreparedMenu('entity', [
	'entity' => $entity,
]);

$menu = elgg_view_menu('post', [
	'entity' => $entity,
	'items' => $items->getItems(),
	'class' => 'elgg-menu-hover',
]);

if (empty($menu)) {
	return;
}

echo elgg_view('post/module', [
	'title' => elgg_echo('post:profile:menu'),
	'body' => $menu,
	'collapsed' => false,
	'class' => 'post-profile-menu has-list',
]);
