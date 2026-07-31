<?php

/**
 * In-memory array cache.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-attributes
 */

declare(strict_types=1);

namespace X3P0\Attributes\Cache;

use DateInterval;
use DateTimeImmutable;

/**
 * An in-memory `Cache` backed by a plain array. Entries live only as long as
 * the object holding them — there is no persistence across requests — but
 * TTL expiry and key validation behave the same as a real PSR-16 store.
 */
final class ArrayCache implements Cache
{
	/**
	 * Characters PSR-16 reserves and forbids in a cache key, so a key
	 * valid here stays valid against any real PSR-16 implementation.
	 */
	private const RESERVED_KEY_CHARACTERS = '{}()/\@:';

	/**
	 * The stored entries, each a `[value, expiresAt]` pair keyed by cache
	 * key.
	 *
	 * @var array<string, array{0: mixed, 1: int|null}>
	 */
	private array $items = [];

	/**
	 * @inheritDoc
	 */
	public function get(string $key, mixed $default = null): mixed
	{
		return $this->has($key) ? $this->items[$key][0] : $default;
	}

	/**
	 * @inheritDoc
	 */
	public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
	{
		$this->assertValidKey($key);

		$this->items[$key] = [$value, $this->expiresAt($ttl)];

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function delete(string $key): bool
	{
		$this->assertValidKey($key);

		unset($this->items[$key]);

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function clear(): bool
	{
		$this->items = [];

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function getMultiple(iterable $keys, mixed $default = null): iterable
	{
		$values = [];

		foreach ($keys as $key) {
			$values[$key] = $this->get($key, $default);
		}

		return $values;
	}

	/**
	 * @inheritDoc
	 */
	public function setMultiple(iterable $values, null|int|DateInterval $ttl = null): bool
	{
		foreach ($values as $key => $value) {
			$this->set((string) $key, $value, $ttl);
		}

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function deleteMultiple(iterable $keys): bool
	{
		foreach ($keys as $key) {
			$this->delete($key);
		}

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function has(string $key): bool
	{
		$this->assertValidKey($key);

		if (!array_key_exists($key, $this->items)) {
			return false;
		}

		[, $expires] = $this->items[$key];

		if ($expires !== null && $expires <= time()) {
			unset($this->items[$key]);

			return false;
		}

		return true;
	}

	/**
	 * Converts a TTL into an absolute expiration timestamp, or `null` if
	 * the entry should never expire on its own.
	 */
	private function expiresAt(null|int|DateInterval $ttl): ?int
	{
		return match (true) {
			$ttl === null => null,
			$ttl instanceof DateInterval => (new DateTimeImmutable())->add($ttl)->getTimestamp(),
			default => time() + $ttl,
		};
	}

	/**
	 * Validates that `$key` meets `Cache`'s key requirements.
	 *
	 * @throws InvalidArgumentException
	 */
	private function assertValidKey(string $key): void
	{
		if ($key === '' || strpbrk($key, self::RESERVED_KEY_CHARACTERS) !== false) {
			throw new InvalidCacheKeyException(
				sprintf('"%s" is not a legal cache key.', $key)
			);
		}
	}
}
