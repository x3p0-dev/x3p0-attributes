<?php

/**
 * Attribute reader contract.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-attributes
 */

declare(strict_types=1);

namespace X3P0\Attributes;

use ReflectionClass;
use ReflectionClassConstant;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Reads attribute instances declared on a reflected class, method, property,
 * constant, or function. Implementations decide whether — and how — a
 * result is reused across calls; `ReflectionAttributeReader` does not, while
 * `CachedAttributeReader` decorates one to add that.
 */
interface AttributeReader
{
	/**
	 * Returns every instance of `$attributeClass` declared on `$target`.
	 * `$flags` mirrors `ReflectionClass::getAttributes()` — pass
	 * `ReflectionAttribute::IS_INSTANCEOF` to match subclasses instead of
	 * requiring the exact class.
	 *
	 * @template T of object
	 * @param    class-string<T> $attributeClass
	 * @return   list<T>
	 */
	public function attributesOn(
		ReflectionClass|ReflectionMethod|ReflectionProperty|ReflectionClassConstant|ReflectionFunction $target,
		string $attributeClass,
		int $flags = 0
	): array;
}
