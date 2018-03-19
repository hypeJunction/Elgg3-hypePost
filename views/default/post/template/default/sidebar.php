<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggEntity) {
	return;
}

//$output = elgg_view("post/profile/author", $vars);
$output .= elgg_view("post/profile/fields", $vars);

$modules = elgg()->{'posts.post'}->getModules($entity);

foreach ($modules as $module => $options) {
	if (!$options['enabled'] || $options['position'] !== 'sidebar') {
		continue;
	}

	$output .= elgg_view("post/module/$module", $vars);
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

if (elgg_trigger_plugin_hook('uses:widgets', "$entity->type:$entity->subtype", $vars, false)) {

	elgg_push_context('post');

	echo elgg_view_layout('widgets', [
		'owner_guid' => $entity->guid,
		'num_columns' => 1,
	]);

	elgg_pop_context();

}