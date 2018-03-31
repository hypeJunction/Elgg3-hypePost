<?php

namespace hypeJunction\Validators;

use hypeJunction\ValidationException;

class UrlValidator implements ValidatorInterface {

	/**
	 * {@inheritdoc}
	 */
	public function validate($value) {
		// based on http://php.net/manual/en/function.filter-var.php#104160
		// adapted by @mrclay in https://github.com/mrclay/Elgg-leaf/blob/62bf31c0ccdaab549a7e585a4412443e09821db3/engine/lib/output.php
		$res = filter_var($value, FILTER_VALIDATE_URL);
		if ($res) {
			return;
		}
		// Check if it has unicode chars.
		$l = mb_strlen($value);
		if (strlen($value) == $l) {
			return;
		}
		// Replace wide chars by “X”.
		$s = '';
		for ($i = 0; $i < $l; ++$i) {
			$ch = elgg_substr($value, $i, 1);
			$s .= (strlen($ch) > 1) ? 'X' : $ch;
		}
		// Re-check now.
		if (filter_var($s, FILTER_VALIDATE_URL)) {
			return;
		}

		throw new ValidationException(elgg_echo('validation:error:type:url'));

	}
}