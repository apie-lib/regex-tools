<?php
namespace Apie\RegexTools\Parts;

final class StartOfRegex implements RegexPartInterface
{
    public function __toString(): string
    {
        return '^';
    }

    public function getRegexStringLength(): int
    {
        return 1;
    }

    public function getMinimalPossibleLength(): int
    {
        return 0;
    }

    public function getMaximumPossibleLength(): int
    {
        return 0;
    }
}
