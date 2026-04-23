<?php

namespace hypeJunction\Validators;

use Elgg\IntegrationTestCase;
use hypeJunction\ValidationException;

class LengthValidatorTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return '';
	}

	public function up(): void {}

	public function down(): void {}

	public function testPassesWhenNoConstraints(): void {
		$v = new LengthValidator();
		$v->validate('anything');
		$this->assertTrue(true);
	}

	public function testPassesAtExactMinLength(): void {
		$v = new LengthValidator(3);
		$v->validate('abc');
		$this->assertTrue(true);
	}

	public function testThrowsWhenBelowMinLength(): void {
		$v = new LengthValidator(5);
		$this->expectException(ValidationException::class);
		$v->validate('ab');
	}

	public function testPassesAtExactMaxLength(): void {
		$v = new LengthValidator(null, 5);
		$v->validate('hello');
		$this->assertTrue(true);
	}

	public function testThrowsWhenAboveMaxLength(): void {
		$v = new LengthValidator(null, 3);
		$this->expectException(ValidationException::class);
		$v->validate('toolong');
	}

	public function testPassesWithinBothBounds(): void {
		$v = new LengthValidator(3, 10);
		$v->validate('hello');
		$this->assertTrue(true);
	}

	public function testThrowsOnEmptyStringBelowMin(): void {
		$v = new LengthValidator(1);
		$this->expectException(ValidationException::class);
		$v->validate('');
	}
}
