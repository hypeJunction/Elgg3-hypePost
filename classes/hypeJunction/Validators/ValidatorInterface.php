<?php

namespace hypeJunction\Validators;

use hypeJunction\ValidationException;

/**
 * ValidatorInterface interface.
 */
interface ValidatorInterface {

	/**
	 * Validate a value
	 *
	 * @param mixed $value Value to validate
	 *
	 * @return void
	 * @throws ValidationException
	 */
	public function validate($value);
}
