<?php

$title = elgg_extract('title', $vars);
$body = elgg_extract('body', $vars);
$collapsed = elgg_extract('collapsed', $vars);
$class = elgg_extract_class($vars);

$menu = elgg_view('output/url', [
	'text' => '',
	'icon' => elgg_view_icon('angle-right'),
	'href' => '#',
	'class' => 'elgg-action-expand',
]);

$menu .= elgg_view('output/url', [
	'text' => '',
	'icon' => elgg_view_icon('angle-down'),
	'href' => '#',
	'class' => 'elgg-action-collapse',
]);

$class[] = $collapsed ? 'elgg-state-collapsed' : 'elgg-state-expanded';

echo elgg_view_module('collapse', $title, $body, [
	'menu' => $menu,
	'class' => $class,
]);

elgg_require_js('post/module');
