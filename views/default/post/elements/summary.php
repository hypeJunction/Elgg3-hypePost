<?php

$entity = elgg_extract('entity', $vars);
$full_view = elgg_extract('full_view', $vars);

if (!$entity instanceof \ElggEntity) {
	return;
}

foreach ($vars as $key => $value) {
	if (is_callable($value)) {
		$vars[$key] = call_user_func($value, $entity, $full_view);
	}
}

if ($entity instanceof ElggObject) {
	$owner = $entity->getOwnerEntity();
	if ($entity->owner_guid && !$owner) {
		return;
	}

	$container = $entity->getContainerEntity();
	if ($entity->container_guid && !$container) {
		return;
	}
}

$title = elgg_extract('title', $vars);
if (empty($title) && $title !== false) {
	$title = elgg_get_excerpt($entity->getDisplayName(), 100);
	$vars['title'] = elgg_view('output/url', [
		'text' => $title,
		'href' => $entity->getURL(),
	]);
}

$content = elgg_extract('content', $vars);
if (empty($content) && $content !== false) {
	foreach (['excerpt', 'briefdescription', 'description'] as $prop) {
		if ($entity->$prop) {
			$vars['content'] = elgg_get_excerpt($entity->$prop, elgg_extract('content_limit', $vars, 200));
			break;
		}
	}
}

$icon = elgg_extract('icon', $vars);
if (!$icon && $icon !== false) {
	$vars['icon'] = elgg_view('post/cover', array_merge(['ratio' => 60], $vars));
}

$vars['class'] = elgg_extract_class($vars, 'elgg-listing-has-cover');

echo elgg_view('object/elements/summary', $vars);
