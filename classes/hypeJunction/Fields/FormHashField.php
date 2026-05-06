<?php

namespace hypeJunction\Fields;

use ElggEntity;

/**
 * FormHashField class.
 */
class FormHashField extends HiddenField {

	/**
	 * retrieve.
	 *
	 * @param ElggEntity $entity entity
	 *
	 * @return mixed
	 */
	public function retrieve(ElggEntity $entity) {
		return elgg_build_hmac([
			'guid' => (int) $entity->guid,
			'type' => $entity->type,
			'subtype' => $entity->subtype,
		])->getToken();
	}
}
