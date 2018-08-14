<?php

$name = elgg_extract('name', $vars, 'range');

$min = elgg_extract('min', $vars, 0);
$max = elgg_extract('max', $vars, 100);

$value = elgg_extract('value', $vars, [$min, $max]);

$lower_bound = elgg_view('input/hidden', [
	'name' => "{$name}[lower]",
	'value' => elgg_extract(0, $value),
	'class' => 'elgg-input-range__lower-bound',
]);

$upper_bound = elgg_view('input/hidden', [
	'name' => "{$name}[upper]",
	'value' => elgg_extract(1, $value),
	'class' => 'elgg-input-range__upper-bound',
]);

$range = elgg_format_element('div', [
	'class' => 'elgg-input-range__sliders',
	'data-options' => json_encode([
		'range' => true,
		'min' => $min,
		'max' => $max,
		'values' => is_array($value) && !empty($value) ? $value : null,
	]),
]);

$step = ($max - $min) / 4;
$steps = range($min, $max, $step);

$axis = '';
foreach ($steps as $step) {
	$axis .= elgg_format_element('span', [], $step);
}

$axis = elgg_format_element('div', [
	'class' => 'elgg-input-range__slider-axis',
], $axis);

echo elgg_format_element('div', [
	'class' => 'elgg-input-range__js-container',
], $lower_bound . $upper_bound . $range . $axis);

?>
<script>
	require(['input/range']);
</script>
