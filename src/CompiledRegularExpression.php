<?php
namespace Apie\RegexTools;

use Apie\RegexTools\Parts\EndOfRegex;
use Apie\RegexTools\Parts\RegexPartInterface;
use Apie\RegexTools\Parts\StartOfRegex;
use Stringable;

final class CompiledRegularExpression implements Stringable
{
    /**
     * @var array<int, RegexPartInterface>
     */
    private array $parts;

    private function __construct(RegexPartInterface... $parts)
    {
        $this->parts = $parts;
    }

    public static function createFromRegexWithoutDelimiters(string $regex): self
    {
        return new self(...(new RegexStream($regex)));
    }

    public function hasStartOfRegexMarker(): bool
    {
        return reset($this->parts) instanceof StartOfRegex;
    }

    public function hasEndOfRegexMarker(): bool
    {
        return end($this->parts) instanceof EndOfRegex;
    }

    public function getMinimalPossibleLength(): int
    {
        return array_sum(
            array_map(
                function (RegexPartInterface $part) {
                    return $part->getMinimalPossibleLength();
                },
                $this->parts
            )
        );
    }

    public function getMaximumPossibleLength(): ?int
    {
        $sum = 0;
        foreach ($this->parts as $part) {
            $max = $part->getMaximumPossibleLength();
            if ($max === null) {
                return null;
            }
            $sum += $max;
        }
        return $sum;
    }

    public function __toString(): string
    {
        return implode('', $this->parts);
    }
}
