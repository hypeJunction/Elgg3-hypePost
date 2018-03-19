<?php

namespace hypeJunction\Post;

use DatabaseException;
use Elgg\Event;

/**
 * Handler
 */
class CreateRiverItem {

	/**
	 * Add post to river
	 *
	 * @elgg_event create object
	 *
	 * @param Event $event Event
	 *
	 * @throws DatabaseException
	 */
	public function __invoke(Event $event) {
		$entity = $event->getObject();
		if (!$entity instanceof \ElggEntity) {
			return;
		}

		if (!$entity->getVolatileData('add_to_river')) {
			return;
		}

		$params = ['entity' => $entity];

		if (!$event->elgg()->hooks->trigger(
			'uses:river',
			"$entity->type:$entity->subtype",
			$params,
			true
		)) {
			return;
		}

		elgg_create_river_item([
			'action_type' => 'create',
			'subject_guid' => $entity->owner_guid,
			'object_guid' => $entity->guid,
			'target_guid' => $entity->container_guid,
		]);
	}
}
