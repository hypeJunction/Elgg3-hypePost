<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof \ElggEntity) {
	return;
}

$template = elgg()->{'posts.post'}->getTemplate($entity);

if (elgg_view_exists("post/template/$template/content")) {
	$content = elgg_view("post/template/$template/content", $vars);
} else {
	$content = elgg_view_entity($entity, [
		'full_view' => true,
		'show_responses' => true,
	]);
}

if (elgg_view_exists("post/template/$template/footer")) {
	$content .= elgg_view("post/template/$template/footer", $vars);
}

if (elgg_view_exists("post/template/$template/header")) {
	$title = null;
	$header .= elgg_view("post/template/$template/header", $vars);
} else {
	$header = null;
	$title = $entity->getDisplayName();
}

if (elgg_view_exists("post/template/$template/sidebar")) {
	$sidebar = elgg_view("post/template/$template/sidebar", $vars);
} else {
	$sidebar = false;
}

echo elgg_view_layout('post', [
	'header' => $header,
	'title' => $title,
	'content' => $content,
	'sidebar' => $sidebar,
	'filter_id' => "view:$entity->type:$entity->subtype",
	'filter_value' => 'default',
]);

elgg()->{'posts.post'}->setPageMetatags($entity);
elgg()->{'posts.post'}->logView($entity);
