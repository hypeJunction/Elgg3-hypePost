<?php

namespace hypeJunction\Post;

use Elgg\Hook;
use hypeJunction\Fields\AccessField;
use hypeJunction\Fields\Collection;
use hypeJunction\Fields\CoverField;
use hypeJunction\Fields\DisableCommentsField;
use hypeJunction\Fields\IconField;
use hypeJunction\Fields\MetaField;
use hypeJunction\Fields\TagsField;
use hypeJunction\Fields\TitleField;
use InvalidParameterException;

class SetObjectFields {

	/**
	 * Setup default object fields
	 *
	 * @elgg_plugin_hook fields object
	 *
	 * @param Hook $hook Hook
	 *
	 * @return Collection|mixed
	 * @throws InvalidParameterException
	 */
	public function __invoke(Hook $hook) {

		$fields = $hook->getValue();
		/* @var $fields Collection */

		$fields->add('title', new TitleField([
			'type' => 'text',
			'is_profile_field' => false,
			'priority' => 10,
			'required' => true,
		]));

		$fields->add('description', new MetaField([
			'type' => 'longtext',
			'rows' => 3,
			'section' => 'content',
			'is_profile_field' => false,
			'required' => true,
			'data-parsley-required' => true,
			'data-parsley-trigger' => 'null',
			'priority' => 20,
		]));

		$fields->add('excerpt', new MetaField([
			'type' => 'text',
			'maxlength' => 200,
			'is_profile_field' => false,
			'priority' => 30,
		]));

		$fields->add('icon', new IconField([
			'type' => 'file',
			'section' => 'sidebar',
			'priority' => 400,
			'is_profile_field' => false,
		]));

		$fields->add('cover', new CoverField([
			'type' => 'post/cover',
			'section' => 'sidebar',
			'priority' => 400,
			'is_profile_field' => false,
		]));

		$fields->add('tags', new TagsField([
			'type' => 'tags',
			'section' => 'content',
			'is_profile_field' => false,
		]));

		$fields->add('access_id', new AccessField([
			'type' => 'access',
			'section' => 'sidebar',
			'required' => true,
			'priority' => 100,
			'is_profile_field' => false,
		]));

		$fields->add('disable_comments', new DisableCommentsField([
			'type' => 'select',
			'section' => 'sidebar',
			'options_values' => [
				0 => elgg_echo('enable'),
				1 => elgg_echo('disable'),
			],
			'priority' => 300,
			'is_profile_field' => false,
		]));

		return $fields;
	}
}