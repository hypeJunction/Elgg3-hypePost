<?php

namespace hypeJunction\Post;

use Elgg\Event;
use hypeJunction\Fields\ProfileModulesField;

/**
 * AddProfileModulesField class.
 */
class AddProfileModulesField {

	/**
	 * __invoke.
	 *
	 * @param Hook $hook hook
	 *
	 * @return mixed
	 */
	public function __invoke(Event $event) {
		$entity = $event->getEntityParam();
		$fields = $event->getValue();

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
