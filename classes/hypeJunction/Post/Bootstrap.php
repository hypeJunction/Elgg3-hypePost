<?php

namespace hypeJunction\Post;

use Elgg\PluginBootstrap;
use hypeJunction\Post\DefineCoverSizes;
use hypeJunction\Post\EntityMenu;
use hypeJunction\Post\SaveEditHistory;
use hypeJunction\Post\SetObjectFields;
use hypeJunction\Post\SocialMenu;

class Bootstrap extends PluginBootstrap {

	/**
	 * {@inheritdoc}
	 */
	public function load() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function boot() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function init() {
		elgg_extend_view('elgg.css', 'post/styles.css');
		elgg_extend_view('elements/forms.css', 'forms/validation.css');
		elgg_extend_view('elements/forms.css', 'input/range.css');

		elgg_register_action('post/save', \hypeJunction\Post\SavePostAction::class);

		elgg_register_plugin_hook_handler('fields', 'object', AddProfileModulesField::class);
		elgg_register_plugin_hook_handler('fields', 'group', AddProfileModulesField::class);
		elgg_register_plugin_hook_handler('fields', 'user', AddProfileModulesField::class);

		elgg_register_plugin_hook_handler('fields', 'object', SetObjectFields::class);

		elgg_register_event_handler('update', 'object', SaveEditHistory::class);

		elgg_register_plugin_hook_handler('entity:cover:sizes', 'all', DefineCoverSizes::class);

		elgg_register_plugin_hook_handler('register', 'menu:social', SocialMenu::class);
		elgg_register_plugin_hook_handler('register', 'menu:entity', EntityMenu::class);

		elgg_register_plugin_hook_handler('adapter:entity', 'all', PopulateExportData::class);
	}

	/**
	 * {@inheritdoc}
	 */
	public function ready() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function shutdown() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function activate() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function deactivate() {

	}

	/**
	 * {@inheritdoc}
	 */
	public function upgrade() {

	}
}