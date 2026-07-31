<?php

/**
 * Cached attribute reader.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-attributes
 */

declare(strict_types=1);

namespace X3P0\Attributes;

use DateInterval;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use stdClass;
use X3P0\Attributes\Cache\ArrayCache;
use X3P0\Attributes\Cache\Cache;
use X3P0\Attributes\Cache\InvalidArgumentException;

/**
 * Decorates another `AttributeReader`, caching each call's result by target,
 * attribute class, and flags so a repeated read of the same combination
 * returns the same instances instead of reconstructing them.
 */
final class CachedAttributeReader implements AttributeReader
{
	/**
	 * A sentinel passed as `Cache::get()`'s default, so a genuine cache
	 * miss can be distinguished from a stored value of `null` (or any
	 * other falsy result). Shared by every instance since it carries no
	 * state of its own — only its identity matters.
	 */
	private static ?stdClass $miss = null;

	/**
	 * Decorates `$reader` with a cache, so a repeated call for the same
	 * target, attribute class, and flags is served from `$cache` instead
	 * of reflecting `$reader` again.
	 */
	public function __construct(
		private readonly AttributeReader $reader,
		private readonly Cache $cache = new ArrayCache(),
		private readonly null|int|DateInterval $ttl = null
	) {
	}

	/**
	 * @inheritDoc
	 * @throws InvalidArgumentException
	 */
	public function attributesOn(
		ReflectionClass|ReflectionMethod|ReflectionProperty|ReflectionClassConstant|ReflectionFunction|ReflectionParameter $target,
		string $attributeClass,
		int $flags = 0
	): array {
		$key  = $this->key($target, $attributeClass, $flags);
		$miss = self::$miss ??= new stdClass();

		$value = $this->cache->get($key, $miss);

		if ($value === $miss) {
			$value = $this->reader->attributesOn($target, $attributeClass, $flags);

			$this->cache->set($key, $value, $this->ttl);
		}

		return $value;
	}

	/**
	 * A cache key unique to a reflected member, attribute class, and flags —
	 * hashed so it can never collide with a character PSR-16 reserves,
	 * regardless of what `Cache` implementation is in use.
	 */
	private function key(
		ReflectionClass|ReflectionMethod|ReflectionProperty|ReflectionClassConstant|ReflectionFunction|ReflectionParameter $target,
		string $attributeClass,
		int $flags
	): string {
		return 'x3p0.attr.' . hash('xxh128', "{$this->identify($target)}|{$attributeClass}|{$flags}");
	}

	/**
	 * A stable string identity for a reflected member, used to key the
	 * cache.
	 */
	private function identify(
		ReflectionClass|ReflectionMethod|ReflectionProperty|ReflectionClassConstant|ReflectionFunction|ReflectionParameter $target
	): string {
		return match (true) {
			$target instanceof ReflectionClass, $target instanceof ReflectionFunction => $target->getName(),
			$target instanceof ReflectionParameter => sprintf(
				'%s($%s:%d)',
				$this->identifyFunction($target->getDeclaringFunction()),
				$target->getName(),
				$target->getPosition()
			),
			default => "{$target->class}::{$target->getName()}",
		};
	}

	/**
	 * A stable string identity for a parameter's declaring function or
	 * method, used to key the cache.
	 */
	private function identifyFunction(ReflectionFunctionAbstract $function): string
	{
		return $function instanceof ReflectionMethod
			? "{$function->class}::{$function->getName()}"
			: $function->getName();
	}
}
