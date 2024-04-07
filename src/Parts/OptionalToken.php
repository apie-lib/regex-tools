<?php
namespace Apie\RegexTools\Parts;

final class OptionalToken implements RegexPartInterface
{
    public function __construct(
        public readonly RegexPartInterface $part
    ) {
    }

    public function __toString(): string
    {
        return $this->part . '?';
    }

    public function getRegexStringLength(): int
    {
        return $this->part->getRegexStringLength() + 1;
    }

    public function getMinimalPossibleLength(): int
    {
        return 0;
    }

    public function getMaximumPossibleLength(): ?int
    {
        return $this->part->getMaximumPossibleLength();
    }
}