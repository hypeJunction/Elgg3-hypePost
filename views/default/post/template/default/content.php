<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggEntity) {
	return;
}

echo elgg_view_entity($entity, array_merge($vars, [
	'full_view' => true,
	'show_navigation' => elgg_trigger_event('uses:navigation', "$entity->type:$entity->subtype", $vars),
]));
