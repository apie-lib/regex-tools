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

    public function toCaseInsensitive(): RegexPartInterface
    {
        return new OptionalToken(
            $this->part->toCaseInsensitive()
        );
    }

    public function removeStartAndEndMarkers(): ?RegexPartInterface
    {
        $part = $this->part->removeStartAndEndMarkers();
        return $part ? new OptionalToken($part) : null;
    }
}
