<?php

namespace hypeJunction\Post;

use DatabaseException;
use Elgg\Event;
use ElggEntity;

/**
 * Handler
 */
class River {

	/**
	 * Create a river item
	 *
	 * @param ElggEntity $entity Entity
	 * @param string     $action Action name
	 *
	 * @return void
	 */
	public function add(ElggEntity $entity, $action = 'create') {

		$params = ['entity' => $entity];

		if (!elgg_trigger_plugin_hook(
			'uses:river',
			"$entity->type:$entity->subtype",
			$params,
			true
		)) {
			return;
		}

		try {
			elgg_create_river_item([
				'action_type' => $action,
				'subject_guid' => $entity->owner_guid,
				'object_guid' => $entity->guid,
				'target_guid' => $entity->getContainerEntity() instanceof \ElggGroup ? $entity->container_guid : null,
				'posted' => $entity->time_created,
			]);
		} catch (DatabaseException $ex) {

		}
	}
}
