<?php

namespace hypeJunction\Post;

use Elgg\Hook;
use ElggMenuItem;

class SocialMenu {

	/**
	 * @elgg_plugin_hook register menu:social
	 *
	 * @param Hook $hook
	 *
	 * @return ElggMenuItem[]|null
	 */
	public function __invoke(Hook $hook) {

		$entity = $hook->getEntityParam();
		if (!$entity) {
			return null;
		}

		$menu = $hook->getValue();
		/* @var $menu ElggMenuItem[] */

		$svc = elgg()->{'posts.post'};
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
