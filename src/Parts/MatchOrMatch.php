<?php
namespace Apie\RegexTools\Parts;

final class MatchOrMatch implements RegexPartInterface
{
    /**
     * @param array<int, RegexPartInterface> $part1
     * @param array<int, RegexPartInterface> $part2
     */
    public function __construct(
        public readonly array $part1,
        public readonly array $part2
    ) {
    }

    public function __toString(): string
    {
        $callback = function (RegexPartInterface $part) {
            return $part->__toString();
        };

        return implode(
            '',
            array_map(
                $callback,
                $this->part1
            )
        ) . '|'
        . implode(
            '',
            array_map(
                $callback,
                $this->part2
            )
        );
    }

    public function getRegexStringLength(): int
    {
        return array_reduce([...$this->part1, ...$this->part2], function (int $prevValue, RegexPartInterface $part) {
            return $prevValue + $part->getRegexStringLength();
        }, 1);
    }

    public function getMinimalPossibleLength(): int
    {
        $part1 = array_sum(
            array_map(
                function (RegexPartInterface $part) {
                    return $part->getMinimalPossibleLength();
                },
                $this->part1
            )
        );
        $part2 = array_sum(
            array_map(
                function (RegexPartInterface $part) {
                    return $part->getMinimalPossibleLength();
                },
                $this->part2
            )
        );
        return min($part1, $part2);
    }

    public function getMaximumPossibleLength(): ?int
    {
        $sum1 = 0;
        foreach ($this->part1 as $part) {
            $max = $part->getMaximumPossibleLength();
            if ($max === null) {
                return null;
            }
            $sum1 += $max;
        }
        $sum2 = 0;
        foreach ($this->part2 as $part) {
            $max = $part->getMaximumPossibleLength();
            if ($max === null) {
                return null;
            }
            $sum2 += $max;
        }
        return max($sum1, $sum2);
    }

    public function toCaseInsensitive(): RegexPartInterface
    {
        return new MatchOrMatch(
            array_map(
                function (RegexPartInterface $part) {
                    return $part->toCaseInsensitive();
                },
                $this->part1
            ),
            array_map(
                function (RegexPartInterface $part) {
                    return $part->toCaseInsensitive();
                },
                $this->part2
            )
        );
    }

    public function toDotAll(): RegexPartInterface
    {
        return new MatchOrMatch(
            array_map(
                function (RegexPartInterface $part) {
                    return $part->toDotAll();
                },
                $this->part1
            ),
            array_map(
                function (RegexPartInterface $part) {
                    return $part->toDotAll();
                },
                $this->part2
            )
        );
    }

    public function removeStartAndEndMarkers(): ?RegexPartInterface
    {
        return new MatchOrMatch(
            array_filter(
                array_map(
                    function (RegexPartInterface $part) {
                        return $part->removeStartAndEndMarkers();
                    },
                    $this->part1
                )
            ),
            array_filter(
                array_map(
                    function (RegexPartInterface $part) {
                        return $part->removeStartAndEndMarkers();
                    },
                    $this->part2
                )
            )
        );
    }
}
