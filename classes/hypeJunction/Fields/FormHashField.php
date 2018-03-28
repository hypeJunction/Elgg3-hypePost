<?php

namespace hypeJunction\Fields;

use ElggEntity;

class FormHashField extends HiddenField {

	public function retrieve(ElggEntity $entity) {
		return elgg_build_hmac([
			'guid' => (int) $entity->guid,
			'type' => $entity->type,
			'subtype' => $entity->subtype,
		])->getToken();
	}

}