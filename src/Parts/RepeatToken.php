<?php
namespace Apie\RegexTools\Parts;

final class RepeatToken implements RegexPartInterface
{
    public function __construct(
        public readonly RegexPartInterface $part,
        public readonly ?int $minimum,
        public readonly ?int $maximum,
        private readonly ?string $regex = null
    ) {
    }

    public function __toString(): string
    {
        if ($this->minimum === $this->maximum && $this->minimum !== null) {
            return $this->part . '{' . $this->minimum . '}';
        }
        return $this->part . '{' . $this->minimum . ',' . $this->maximum . '}';
    }

    public function getRegexStringLength(): int
    {
        if ($this->regex) {
            return $this->part->getRegexStringLength() + strlen($this->regex);
        }
        return $this->part->getRegexStringLength()
            + 3
            + ($this->minimum === null ? 0 : strlen((string) $this->minimum))
            + ($this->maximum === null ? 0 : strlen((string) $this->maximum));
    }

    public function getMinimalPossibleLength(): int
    {
        return $this->part->getMinimalPossibleLength() * $this->minimum;
    }

    public function getMaximumPossibleLength(): ?int
    {
        $max = $this->part->getMaximumPossibleLength();
        if ($max === null) {
            return null;
        }
        return $this->maximum === null ? null : ($max * $this->maximum);
    }
}
