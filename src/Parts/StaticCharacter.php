<?php
namespace Apie\RegexTools\Parts;

final class StaticCharacter implements RegexPartInterface
{
    public function __construct(public readonly string $character)
    {
    }

    public function getRegexStringLength(): int
    {
        return strlen($this->character);
    }

    public function __toString(): string
    {
        return $this->character;
    }

    public function getMinimalPossibleLength(): int
    {
        return strlen($this->character);
    }

    public function getMaximumPossibleLength(): int
    {
        return strlen($this->character);
    }
}