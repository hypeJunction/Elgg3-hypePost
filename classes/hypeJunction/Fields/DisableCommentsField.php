<?php

namespace hypeJunction\Fields;

use ElggEntity;

class DisableCommentsField extends MetaField {

	/**
	 * {@inheritdoc}
	 */
	public function isVisible(ElggEntity $entity, $context = null) {
		if (!parent::isVisible($entity, $context)) {
			return false;
		}

		return elgg_trigger_plugin_hook(
			'uses:comments',
			"$entity->type:$entity->subtype",
			['entity' => $entity],
			$entity instanceof \ElggObject
		);
	}
}