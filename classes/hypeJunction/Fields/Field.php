<?php

namespace hypeJunction\Fields;

use ArrayObject;
use Elgg\Request;
use ElggEntity;
use hypeJunction\ValidationException;
use hypeJunction\Validators\EmailValidator;
use hypeJunction\Validators\LengthValidator;
use hypeJunction\Validators\NumberValidator;
use hypeJunction\Validators\UrlValidator;

abstract class Field extends ArrayObject implements FieldInterface {

	const CONTEXT_PROFILE = 'profile';
	const CONTEXT_EDIT_FORM = 'edit_form';
	const CONTEXT_CREATE_FORM = 'create_form';
	const CONTEXT_EXPORT = 'export';

	var $defaults = [
		'type' => 'text',
		'section' => 'content',
		'is_profile_field' => true,
		'is_create_field' => true,
		'is_edit_field' => true,
		'is_admin_field' => false,
		'is_editable' => true,
		'is_search_field' => false,
		'is_export_field' => false,
		'contexts' => [],
		'priority' => 500,
		'width' => 6,
	];

	/**
	 * {@inheritdoc}
	 */
	final public function __construct($input = [], $flags = ArrayObject::ARRAY_AS_PROPS, $iterator_class = "ArrayIterator") {
		$input = array_merge($this->defaults, $input);
		parent::__construct($input, $flags, $iterator_class);
	}

	/**
	 * Validate raw value
	 * By default action handler will validate required values
	 *
	 * @param string $value Value to validate
	 *
	 * @return void
	 * @throws ValidationException
	 */
	public function validate($value) {
		if ($this->required && empty($value) && $value !== '0') {
			throw new ValidationException(elgg_echo('validation:error:required'));
		}

		if (empty($value)) {
			return;
		}

		if ($this->type === 'email') {
			$email = new EmailValidator();
			$email->validate($value);
		}

		if ($this->type === 'url') {
			$email = new UrlValidator();
			$email->validate($value);
		}

		$length = new LengthValidator($this->minlength, $this->maxlength);
		$length->validate($value);

		$number = new NumberValidator($this->min, $this->max);
		$number->validate($value);
	}

	/**
	 * Determine if the field should be shown in this context
	 *
	 * @param ElggEntity $entity  Entity
	 * @param string     $context Display context
	 *
	 * @return bool
	 */
	public function isVisible(ElggEntity $entity, $context = null) {
		if ($this->is_admin_field && !elgg_is_admin_logged_in()) {
			return false;
		}

		if (!$this->is_editable && (in_array($context, [self::CONTEXT_EDIT_FORM, self::CONTEXT_CREATE_FORM]))) {
			$value = $this->retrieve($entity);
			if (!empty($value)) {
				return false;
			}
		}

		switch ($context) {
			case self::CONTEXT_PROFILE :
				if ($this->is_profile_field === false) {
					return false;
				}

				$ignored = array_merge(\ElggEntity::$primary_attr_names, [
					'title',
					'description',
					'tags',
					'timezone',
					'submit',
					'_hash',
				]);

				if (in_array($this->name, $ignored)) {
					return false;
				}
				break;

			case self::CONTEXT_EDIT_FORM :
				if ($this->is_edit_field === false) {
					return false;
				}
				break;

			case self::CONTEXT_CREATE_FORM :
				if ($this->is_create_field === false) {
					return false;
				}
				break;

			case self::CONTEXT_EXPORT :
				if ($this->is_export_field === true || $this->is_profile_field === true) {
					return true;
				}
				return false;

			default :
				if ($context && $this->contexts !== false) {
					return in_array($context, $this->contexts);
				}
				break;
		}


		return true;
	}

	/**
	 * Prepare raw value from request data and sanitize/normalize it
	 * Raw data will be assembled into a ParameterBag and passed on to storage methods
	 *
	 * @param Request    $request Request object
	 * @param ElggEntity $entity  Entity
	 *
	 * @return mixed
	 */
	public function raw(Request $request, ElggEntity $entity) {
		return $request->getParam($this->name);
	}

