<?php
namespace Apie\RegexTools\Parts;

final class EscapedCharacter implements RegexPartInterface
{
    public function __construct(public readonly string $character)
    {
        assert(strlen($character) === 1);
    }

    public function getRegexStringLength(): int
    {
        return 2;
    }

    public function __toString(): string
    {
        return '\\' . $this->character;
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
        if ($u !== $this->character && !in_array($this->character, ['d', 'D', 'w','W'])) {
            return new CaptureGroup([
                new MatchOrMatch(
                    [$this],
                    [new EscapedCharacter($u)]
                )
            ]);
        }

        return $this;
    }

    public function removeStartAndEndMarkers(): ?RegexPartInterface
    {
        return $this;
    }
}
