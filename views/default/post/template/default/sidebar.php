<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggEntity) {
	return;
}

$modules = \hypeJunction\Post\Post::instance()->getActiveModules($entity, 'sidebar');

foreach ($modules as $module => $options) {
	if ($options['view']) {
		$output .= elgg_view($options['view'], $vars);
	} else {
		$output .= elgg_view("post/module/$module", $vars);
	}

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