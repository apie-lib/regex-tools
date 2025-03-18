<?php
namespace Apie\RegexTools\Parts;

final class CaptureGroup implements RegexPartInterface
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
        return '(' . implode('', $this->part) . ')';
    }

    public function getRegexStringLength(): int
    {
        return array_reduce($this->part, function (int $prevValue, RegexPartInterface $part) {
            return $prevValue + $part->getRegexStringLength();
        }, 2);
    }

    public function getMinimalPossibleLength(): int
    {
        return array_sum(
            array_map(
                function (RegexPartInterface $part) {
                    return $part->getMinimalPossibleLength();
                },
                $this->part
            )
        );
        ;
    }

    public function getMaximumPossibleLength(): ?int
    {
        $sum = 0;
        foreach ($this->part as $part) {
            $max = $part->getMaximumPossibleLength();
            if ($max === null) {
                return null;
            }
            $sum += $max;
        }
        return $sum;
    }
}
