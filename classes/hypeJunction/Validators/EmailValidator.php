<?php

namespace hypeJunction\Validators;

use hypeJunction\ValidationException;

/**
 * EmailValidator class.
 */
class EmailValidator implements ValidatorInterface {

	/**
	 * {@inheritdoc}
	 */
	public function validate($value) {
		if (!is_email_address($value)) {
			throw new ValidationException(elgg_echo('validation:error:type:email'));
		}
	}
}
