<?php

namespace hypeJunction\Fields;

use Elgg\Request;
use ElggEntity;
use hypeJunction\ValidationException;
use JsonSerializable;
use Serializable;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @property string $type              Input type
 * @property string $name              Input/property name
 * @property mixed  $value             Input value
 * @property string $section           Section to display the field on
 * @property bool   $is_profile_field  Display field output on entity profile
 * @property bool   $is_create_field   Display field on create (new entity) form
 * @property bool   $is_edit_field     Display field on edit (existing entity) form
 * @property bool   $is_admin_field    Only show this field to admins
 * @property bool   $is_editable       Should this field be editable once the value is set
 * @property string $priority          Display priority
 * @property int    $width             Field width on a 6-column grid
 * @property string $field_class       Class to apply to field
 *                                    Using 'class' will apply it to the input element
 *
 * @property string $required          Is this field required
 * @property int    $max               Maximum value
 * @property int    $min               Minimum value
 * @property int    $maxlength         Maximum length of the input
 * @property int    $minlength         Minimum length of the input
 */
interface FieldInterface extends Serializable, JsonSerializable {

	/**
	 * Determine if the field should be shown in this context
	 *
	 * @param ElggEntity $entity  Entity
	 * @param string     $context Display context
	 *
	 * @return bool
	 */
	public function isVisible(ElggEntity $entity, $context = null);

	/**
	 * Prepare raw value from request data and sanitize/normalize it
	 * Raw data will be assembled into a ParameterBag and passed on to storage methods
	 *
	 * @param Request    $request Request object
	 * @param ElggEntity $entity  Entity
	 *
	 * @return mixed
	 */
	public function raw(Request $request, ElggEntity $entity);

	/**
	 * Validate raw value
	 * By default action handler will validate required values
	 *
	 * @param string $value Value to validate
	 *
	 * @return void
	 * @throws ValidationException
	 */
	public function validate($value);

	/**
	 * Store raw value as an entity property
	 *
	 * @param ElggEntity   $entity     Entity
	 * @param ParameterBag $parameters Raw data
	 *
	 * @return bool
	 */
	public function save(ElggEntity $entity, ParameterBag $parameters);

	/**
	 * Retrieve entity property
	 *
	 * @param ElggEntity $entity Entity
	 *
	 * @return mixed
	 */
	public function retrieve(ElggEntity $entity);

	/**
	 * Get field label
	 *
	 * @param ElggEntity $entity Entity
	 *
	 * @return string|false
	 */
	public function label(ElggEntity $entity);

	/**
	 * Get field help text
	 *
	 * @param ElggEntity $entity Entity
	 *
	 * @return string|false
	 */
	public function help(ElggEntity $entity);

	/**
	 * Get input placeholder text
	 *
	 * @param ElggEntity $entity Entity
	 *
	 * @return string|false
	 */
	public function placeholder(ElggEntity $entity);

	/**
	 * Render an input field
	 *
	 * @param ElggEntity $entity  Entity
	 * @param null       $context Display context
	 *
	 * @return string
	 */
	public function render(\ElggEntity $entity, $context = null);

	/**
	 * Render field output
	 *
	 * @param ElggEntity $entity  Entity
	 * @param null       $context Display context
	 *
	 * @return mixed
	 */
	public function output(ElggEntity $entity, $context = null);

}