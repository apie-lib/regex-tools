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

    public function toCaseInsensitive(): RegexPartInterface
    {
        $u = mb_strtoupper($this->character);
        if ($u !== $this->character) {
            return new CaptureGroup([
                new MatchOrMatch(
                    [$this],
                    [new StaticCharacter($u)]
                )
            ]);
        }

        return $this;
    }

    public function toDotAll(): RegexPartInterface
    {
        if ($this->character === '.') {
            return new MatchOrMatch([new StaticCharacter('.')], [new EscapedCharacter('n')]);
        }

        return $this;
    }

    public function removeStartAndEndMarkers(): ?RegexPartInterface
    {
        return $this;
    }
}
