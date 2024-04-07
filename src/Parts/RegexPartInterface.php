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
}