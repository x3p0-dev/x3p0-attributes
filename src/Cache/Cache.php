<?php

/**
 * Cache contract.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-attributes
 */

declare(strict_types=1);

namespace X3P0\Attributes\Cache;

use DateInterval;

/**
 * A cache that stores and retrieves arbitrary values by string key, each
 * optionally expiring after a time-to-live. Mirrors PSR-16
 * (`Psr\SimpleCache\CacheInterface`) method-for-method so an adapter around
 * a real PSR-16 implementation is a thin pass-through, without this package
 * depending on `psr/simple-cache`.
 */
interface Cache
{
	/**
	 * Returns the value stored under `$key`, or `$default` if it does not
	 * exist or has expired.
	 *
	 * @throws InvalidArgumentException if `$key` is not a legal value.
	 */
	public function get(string $key, mixed $default = null): mixed;

	/**
	 * Stores `$value` under `$key`. `$ttl` is the number of seconds (or an
	 * equivalent `DateInterval`) before the entry expires; `null` means it
	 * never expires on its own.
	 *
	 * @throws InvalidArgumentException if `$key` is not a legal value.
	 */
	public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool;

	/**
	 * Removes the value stored under `$key`, if any.
	 *
	 * @throws InvalidArgumentException if `$key` is not a legal value.
	 */
	public function delete(string $key): bool;

	/**
	 * Removes every value from the cache.
	 */
	public function clear(): bool;

	/**
	 * Returns the values stored under `$keys`, keyed the same way; any key
	 * without a stored (or non-expired) value maps to `$default`.
	 *
	 * @param  iterable<string> $keys
	 * @return iterable<string, mixed>
	 * @throws InvalidArgumentException if `$keys` is not a list of legal keys.
	 */
	public function getMultiple(iterable $keys, mixed $default = null): iterable;

	/**
	 * Stores each value in `$values` under its key, all with the same `$ttl`.
	 *
	 * @param  iterable<string, mixed> $values
	 * @throws InvalidArgumentException if `$values` contains an illegal key.
	 */
	public function setMultiple(iterable $values, null|int|DateInterval $ttl = null): bool;

	/**
	 * Removes the values stored under `$keys`, if any.
	 *
	 * @param  iterable<string> $keys
	 * @throws InvalidArgumentException if `$keys` is not a list of legal keys.
	 */
	public function deleteMultiple(iterable $keys): bool;

	/**
	 * Whether a non-expired value is stored under `$key`.
	 *
	 * @throws InvalidArgumentException if `$key` is not a legal value.
	 */
	public function has(string $key): bool;
}
