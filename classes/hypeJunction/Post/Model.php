<?php

namespace hypeJunction\Post;

use Elgg\Di\ServiceFacade;
use Elgg\EntityNotFoundException;
use Elgg\EntityPermissionsException;
use Elgg\HttpException;
use Elgg\Request;
use ElggEntity;
use ElggObject;
use hypeJunction\Fields\Collection;
use hypeJunction\Fields\ControlElement;
use hypeJunction\Fields\Field;
use hypeJunction\Fields\FieldInterface;
use hypeJunction\Fields\FormHashField;
use hypeJunction\Fields\HiddenField;
use hypeJunction\ValidationException;
use InvalidParameterException;
use Symfony\Component\HttpFoundation\ParameterBag;

class Model {

	use ServiceFacade;

	/**
	 * @var Post
	 */
	protected $post_service;

	/**
	 * Constructor
	 *
	 * @param Post $post_service
	 */
	public function __construct(Post $post_service) {
		$this->post_service = $post_service;
	}

	/**
	 * {@inheritdoc}
	 */
	public function name() {
		return 'posts.model';
	}

	/**
	 * Get entity fields
	 *
	 * @param ElggEntity   $entity  Entity
	 * @param string|array $options Hook options
	 *
	 * @option string $context Display context
	 *
	 * @return Collection
	 * @throws InvalidParameterException
	 */
	public function getFields(ElggEntity $entity, $options = []) {

		if (is_string($options)) {
			$context = $options;
			$options = [
				'context' => $context,
			];
		}

		$options['entity'] = $entity;

		$fields = new Collection();

		$fields = elgg_trigger_plugin_hook('fields', "$entity->type", $options, $fields);
		if (!$fields instanceof Collection) {
			throw new \RuntimeException("'fields' hook must return an instance of " . Collection::class);
		}

		$fields = elgg_trigger_plugin_hook('fields', "$entity->type:$entity->subtype", $options, $fields);
		if (!$fields instanceof Collection) {
			throw new \RuntimeException("'fields' hook must return an instance of " . Collection::class);
		}

		$fields->add('type', new HiddenField([
			'type' => 'hidden',
			'contexts' => false,
		]));

		$fields->add('subtype', new HiddenField([
			'type' => 'hidden',
			'contexts' => false,
		]));

		$fields->add('guid', new HiddenField([
			'type' => 'hidden',
			'contexts' => false,
		]));

		if (!$fields->has('container_guid')) {
			$fields->add('container_guid', new HiddenField([
				'type' => 'hidden',
				'contexts' => false,
			]));
		}

		// We are adding a hash to validate that the original type and subtype of
		// the entity were not altered client side
		$fields->add('_hash', new FormHashField([
			'type' => 'hidden',
			'contexts' => false,
		]));

		if (!$fields->has('cancel')) {
			$fields->add('cancel', new ControlElement([
				'type' => 'post/cancel',
				'section' => 'actions',
				'priority' => 400,
				'contexts' => false,
			]));
		}

		if (!$fields->has('submit')) {
			$fields->add('submit', new ControlElement([
				'type' => 'submit',
				'section' => 'actions',
				'value' => elgg_echo('save'),
				'priority' => 600,
				'contexts' => false,
			]));
		}

		$context = elgg_extract('context', $options);

		$fields = $fields->filter(function (FieldInterface $field) use ($entity, $context) {
			return $field->isVisible($entity, $context);
		});

		return $fields;
	}

