<?php

/**
 * Object summary
 * Passing an 'icon' with the variables will wrap the listing in an image block. In that case,
 * variables not listed in @uses (e.g. image_alt) will be passed to the image block.
 *
 * @uses $vars['entity']    ElggEntity
 * @uses $vars['title']     Title link (optional) false = no title, '' = default
 * @uses $vars['metadata']  HTML for entity menu and metadata (optional)
 * @uses $vars['subtitle']  HTML for the subtitle (optional)
 * @uses $vars['tags']      HTML for the tags (default is tags on entity, pass false for no tags)
 * @uses $vars['content']   HTML for the entity content (optional)
 * @uses $vars['icon']      Object icon. If set, the listing will be wrapped with an image block
 * @uses $vars['class']     Class selector for the image block
 * @uses $vars['image_block_vars'] Attributes for the image block wrapper
 */
$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggEntity) {
	return;
}

$title = elgg_extract('title', $vars);
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

$title = elgg_view('object/elements/summary/title', $vars);
$subtitle = elgg_view('object/elements/summary/subtitle', $vars);
$extensions = elgg_view('object/summary/extend', $vars);
$content = elgg_view('object/elements/summary/content', $vars);

$summary = $title . $subtitle . $extensions . $content;

$icon = elgg_extract('icon', $vars);
if (!$icon && $icon !== false) {
	$icon = elgg_view('post/cover', array_merge(['ratio' => 60], $vars));
}

$vars['metadata'] = false;
$vars['class'] = 'elgg-listing-widget';

$params = (array) elgg_extract('image_block_vars', $vars, []);

$class = elgg_extract_class($params, ['elgg-listing-has-cover']);
$class = elgg_extract_class($vars, $class);
$params['class'] = $class;
$params['data-guid'] = $entity->guid;

echo elgg_view_image_block($icon, $summary, $params);