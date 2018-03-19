<?php

namespace hypeJunction\Post;

use Elgg\Event;
use Exception;

class SaveEditHistory {

	/**
	 * Add post to river
	 *
	 * @elgg_event update object
	 *
	 * @param Event $event Event
	 *
	 * @throws Exception
	 */
	public function __invoke(Event $event) {
		$entity = $event->getObject();
		if (!$entity instanceof \ElggEntity) {
			return;
		}

		elgg()->{'posts.post'}->logHistory($entity);
	}
}
