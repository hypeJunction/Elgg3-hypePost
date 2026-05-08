<?php

namespace hypeJunction\Post;

use Elgg\Event;

/**
 * EntityMenu class.
 */
class EntityMenu {

	/**
	 * @elgg_plugin_hook register menu:social
	 *
	 * @param Hook $hook Plugin hook
	 *
	 * @return \ElggMenuItem[]|null
	 */
	public function __invoke(Event $event) {

		$entity = $event->getEntityParam();
		if (!$entity) {
			return null;
		}

		$menu = $event->getValue();
		/* @var $menu \ElggMenuItem[] */

		if ($entity->canEdit() && $entity->hasIcon('master', 'cover')) {
			$menu->add(\ElggMenuItem::factory([
				'name' => 'delete:cover',
				'icon' => 'minus-circle',
				'text' => elgg_echo('post:cover:delete'),
				'href' => elgg_generate_action_url('cover/delete', [
					'guid' => $entity->guid,
				]),
				'confirm' => true,
			]));
		}

		return $menu;
	}
}
