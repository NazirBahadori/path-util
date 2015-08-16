<?php

/*
 * This file is part of the webmozart/path-util package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\PathUtil;

use InvalidArgumentException;
use RuntimeException;
use Webmozart\Assert\Assert;

/**
 * Contains utility methods for handling URL strings.
 *
 * The methods in this class are able to deal with URLs.
 *
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Claudio Zizza <claudio@budgegeria.de>
 */
final class Url
{
    /**
     * Turns an URL into a relative path.
     *
     * If the base path is not an absolute path or URL, an exception is thrown.
     * If the Domain name of path and basepath does not match, an exception is thrown.
     *
     * The result is a canonical path. This class is using functionality of Path class
     *
     * @see Path
     *
     * @param string $path     An URL to make relative.
     * @param string $basePath A base path or URL.
     *
     * @return string
     */
    public static function makeRelative($path, $basePath)
    {
        Assert::string($path, 'The path must be a string. Got: %s');
        Assert::string($basePath, 'The base path must be a string. Got: %s');

        list($root, $relativePath) = self::split($path);
        list($baseRoot, $relativeBasePath) = self::split($basePath);

        $path = Path::makeRelative($relativePath, $relativeBasePath);

        if ('' !== $root && '' !== $baseRoot && $root !== $baseRoot) {
            throw new InvalidArgumentException(sprintf(
                'Domain "%s" doesn\'t equal to base "%s".',
                $root,
                $baseRoot
            ));
        }

        return $path;
    }

    /**
     * Splits a part into its root directory and the remainder.
     *
     * If the path has no URL, an empty root directory will be
     * returned.
     *
     * list ($root, $path) = Path::split("http://example.com/webmozart")
     * // => array("http://example.com", "/webmozart")
     *
     * list ($root, $path) = Path::split("http://example.com")
     * // => array("http://example.com", "")
     *
     * @param string $path The URL to split.
     *
     * @return string[] An array with the domain and the remaining
     *                  relative path.
     */
    private static function split($path)
    {
        if ('' === $path) {
            return array('', '');
        }

        // Remember scheme as part of the root, if any
        if (false !== ($pos = strpos($path, '://'))) {
            $scheme = substr($path, 0, $pos + 3);
            $path = substr($path, $pos + 3);

            if (false !== ($pos = strpos($path, '/'))) {
                $domain = substr($path, 0, $pos);
                $path = substr($path, $pos);
            } else {
                // No path, only domain
                $domain = $path;
                $path = '';
            }
        } else {
            $scheme = '';
            $domain = '';
        }

        // At this point, we have $scheme, $domain and $path
        $root = $scheme.$domain;

        return array($root, $path);
    }
}
