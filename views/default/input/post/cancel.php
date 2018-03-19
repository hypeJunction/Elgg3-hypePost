<?php

$href = _elgg_services()->request->headers->get('Referer');

if (!$href) {
	$href = elgg()->session->last_forward_from;
}

if (!$href) {
	$href = elgg_get_site_url();
}

echo elgg_format_element('button', [
	'data-href' => $href,
	'type' => 'button',
	'class' => 'elgg-button elgg-button-cancel post-button-cancel',
], elgg_echo('cancel'));

elgg_require_js('input/post/cancel');