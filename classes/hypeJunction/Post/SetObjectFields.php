<?php

namespace hypeJunction\Post;

use Elgg\Hook;
use hypeJunction\Fields\AccessField;
use hypeJunction\Fields\Collection;
use hypeJunction\Fields\CoverField;
use hypeJunction\Fields\DisableCommentsField;
use hypeJunction\Fields\HtmlField;
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

		$entity = $hook->getEntityParam();

		$fields->add('title', new TitleField([
			'type' => 'text',
			'is_profile_field' => false,
			'priority' => 10,
			'required' => true,
			'is_export_field' => true,
		]));

		$fields->add('description', new HtmlField([
			'type' => 'longtext',
			'rows' => 3,
			'section' => 'content',
			'is_profile_field' => false,
			'required' => true,
			'data-parsley-required' => true,
			'data-parsley-trigger' => 'null',
			'priority' => 20,
			'is_export_field' => true,
		]));

		$fields->add('excerpt', new MetaField([
			'type' => 'text',
			'maxlength' => 200,
			'is_profile_field' => false,
			'priority' => 30,
			'is_export_field' => true,
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
			'is_export_field' => true,
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
			'type' => 'checkbox',
			'section' => 'sidebar',
			'default' => '0',
			'value' => '1',
			'checked' => (bool) $entity->disable_comments,
			'switch' => true,
			'priority' => 800,
			'is_profile_field' => false,
			'is_export_field' => true,
		]));

		return $fields;
	}
}