<?php

/**
 * Cache exception marker.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-attributes
 */

declare(strict_types=1);

namespace X3P0\Attributes\Cache;

use Throwable;

/**
 * Marker interface implemented by every exception a `Cache` can throw, so
 * calling code can catch cache failures without depending on a concrete
 * exception class.
 */
interface CacheException extends Throwable
{
}
