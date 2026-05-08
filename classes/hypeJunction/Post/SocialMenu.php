<?php

namespace hypeJunction\Post;

use Elgg\Event;
use ElggMenuItem;

/**
 * SocialMenu class.
 */
class SocialMenu {

	/**
	 * @elgg_plugin_hook register menu:social
	 *
	 * @param Hook $hook Plugin hook
	 *
	 * @return ElggMenuItem[]|null
	 */
	public function __invoke(Event $event) {

		$entity = $event->getEntityParam();
		if (!$entity) {
			return null;
		}

		$menu = $event->getValue();
		/* @var $menu ElggMenuItem[] */

		$svc = \hypeJunction\Post\Post::instance();
		/* @var $svc \hypeJunction\Post\Post */

		if (!$svc->hasCommentBlock($entity)) {
			foreach ($menu as $key => $item) {
				if ($item instanceof ElggMenuItem && $item->getName() === 'comment') {
					unset($menu[$key]);
				}
			}
		}

		return $menu;
	}
}
