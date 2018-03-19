<?php

namespace hypeJunction\Post;

use Elgg\Hook;

class EntityMenu {

	/**
	 * @elgg_plugin_hook register menu:social
	 *
	 * @param Hook $hook
	 *
	 * @return \ElggMenuItem[]|null
	 */
	public function __invoke(Hook $hook) {

		$entity = $hook->getEntityParam();
		if (!$entity) {
			return null;
		}

		$menu = $hook->getValue();
		/* @var $menu \ElggMenuItem[] */

		if ($entity->canEdit() && $entity->hasIcon('master', 'cover')) {
			$menu[] = \ElggMenuItem::factory([
				'name' => 'delete:cover',
				'icon' => 'minus-circle',
				'text' => elgg_echo('post:cover:delete'),
				'href' => elgg_generate_action_url('cover/delete', [
					'guid' => $entity->guid,
				]),
				'confirm' => true,
			]);
		}

		return $menu;
	}
}
