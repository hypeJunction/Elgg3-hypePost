<?php

$cover = elgg_extract('cover', $vars);

if (!$cover instanceof \hypeJunction\Post\CoverWrapper) {
	return;
}

?>
<div class="post-cover-attribution">
	<?php
	if ($cover->author || $cover->author_url) {
		$author_link = elgg_view('output/url', [
			'text' => $cover->author ? : elgg_echo('post:cover:unknown'),
			'href' => $cover->author_url ? : '#',
			'class' => 'cover-author',
		]);
		echo elgg_echo('post:cover:author', [$author_link]);
	}

	if ($cover->provider || $cover->provider_url) {
		echo elgg_view('output/url', [
			'text' => $cover->provider ? : elgg_echo('post:cover:source'),
			'href' => $cover->provider_url ? : '#',
			'class' => 'cover-provider',
		]);
	}
	?>
</div>