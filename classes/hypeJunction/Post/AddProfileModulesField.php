<?php

namespace hypeJunction\Post;

use Elgg\HooksRegistrationService\Hook;
use hypeJunction\Fields\ProfileModulesField;

class AddProfileModulesField {

	public function __invoke(Hook $hook) {
		$entity = $hook->getEntityParam();
		$fields = $hook->getValue();

		$fields->add('modules', new ProfileModulesField([
			'type' => 'profile_modules',
			'section' => 'sidebar',
			'priority' => 900,
			'is_profile_field' => false,
			'is_create_field' => !$entity instanceof \ElggUser,
		]));

		return $fields;
	}
}