	/**
	 * Prepare form vars
	 *
	 * @param ElggEntity $entity Entity
	 * @param array      $vars   Default vars
	 *
	 * @return array
	 * @throws InvalidParameterException
	 */
	public function getFormVars(ElggEntity $entity, array $vars = []) {

		if ($entity->container_guid) {
			$container = $entity->getContainerEntity();
		} else {
			$container = elgg_extract('container', $vars);
			if (!$container) {
				$container_guid = elgg_extract('container_guid', $vars);
				$container = get_entity($container_guid);
			}
		}

		$defaults = [
			'title' => '',
			'description' => '',
			'access_id' => get_default_access(),
		];

		$fields = $this->getFields($entity, $vars);

		foreach ($fields as $field) {
			/* @var $field FieldInterface */

			$name = $field->name;
			if (isset($defaults[$name])) {
				continue;
			}
			$defaults[$name] = '';
		}

		$sticky = elgg_get_sticky_values("edit:$entity->type:$entity->subtype");
		elgg_clear_sticky_form("edit:$entity->type:$entity->subtype");

		foreach ($defaults as $key => $value) {
			$vars[$key] = elgg_extract($key, $sticky);
		}

		foreach ($fields as $field) {
			/* @var $field FieldInterface */
			$name = $field->name;
			if (isset($vars[$name])) {
				$field->value = $vars[$name];
			}
		}

		$vars['fields'] = $fields;
		$vars['entity'] = $entity;
		$vars['guid'] = $entity->guid;
		$vars['container'] = $container;
		$vars['container_guid'] = $container->guid;

		return $vars;
	}

	/**
	 * Accept save action values
	 *
	 * @param $request Request
	 *
	 * @return ElggEntity|false
	 * @throws EntityNotFoundException
	 * @throws EntityPermissionsException
	 * @throws HttpException
	 * @throws InvalidParameterException
	 */
	public function save(Request $request) {

		$guid = (int) $request->getParam('guid');
		$type = $request->getParam('type');
		$subtype = $request->getParam('subtype');

		elgg_make_sticky_form("edit:$type:$subtype");

		$user = elgg_get_logged_in_user_entity();

		if ($guid) {
			$context = Field::CONTEXT_EDIT_FORM;

			$entity = get_entity($guid);
			if (!$entity) {
				throw new EntityNotFoundException();
			}

			if (!$entity->canEdit()) {
				throw new EntityPermissionsException();
			}
			$container = $entity->getContainerEntity();
		} else {
			$context = Field::CONTEXT_CREATE_FORM;

			$container_guid = $request->getParam('container_guid');
			if (!$container_guid) {
				$container_guid = $user->guid;
			}

			$container = get_entity($container_guid);
			if (!$container || !$container->canWriteToContainer(0, $type, $subtype)) {
				throw new EntityPermissionsException();
			}

			$class = elgg_get_entity_class($type, $subtype) ? : ElggObject::class;

			$entity = new $class();

			if (!$entity instanceof ElggEntity) {
				throw new HttpException("$class must implement " . ElggEntity::class);
			}

			$entity->owner_guid = $user->guid;
			$entity->container_guid = $container_guid;
		}

		$access_id = $request->getParam('access_id');
		if (!isset($access_id)) {
			if ($container instanceof \ElggGroup) {
				$access_id = $container->group_acl;
			} else {
				$access_id = get_default_access($user);
			}
		}

		$entity->access_id = $access_id;

		$fields = $this->getFields($entity);

		$fields = $fields->filter(function (FieldInterface $field) {
			if (in_array($field->name, ElggEntity::$primary_attr_names)) {
				return false;
			}

			return true;
		});

		$parameters = new ParameterBag();

		$errors = [];

		foreach ($fields as $field) {
			/* @var $field \hypeJunction\Fields\FieldInterface */

			$value = $field->raw($request, $entity);

			if (!isset($value)) {
				// Field is not present
				continue;
			}

			$label = $field->label($entity);
			try {
				$field->validate($value);
			} catch (ValidationException $ex) {
				$errors[$field->name] = elgg_echo('validation:error', [$label, $ex->getMessage()]);
				continue;
			}

			$field->value = $value;
			$parameters->set($field->name, $value);
		}

		if ($errors) {
			throw new HttpException(implode("\r\n", $errors));
		}

		try {
			if (!$entity->save()) {
				// Save entity attributes
				return false;
			}
		} catch (\Exception $e) {
			throw new HttpException($e->getMessage());
		}

		foreach ($fields as $field) {
			if (is_null($parameters->get($field->name))) {
				continue;
			}

			$field->save($entity, $parameters);
		}

		$entity->save();

		$entity->setVolatileData('add_to_river', $context == Field::CONTEXT_CREATE_FORM);

		if (!isset($entity->published_status)) {
			$entity->published_status = 'published';
			elgg_trigger_event('publish', 'object', $entity);
		}

		elgg_clear_sticky_form("edit:$entity->type:$entity->subtype");

		return $entity;
	}
}
