<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\FormExtensionsBundle\Form\Util;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class DateTimeUtil
{
    /**
     * Get the pattern of localized datetime format for javascript.
     *
     * @param string|null $locale   The locale
     * @param string|null $timezone The timezone
     * @param bool        $date     Check if the date must be displayed
     * @param bool        $time     Check if the time must be displayed
     * @param bool        $seconds  Check if the time must be displayed with seconds
     *
     * @return string
     */
    public static function getJsFormat($locale = null, $timezone = null, $date = true, $time = true, $seconds = false)
    {
        $pattern = static::getPattern($locale, $timezone, $date, $time, $seconds);

        return static::convertToJsFormat($pattern);
    }

    /**
     * Get the pattern of localized datetime format.
     *
     * @param string|null $locale   The locale
     * @param string|null $timezone The timezone
     * @param bool        $date     Check if the date must be displayed
     * @param bool        $time     Check if the time must be displayed
     * @param bool        $seconds  Check if the time must be displayed with seconds
     *
     * @return string
     */
    public static function getPattern($locale = null, $timezone = null, $date = true, $time = true, $seconds = false)
    {
        $formatter = static::getFormatter($locale, $timezone, $date, $time, $seconds);
        $pattern = $formatter->getPattern();

        return $pattern;
    }

    /**
     * Get the intl date formatter with the localized configuration.
     *
     * @param string|null $locale   The locale
     * @param string|null $timezone The timezone
     * @param bool        $date     Check if the date must be displayed
     * @param bool        $time     Check if the time must be displayed
     * @param bool        $seconds  Check if the time must be displayed with seconds
     *
     * @return \IntlDateFormatter
     */
    public static function getFormatter($locale = null, $timezone = null, $date = true, $time = true, $seconds = false)
    {
        $locale = null === $locale ? \Locale::getDefault() : $locale;
        $date_format = \IntlDateFormatter::NONE;
        $time_format = \IntlDateFormatter::NONE;

        if ($date) {
            $date_format = \IntlDateFormatter::SHORT;
        }

        if ($time) {
            $time_format = $seconds ? \IntlDateFormatter::MEDIUM : \IntlDateFormatter::SHORT;
        }

        $formatter = new \IntlDateFormatter(
            $locale,
            $date_format,
            $time_format,
            $timezone,
            \IntlDateFormatter::GREGORIAN,
            null
        );

        $formatter->setLenient(false);

        return $formatter;
    }

    /**
     * Convert the php pattern to javascript pattern.
     *
     * @param string $pattern The php pattern
     *
     * @return string
     */
    public static function convertToJsFormat($pattern)
    {
        if (false === strpos($pattern, 'yyyy')) {
            if (false !== strpos($pattern, 'yy')) {
                $pattern = str_replace('yy', 'yyyy', $pattern);
            } elseif (false !== strpos($pattern, 'y')) {
                $pattern = str_replace('y', 'yyyy', $pattern);
            }
        }

        $pattern = str_replace('d', 'D', $pattern);
        $pattern = str_replace('y', 'Y', $pattern);
        $pattern = str_replace('a', 'A', $pattern);

        return $pattern;
    }
}
