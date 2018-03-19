<?php

require_once __DIR__ . '/autoloader.php';

return function () {

	elgg_register_event_handler('init', 'system', function () {

		elgg_extend_view('elgg.css', 'post/styles.css');

		elgg_register_action('post/save', \hypeJunction\Post\SavePostAction::class);

		elgg_register_event_handler('create', 'object', \hypeJunction\Post\CreateRiverItem::class);
		elgg_register_event_handler('update', 'object', \hypeJunction\Post\SaveEditHistory::class);

		elgg_register_plugin_hook_handler('entity:cover:sizes', 'all', \hypeJunction\Post\DefineCoverSizes::class);

		elgg_register_plugin_hook_handler('register', 'menu:social', \hypeJunction\Post\SocialMenu::class);
		elgg_register_plugin_hook_handler('register', 'menu:entity', \hypeJunction\Post\EntityMenu::class);
	});

};
