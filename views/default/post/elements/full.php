<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggEntity) {
	return;
}

$summary = elgg_extract('summary', $vars);
if (empty($summary) && $summary !== false) {
	$summary = elgg_view('post/elements/summary', [
		'entity' => $entity,
		'icon' => false,
		'content' => false,
		'title' => false,
	]);

	$vars['summary'] = $summary;
}

$icon = elgg_extract('icon', $vars);
if (empty($icon) && $icon !== false) {
	if ($entity instanceof ElggUser || $entity instanceof ElggGroup) {
		$vars['icon'] = elgg_view_entity_icon($entity, 'small');
	} else {
		$owner = $entity->getOwnerEntity();
		if ($owner) {
			$vars['icon'] = elgg_view_entity_icon($owner, 'small');
		}
	}
}

$body = elgg_extract('body', $vars);
if (empty($body) && $body !== false) {
	$vars['body'] = elgg_view('output/longtext', [
		'value' => $entity->description,
	]);
}

$attachments = elgg_extract('attachments', $vars, '');

$modules = \hypeJunction\Post\Post::instance()->getModules($entity, 'content');

$view = '';
foreach ($modules as $module => $options) {
	$attachments .= elgg_view("post/module/$module", $vars);
}

$vars['attachments'] = $attachments;

$responses = elgg_extract('responses', $vars);
if (empty($responses) && $responses !== false && \hypeJunction\Post\Post::instance()->hasCommentBlock($entity)) {
	$vars['responses'] = elgg_view_comments($entity, null, $vars);
}

if (elgg_extract('cover', $vars) !== false) {
	echo elgg_view('post/cover', [
		'cover' => \hypeJunction\Post\Post::instance()->getCover($entity),
	]);
}

echo elgg_view('object/elements/full', $vars);
