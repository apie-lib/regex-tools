<?php
namespace Apie\RegexTools\Parts;

final class AnyMatch implements RegexPartInterface
{
    /**
     * @param array<int, RegexPartInterface> $part
     */
    public function __construct(
        public readonly array $part
    ) {
    }

    public function __toString(): string
    {
        return '[' . implode('', $this->part) . ']';
    }

    public function getRegexStringLength(): int
    {
        return array_reduce($this->part, function (int $prevValue, RegexPartInterface $part) {
            return $prevValue + $part->getRegexStringLength();
        }, 2);
    }

    public function getMinimalPossibleLength(): int
    {
        return min(
            ...array_map(
                function (RegexPartInterface $part) {
                    return $part instanceof StartOfRegex ? 1 : $part->getMinimalPossibleLength();
                },
                $this->part
            )
        );
    }

    public function getMaximumPossibleLength(): int
    {
        $currentMax = 0;
        foreach ($this->part as $part) {
            $max = $part->getMaximumPossibleLength();
            if ($max === null) {
                return null;
            }
            if ($currentMax < $max) {
                $currentMax = $max;
            }
        }
        return $currentMax;
    }
}