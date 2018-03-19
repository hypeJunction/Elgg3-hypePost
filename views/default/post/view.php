<?php

$entity = elgg_extract('entity', $vars);
$full = elgg_extract('full_view', $vars, false);
$is_gallery = elgg_in_context('gallery');
$is_widget = elgg_in_context('widgets');

if ($full) {
	echo elgg_view('post/elements/full', $vars);
} else if ($is_widget) {
	echo elgg_view('post/elements/widget', $vars);
} else if ($is_gallery) {
	echo elgg_view('post/elements/card', $vars);
} else {
	echo elgg_view('post/elements/summary', $vars);
}
