<?php

namespace hypeJunction\Fields;

use ElggEntity;

class AccessField extends MetaField {

	/**
	 * {@inheritdoc}
	 */
	public function retrieve(ElggEntity $entity) {
		if (!$entity->guid) {
			return get_default_access();
		}

		return parent::retrieve($entity);
	}

}