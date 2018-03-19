<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggEntity) {
	return;
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
	foreach (['excerpt', 'briefdescription'] as $prop) {
		if ($entity->$prop) {
			$vars['content'] = elgg_get_excerpt($entity->$prop, elgg_extract('content_limit', $vars, 200));
			break;
		}
	}
}

$icon = elgg_extract('icon', $vars);

if (empty($icon) && $icon !== false) {
	if ($entity instanceof ElggUser || $entity instanceof ElggGroup) {
		$vars['icon'] = elgg_view_entity_icon($entity, 'tiny');
	} else {
		if ($owner) {
			$vars['icon'] = elgg_view_entity_icon($owner, 'tiny');
		}
	}
}

$header = elgg_view('post/elements/card/header', $vars);
$media = elgg_view('post/elements/card/media', $vars);
$body = elgg_view('post/elements/card/body', $vars);
$footer = elgg_view('post/elements/card/footer', $vars);

echo elgg_format_element('div', [
	'class' => 'elgg-listing-card card',
	'data-guid' => $entity->guid,
], $media . $header . $body . $footer);
