<?php
namespace Apie\RegexTools\Parts;

final class RepetitionToken implements RegexPartInterface
{
    public function __construct(
        public readonly RegexPartInterface $part,
        public readonly bool $minimalOnce = false
    ) {
    }

    public function __toString(): string
    {
        return $this->part . ($this->minimalOnce ? '+' : '*');
    }

    public function getRegexStringLength(): int
    {
        return $this->part->getRegexStringLength() + 1;
    }

    public function getMinimalPossibleLength(): int
    {
        return ($this->minimalOnce ? 1 : 0);
    }

    public function getMaximumPossibleLength(): ?int
    {
        return null;
    }
}