	/**
	 * Generate a label
	 *
	 * @param ElggEntity $entity Entity
	 * @param string     $suffix Suffix (e.g. help, placeholder)
	 * @param bool       $strict Strict mode
	 *                           If strict, only existing translations will be returned
	 *                           Otherwise, either an existing translation or a raw string will be returned
	 *
	 * @return null|string
	 */
	protected function makeLabel(\ElggEntity $entity, $suffix, $strict = true) {
		$type = $entity->type;
		$subtype = $entity->subtype;

		$keys = [
			"field:$type:$subtype:$suffix",
			"field:$type:$suffix",
			"field:$suffix",
			"$suffix",
		];

		foreach ($keys as $key) {
			if (elgg_language_key_exists($key)) {
				return elgg_echo($key);
			}
		}

		if (!$strict) {
			return elgg_echo("field:$type:$subtype:$suffix");
		}

		return null;
	}

	/**
	 * Get field label
	 *
	 * @param ElggEntity $entity Entity
	 *
	 * @return string|false
	 */
	public function label(ElggEntity $entity) {
		return $this->makeLabel($entity, $this->name, false);
	}

	/**
	 * Get field help text
	 *
	 * @param ElggEntity $entity Entity
	 *
	 * @return string|false
	 */
	public function help(ElggEntity $entity) {
		return $this->makeLabel($entity, "{$this->name}:help");
	}

	/**
	 * Get input placeholder text
	 *
	 * @param ElggEntity $entity Entity
	 *
	 * @return string|false
	 */
	public function placeholder(ElggEntity $entity) {
		return $this->makeLabel($entity, "{$this->name}:placeholder");
	}

	/**
	 * Prepare field parameters
	 *
	 * @param ElggEntity $entity Entity
	 *
	 * @return array
	 */
	protected function normalize(\ElggEntity $entity) {
		$props = $this->getArrayCopy();

		$private = array_keys($this->defaults);

		foreach ($private as $key) {
			unset($props[$key]);
		}

		foreach ($props as $key => $prop) {
			if ($prop instanceof \Closure) {
				$props[$key] = $prop($entity, $this);
			}
		}

		$props['entity'] = $entity;

		if (!isset($props['#type'])) {
			$props['#type'] = $this->type;
		}
		if (!isset($props['#label'])) {
			$props['#label'] = $this->label($entity);
		} else if ($props['#label'] === false) {
			// checkbox hook does weird stuff
			unset($props['#label']);
		}

		if (!isset($props['#help'])) {
			$props['#help'] = $this->help($entity);
		}
		if (!isset($props['#placeholder'])) {
			$props['#placeholder'] = $this->placeholder($entity);
		}
		if (!isset($props['value'])) {
			$props['value'] = $this->retrieve($entity);
		}
		if (isset($props['input_name'])) {
			$props['name'] = $props['input_name'];
		}

		$class = elgg_extract_class($props, ['elgg-col'], 'field_class');
		$props['#class'] = elgg_extract_class($props, $class, '#class');

		$classes = [
			6 => 'elgg-col-1of1',
			5 => 'elgg-col-5of6',
			4 => 'elgg-col-2of3',
			3 => 'elgg-col-1of2',
			2 => 'elgg-col-1of3',
			1 => 'elgg-col-1of6',
			0 => '',
		];

		$props['#class'][] = elgg_extract((int) $this->width, $classes);

		if ($this->section === 'sidebar') {
			$props['#view'] = 'post/input/field';
		}

		return $props;
	}

	/**
	 * Render an input field
	 *
	 * @param ElggEntity $entity  Entity
	 * @param null       $context Display context
	 *
	 * @return string
	 */
	public function render(\ElggEntity $entity, $context = null) {
		if (!$this->isVisible($entity, $context)) {
			return '';
		}

		if (!elgg_view_exists("input/$this->type")) {
			return '';
		}

		return elgg_view_field($this->normalize($entity));
	}

	/**
	 * Render field output
	 *
	 * @param ElggEntity $entity  Entity
	 * @param null       $context Display context
	 *
	 * @return mixed
	 */
	public function output(ElggEntity $entity, $context = null) {
		if (!$this->isVisible($entity, $context)) {
			return '';
		}

		return elgg_view("post/output/field", $this->normalize($entity));
	}

	/**
	 * {@inheritdoc}
	 */
	public function export(ElggEntity $entity) {
		return $this->retrieve($entity);
	}

	/**
	 * {@inheritdoc}
	 */
	public function jsonSerialize() {
		return $this->getArrayCopy();
	}
}