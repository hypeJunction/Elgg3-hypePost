<?php

namespace hypeJunction\Validators;

use Elgg\IntegrationTestCase;
use hypeJunction\ValidationException;

class UrlValidatorTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return '';
	}

	public function up(): void {}

	public function down(): void {}

	public function testPassesValidHttpUrl(): void {
		$v = new UrlValidator();
		$v->validate('http://example.com');
		$this->assertTrue(true);
	}

	public function testPassesValidHttpsUrlWithPath(): void {
		$v = new UrlValidator();
		$v->validate('https://example.com/path?query=1&foo=bar');
		$this->assertTrue(true);
	}

	public function testPassesValidUrlWithUnicodeChars(): void {
		// Unicode URL that passes filter_var after ASCII substitution
		$v = new UrlValidator();
		$v->validate('http://münchen.de/path');
		$this->assertTrue(true);
	}

	public function testAsciiNonUrlPassesSilently(): void {
		// Implementation only validates unicode strings; ASCII non-URLs pass without check
		$v = new UrlValidator();
		$v->validate('not a url');
		$this->assertTrue(true);
	}

	public function testThrowsOnUnicodeStringWithInvalidStructure(): void {
		// Unicode string that cannot pass filter_var even after substitution
		$v = new UrlValidator();
		$this->expectException(ValidationException::class);
		$v->validate('héllo wörld'); // no scheme, no host — invalid URL structure
	}
}
