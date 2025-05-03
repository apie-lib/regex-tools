<?php
namespace Apie\RegexTools\Parts;

use Stringable;

interface RegexPartInterface extends Stringable
{
    /**
     * Returns string length of regex part.
     * @internal
     */
    public function getRegexStringLength(): int;

    /**
     * Returns minimal possible length of a string that matches this part.
     */
    public function getMinimalPossibleLength(): int;

    /**
     * Returns maximum possible length of a string that matches this part.
     * Returning null means there a string can be of infinite length and still match the regular expression.
     */
    public function getMaximumPossibleLength(): ?int;

    /**
     * Changes part to make the regular expression case insensitive (similar to i regex modifier).
     */
    public function toCaseInsensitive(): RegexPartInterface;

    /**
     * Changes part to make . match \n as well (similar to s regex modifier).
     */
    public function toDotAll(): RegexPartInterface;

    /**
     * Changes part to remove start and end markers.
     */
    public function removeStartAndEndMarkers(): ?RegexPartInterface;
}
