<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggEntity) {
	return;
}

$svc = \hypeJunction\Post\Post::instance();
/* @var $svc \hypeJunction\Post\Post */

$modules = $svc->getModules($entity, 'sidebar');

foreach ($modules as $module => $options) {
	if ($options['position'] !== 'sidebar') {
		continue;
	}

	$output .= elgg_view("post/module/$module", $vars);
}


if (elgg_trigger_plugin_hook('uses:widgets', "$entity->type:$entity->subtype", $vars, false)) {

	elgg_push_context('post');

	$output .= elgg_view_layout('widgets', [
		'owner_guid' => $entity->guid,
		'num_columns' => 1,
	]);

	elgg_pop_context();

}

$output .= elgg_view("post/profile/menu", $vars);

if ($output) {
	echo elgg_format_element('div', [
		'class' => [
			'post-modules',
			'post-position-sidebar',
		],
	], $output);
}