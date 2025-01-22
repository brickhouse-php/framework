<?php

namespace Brickhouse\Support;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;

class StringHelper implements \Stringable
{
    private static null|Inflector $_inflector = null;

    protected Inflector $inflector {
        get {
            self::$_inflector ??= InflectorFactory::create()->build();
            return self::$_inflector;
        }
    }

    public final function __construct(
        public readonly string $value
    ) {}

    /**
     * Creates a new `StringHelper` from the given string.
     *
     * @param string $value
     *
     * @return static
     */
    public static function from(string $value): static
    {
        return new static($value);
    }

    /**
     * Appends the given value after the string.
     *
     * @param string $subject       String to place after the current string.
     *
     * @return static
     */
    public function append(string $subject): static
    {
        return new static($this->value . $subject);
    }

    /**
     * Appends the given value after the string, if the given condition is met.
     *
     * @param callable(string $value):bool  $condition  Condition to meet before appending `$subject` after the current string.
     * @param string                        $subject    String to place after the current string, if `$condition` returns `true`.
     *
     * @return static
     */
    public function appendIf(callable $condition, string $subject): static
    {
        if ($condition($this->value)) {
            return new static($this->value . $subject);
        }

        return new static($this->value);
    }

    /**
     * Capitalizes the first letter of the string.
     *
     * @return static
     */
    public function capitalize(): static
    {
        if (empty($this->value)) {
            return new static($this->value);
        }

        $firstCharacter = strtoupper($this->value[0]);
        $rest = substr($this->value, 1);

        return new static($firstCharacter . $rest);
    }

    /**
     * Limits the length of the string to the given length, and appends an ellipsis if the length is exceeded.
     *
     * If the string is truncated, but is still longer with the appended ellipsis than the original value,
     * the original value is returned instead. In practice, what this means:
     *
     * ```php
     * $subject = "Let's get crazy.";
     * $truncated = StringHelper::from($subject)->ellipsis(15);
     *
     * echo $truncated;
     * // Would print "Let's get crazy.", as "Let's get crazy..." is longer which defeats the purpose.
     * ```
     *
     * @param int       $length         Defines the maximum length of the string, before the string is truncated.
     * @param string    $suspension     Defines what symbol(s) to use as the ellipsis. Defaults to `'...'`.
     *
     * @return static
     */
    public function ellipsis(int $length, string $suspension = '...'): static
    {
        $truncated = substr($this->value, 0, $length);

        // If the truncated string would be longer with the ellipsis than if it hadn't been truncated,
        // the truncation would be useless. So, we make sure that the truncation was "worth it",
        // i.e. that the removed string value is at least as long as the ellipsis.
        if (strlen($truncated) > strlen($this->value) - strlen($suspension)) {
            return new static($this->value);
        }

        return new static($truncated . $suspension);
    }

    /**
     * Ensure that the string ends with the given value. If not, it is appended.
     *
     * @param string $end   String value to append onto the string, if it doesn't already end with that value.
     *
     * @return static
     */
    public function end(string $end): static
    {
        $value = $this->value;
        if (!str_ends_with($value, $end)) {
            $value .= $end;
        }

        return new static($value);
    }

    /**
     * Converts the string to be lower-case.
     *
     * @return static
     */
    public function lower(): static
    {
        return new static(strtolower($this->value));
    }

    /**
     * Prepends the given value in front of the string.
     *
     * @param string $subject       String to place in front of the current string.
     *
     * @return static
     */
    public function prepend(string $subject): static
    {
        return new static($subject . $this->value);
    }

    /**
     * Prepends the given value in front of the string, if the given condition is met.
     *
     * @param callable(string $value):bool  $condition  Condition to meet before prepending `$subject` in front of the current string.
     * @param string                        $subject    String to place in front of the current string, if `$condition` returns `true`.
     *
     * @return static
     */
    public function prependIf(callable $condition, string $subject): static
    {
        if ($condition($this->value)) {
            return new static($subject . $this->value);
        }

        return new static($this->value);
    }

    /**
     * Converts the given string to be plural.
     *
     * @return static
     */
    public function pluralize(): static
    {
        $value = $this->inflector->pluralize($this->value);

        return new static($value);
    }

    /**
     * Removes the given string value, only if the value appears at the end of the string.
     *
     * @param string $subject   String value to remove from the end of the current string value.
     *
     * @return static
     */
    public function removeEnd(string $subject): static
    {
        if (str_ends_with($this->value, $subject)) {
            $snippedString = substr($this->value, 0, strlen($this->value) - strlen($subject));

            return new static($snippedString);
        }

        return $this;
    }

    /**
     * Removes the given string value, only if the value appears at the start of the string.
     *
     * @param string $subject   String value to remove from the start of the current string value.
     *
     * @return static
     */
    public function removeStart(string $subject): static
    {
        if (str_starts_with($this->value, $subject)) {
            $snippedString = substr($this->value, strlen($subject));

            return new static($snippedString);
        }

        return $this;
    }

    /**
     * Replaces a given string within the string.
     *
     * @param string    $search         The string value which should be replacement.
     * @param string    $replacement    The string value to replace matched substrings with.
     * @param bool      $caseSensitive  Defines whether the search should be case-sensitive or case-insensitive.
     *
     * @return static
     */
    public function replace(string $search, string $replacement, bool $caseSensitive = true): static
    {
        if ($caseSensitive) {
            $value = str_replace($search, $replacement, $this->value);
        } else {
            $value = str_ireplace($search, $replacement, $this->value);
        }

        return new static($value);
    }

    /**
     * Converts the string to an URL-friendly slug.
     *
     * @param string $delimiter     Custom delimiter to use between words. Defaults to `-`.
     *
     * @return static
     */
    public function slug(string $delimiter = '-'): static
    {
        $value = $this->snake($delimiter)->replace(" ", "");

        return new static(htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE));
    }

    /**
     * Ensure that the string starts with the given value. If not, it is prepended.
     *
     * @param string $start     String value to prepend onto the string, if it doesn't already start with that value.
     *
     * @return static
     */
    public function start(string $start): static
    {
        $value = $this->value;
        if (!str_starts_with($value, $start)) {
            $value = $start . $value;
        }

        return new static($value);
    }

    /**
     * Convert the string into snake-case (`snake_case`).
     *
     * @param string $delimiter     Custom delimiter to use between words. Defaults to `_`.
     *
     * @return static
     */
    public function snake(string $delimiter = '_'): static
    {
        $value = preg_replace_callback(
            '/(?:\s*)[A-Z]/',
            fn(array $matches) => $delimiter . strtolower(trim($matches[0])),
            $this->value
        );

        $value = ltrim($value, $delimiter);

        return new static($value);
    }

    /**
     * Convert the string into title case (`Title Case`).
     *
     * @return static
     */
    public function title(): static
    {
        $value = preg_replace_callback(
            '/\b\w/',
            fn(array $matches) => strtoupper($matches[0]),
            $this->value
        );

        return new static($value);
    }

    /**
     * Trims the string of whitespace (or the given characters, if not default) on both ends.
     *
     * @param string $characters    Defines a list of characters to trim from both ends of the string.
     *
     * @return static
     */
    public function trim(string $characters = " \n\r\t\v\0"): static
    {
        return new static(trim($this->value, $characters));
    }

    /**
     * Converts the string to be upper-case.
     *
     * @return static
     */
    public function upper(): static
    {
        return new static(strtoupper($this->value));
    }

    /**
     * Gets the string value of the helper.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
