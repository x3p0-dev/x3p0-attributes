<?php

/**
 * Invalid cache argument exception marker.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-attributes
 */

declare(strict_types=1);

namespace X3P0\Attributes\Cache;

/**
 * Marker interface for exceptions thrown when a key (or a value passed as
 * one) does not meet `Cache`'s requirements — e.g., it is empty or contains
 * a character PSR-16 reserves.
 */
interface InvalidArgumentException extends CacheException
{
}
