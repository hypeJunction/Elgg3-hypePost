<?php

/**
 * Post layout
 *
 * @uses $vars['class']        Additional CSS classes to apply to the layout
 * @uses $vars['title']        Optional title for main content area
 * @uses $vars['header']       Optional override for the header
 *
 * @uses $vars['content']      Page content

 * @uses $vars['footer']       Optional footer
 *
 * @uses $vars['sidebar']      Sidebar HTML (default: empty string)
 *                             Will not be rendered if the value is 'false'
 * @uses $vars['sidebar_alt']  Second sidebar HTML (default: false)
 *                             Will not be rendered if the value is 'false'
 *
 * @uses $vars['filter']       An optional array of filter tabs
 *                             Array items should be suitable for usage with
 *                             elgg_register_menu_item()
 * @uses $vars['filter_id']    An optional ID of the filter
 *                             If provided, plugins can adjust filter tabs menu
 *                             via 'register, menu:filter:$filter_id' hook
 * @uses $vars['filter_value'] Optional name of the selected filter tab
 *                             If not provided, will be determined by the current page's URL
 */
$class = elgg_extract_class($vars, [
	'elgg-layout',
	'elgg-layout-post',
	'clearfix'
]);

unset($vars['class']);

// Prepare layout sidebar
$vars['sidebar'] = elgg_extract('sidebar', $vars, '');
$sidebar = elgg_view('page/layouts/elements/sidebar', $vars);

// Prepare second layout sidebar
$sidebar_alt = '';
if ($sidebar) {
	$sidebar_alt = elgg_view('page/layouts/elements/sidebar_alt', $vars);
}

if ($sidebar && $sidebar_alt) {
	$class[] = 'elgg-layout-two-sidebar';
} else if ($sidebar) {
	$class[] = 'elgg-layout-one-sidebar';
} else {
	$class[] = 'elgg-layout-one-column';
}

$header = elgg_view('page/layouts/post/header', $vars);
$filter = elgg_view('page/layouts/elements/filter', $vars);
$content = elgg_view('page/layouts/elements/content', $vars);
$footer = elgg_view('page/layouts/elements/footer', $vars);

$body = elgg_format_element('div', [
	'class' => 'elgg-main elgg-body elgg-layout-body clearfix',
], $filter . $content);

$layout = $header;

$layout .= elgg_format_element('div', [
	'class' => 'elgg-layout-columns',
], $sidebar_alt . $body . $sidebar);

$layout .= $footer;

echo elgg_format_element('div', [
	'class' => $class,
], $layout);