<?php
namespace Apie\RegexTools\Parts;

final class AnyMatch implements RegexPartInterface
{
    public function __construct(
        public readonly string $part
    ) {
    }

    public function __toString(): string
    {
        return '[' . $this->part . ']';
    }

    public function getRegexStringLength(): int
    {
        return 2 + strlen($this->part);
    }

    public function getMinimalPossibleLength(): int
    {
        return 1;
    }

    public function getMaximumPossibleLength(): int
    {
        return 1;
    }

    private function makeAllIncludedArray(?callable $callback): array
    {
        $split = mb_str_split($this->part);
        $included = [];
        $nextEscaped = false;
        $skip = 0;
        if ($split[0] === '^') {
            $skip = 1;
        }
        foreach ($split as $key => $character) {
            if ($skip > 0) {
                $skip--;
                continue;
            }
            // escaped character \
            if ($character === '\\') {
                if ($nextEscaped) {
                    $included[] = '\\\\';
                    $nextEscaped = false;
                    continue;
                }
                $nextEscaped = true;
                continue;
            }
            // previous character was \
            if ($nextEscaped) {
                $included[] = '\\' . $character;
                $nextEscaped = false;
                // todo \p and \P
                continue;
            }
            // range
            if (($split[$key + 1] ?? null) === '-' && !empty($split[$key + 2]) && $split[$key + 2] > $character) {
                $minCode = mb_ord($character);
                $maxCode = mb_ord($split[$key + 2]);

                for ($code = $minCode; $code <= $maxCode; $code++) {
                    $chr = mb_chr($code);
                    foreach ($callback($chr) as $added) {
                        $included[] = $added;
                    }
                }
                $skip = 2;
            } else {
                foreach ($callback($character) as $added) {
                    $included[] = $added;
                }
            }
        }
        $included = array_filter(
            array_map(
                function (string $word) {
                    if (mb_substr($word, 0, 1) === '\\') {
                        return mb_substr($word, 1);
                    }
                    return $word;
                },
                array_unique($included)
            ),
            function (string $input) {
                // for some reason mb_strtoupper('ß') returns 'SS'
                return $input !== '' && $input !== 'SS';
            }
        );
        sort($included);
        return $included;
    }

    public function toCaseInsensitive(): RegexPartInterface
    {
        $prefix = '';
        if ($this->part[0] === '^') {
            $prefix = '^';
        }
        $included = $this->makeAllIncludedArray(function (string $chr): array {
            return [$chr, mb_strtoupper($chr), mb_strtolower($chr)];
        });
        return new AnyMatch(
            $prefix . self::createRange($included)
        );
    }

    public function toDotAll(): RegexPartInterface
    {
        $prefix = '';
        if ($this->part[0] === '^') {
            $prefix = '^';
        }
        $included = $this->makeAllIncludedArray(function (string $chr): array {
            if ($chr === '.') {
                return ['.', "\n", "\r"];
            }
            return [$chr];
        });
        return new AnyMatch(
            $prefix . self::createRange($included)
        );
    }

    /**
     * @param list<string> $included
     */
    private static function createRange(array $included): string
    {
        $result = '';
        $currentRange = null;
        $startRange = null;

        while (!empty($included)) {
            $character = array_shift($included);
            if ($startRange === null) {
                $startRange = $character;
                $currentRange = mb_ord($character) + 1;
            } else {
                if (mb_ord($character) === $currentRange) {
                    $currentRange++;
                } else {
                    $result .= self::renderRange($startRange, $currentRange);
                    
                    $startRange = $character;
                    $currentRange = mb_ord($character) + 1;
                }
            }
        }
        if ($startRange !== null) {
            $result .= self::renderRange($startRange, $currentRange);
        }

        return $result;
    }

    private static function renderRange(?string $startRange, ?int $currentRange): string
    {
        $last = mb_chr($currentRange - 1);
        if ($last === $startRange) {
            return preg_quote($startRange);
        }

        if ($currentRange - 1 === mb_ord($startRange) + 1) {
            return preg_quote($startRange) . preg_quote($last);
        }
        
        return preg_quote($startRange) . '-' . preg_quote($last);
    }

    public function removeStartAndEndMarkers(): ?RegexPartInterface
    {
        return $this;
    }
}
