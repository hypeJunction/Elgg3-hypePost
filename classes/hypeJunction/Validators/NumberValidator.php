<?php

namespace hypeJunction\Validators;

use hypeJunction\ValidationException;

class NumberValidator implements ValidatorInterface {

	/**
	 * @var int|null
	 */
	protected $min;
	/**
	 * @var int|null
	 */
	protected $max;

	/**
	 * Constructor
	 *
	 * @param int $min Min length
	 * @param int $max Max length
	 */
	public function __construct($min = null, $max = null) {
		$this->min = $min;
		$this->max = $max;
	}

	/**
	 * Validate a value
	 *
	 * @param mixed $value Value to validate
	 *
	 * @return void
	 * @throws ValidationException
	 */
	public function validate($value) {
		if (isset($this->min) && $value < $this->min) {
			throw new ValidationException(elgg_echo('validation:error:min'));
		}

		if (isset($this->max) && $value > $this->max) {
			throw new ValidationException(elgg_echo('validation:error:max'));
		}
	}
}