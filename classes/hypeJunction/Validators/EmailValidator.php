<?php

namespace hypeJunction\Validators;

use hypeJunction\ValidationException;

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