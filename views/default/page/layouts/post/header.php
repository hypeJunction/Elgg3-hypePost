<?php

/**
 * Header for layouts
 *
 * @uses $vars['title']  Title
 * @uses $vars['header'] Optional override for the header
 */
$header = elgg_extract('header', $vars);
unset($vars['header']);

if (!isset($header)) {
	$title = elgg_extract('title', $vars, '');
	unset($vars['title']);

	if ($title) {
		$title = elgg_view_title($title, [
			'class' => 'elgg-heading-main',
		]);
	}

	$buttons = '';

	if (elgg_extract('title_menu', $vars) !== false) {
		$menu_params = $vars;
		$menu_params['sort_by'] = 'priority';
		$menu_params['class'] = 'elgg-menu-hz';
		$buttons = elgg_view_menu('form:actions', $menu_params);
	}

	$header = $title . $buttons;
}

if (!$header) {
	return;
}
?>
<div class="elgg-head elgg-layout-header">
	<?= $header ?>
</div>
