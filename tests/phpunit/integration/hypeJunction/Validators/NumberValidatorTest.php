<?php

namespace hypeJunction\Validators;

use Elgg\IntegrationTestCase;
use hypeJunction\ValidationException;

class NumberValidatorTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return '';
	}

	public function up(): void {}

	public function down(): void {}

	public function testPassesWhenNoConstraints(): void {
		$v = new NumberValidator();
		$v->validate(42);
		$this->assertTrue(true);
	}

	public function testPassesAtExactMin(): void {
		$v = new NumberValidator(0);
		$v->validate(0);
		$this->assertTrue(true);
	}

	public function testThrowsWhenBelowMin(): void {
		$v = new NumberValidator(5);
		$this->expectException(ValidationException::class);
		$v->validate(4);
	}

	public function testPassesAtExactMax(): void {
		$v = new NumberValidator(null, 100);
		$v->validate(100);
		$this->assertTrue(true);
	}

	public function testThrowsWhenAboveMax(): void {
		$v = new NumberValidator(null, 10);
		$this->expectException(ValidationException::class);
		$v->validate(11);
	}

	public function testPassesWithinBothBounds(): void {
		$v = new NumberValidator(1, 10);
		$v->validate(5);
		$this->assertTrue(true);
	}

	public function testPassesNegativeValueWithinBounds(): void {
		$v = new NumberValidator(-10, -1);
		$v->validate(-5);
		$this->assertTrue(true);
	}
}
