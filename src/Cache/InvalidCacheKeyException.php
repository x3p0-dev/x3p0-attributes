<?php

/**
 * Invalid cache key exception.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-attributes
 */

declare(strict_types=1);

namespace X3P0\Attributes\Cache;

use InvalidArgumentException as PhpInvalidArgumentException;

/**
 * Thrown by `ArrayCache` when a key does not meet `Cache`'s requirements.
 */
final class InvalidCacheKeyException extends PhpInvalidArgumentException implements InvalidArgumentException
{
}
