<?php

/**
 * Reflection-based attribute reader.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-attributes
 */

declare(strict_types=1);

namespace X3P0\Attributes;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

/**
 * Reads attribute instances straight off PHP's reflection API, doing no
 * caching of its own. Every call constructs a fresh instance via
 * `ReflectionAttribute::newInstance()` — wrap this in `CachedAttributeReader`
 * to reuse results across calls.
 */
final class ReflectionAttributeReader implements AttributeReader
{
	/**
	 * @inheritDoc
	 */
	public function attributesOn(
		ReflectionClass|ReflectionMethod|ReflectionProperty|ReflectionClassConstant|ReflectionFunction|ReflectionParameter $target,
		string $attributeClass,
		int $flags = 0
	): array {
		return array_map(
			fn (ReflectionAttribute $attribute): object => $attribute->newInstance(),
			$target->getAttributes($attributeClass, $flags)
		);
	}
}
