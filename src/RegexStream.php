<?php
namespace Apie\RegexTools;

use Apie\RegexTools\Parts\AnyMatch;
use Apie\RegexTools\Parts\CaptureGroup;
use Apie\RegexTools\Parts\EndOfRegex;
use Apie\RegexTools\Parts\EscapedCharacter;
use Apie\RegexTools\Parts\MatchOrMatch;
use Apie\RegexTools\Parts\OptionalToken;
use Apie\RegexTools\Parts\RegexPartInterface;
use Apie\RegexTools\Parts\RepeatToken;
use Apie\RegexTools\Parts\RepetitionToken;
use Apie\RegexTools\Parts\StartOfRegex;
use Apie\RegexTools\Parts\StaticCharacter;
use IteratorAggregate;
use Traversable;

final class RegexStream implements IteratorAggregate
{
    const METHODMAP = [
        '^' => 'createStartMarker',
        '$' => 'createEndMarker',
        '\\' => 'createEscapedCharacter',
        '(' => 'createCaptureGroup',
        '[' => 'createAnyMatch',
    ];

    private ?RegexPartInterface $previousPart = null;

    private string $fullRegex;

    public function __construct(
        private string $regexToStream
    ) {
        $this->fullRegex = $regexToStream;
    }

    public function nextToken(): ?RegexPartInterface
    {
        $firstCharacter = substr($this->regexToStream, 0, 1);
        if ($firstCharacter === '') {
            return null;
        }
        $method = self::METHODMAP[$firstCharacter] ?? 'createStaticCharacterMarker';
        /** @var RegexPartInterface */
        $part = $this->$method();
        $this->regexToStream = substr($this->regexToStream, $part->getRegexStringLength());
        $part = $this->createRepetition($part);
        $this->previousPart = $part;

        return $part;
    }

    public function getIterator(): Traversable
    {
        return new RegexPartIterator($this->fullRegex);
    }

    private function createRepetition(RegexPartInterface $part): RegexPartInterface
    {
        $firstCharacter = substr($this->regexToStream, 0, 1);
        if ($firstCharacter === '*') {
            $this->regexToStream = substr($this->regexToStream, 1);
            $part = new RepetitionToken($part);
            return $this->createRepetition($part);
        }
        if ($firstCharacter === '+') {
            $this->regexToStream = substr($this->regexToStream, 1);
            $part = new RepetitionToken($part, true);
            return $this->createRepetition($part);
        }
        if ($firstCharacter === '?') {
            $this->regexToStream = substr($this->regexToStream, 1);
            $part = new OptionalToken($part);
            return $this->createRepetition($part);
        }
        if ($firstCharacter === '|') {
            $part = new MatchOrMatch(
                [$part],
                iterator_to_array(new self(substr($this->regexToStream, 1)))
            );
            $this->regexToStream = '';
        }
        if ($firstCharacter === '{') {
            if (preg_match('/^\{\s*(\d*)\s*,\s*(\d*)\s*\}/', $this->regexToStream, $matches)) {
                $this->regexToStream = substr($this->regexToStream, strlen($matches[0]));
                $minimum = $matches[1] === '' ? null : intval($matches[1]);
                $maximum = $matches[2] === '' ? null : intval($matches[2]);
                $part = new RepeatToken($part, $minimum, $maximum, $matches[0]);
                return $this->createRepetition($part);
            }
            if (preg_match('/^\{\s*(\d*)\s*\}/', $this->regexToStream, $matches)) {
                $this->regexToStream = substr($this->regexToStream, strlen($matches[0]));
                $repeatCount = $matches[1] === '' ? null : intval($matches[1]);
                $part = new RepeatToken($part, $repeatCount, $repeatCount, $matches[0]);
                return $this->createRepetition($part);
            }
            // first character is { without } or invalid format => assume static {
        }
        return $part;
    }

    private function createStaticCharacterMarker(): RegexPartInterface
    {
        return new StaticCharacter(substr($this->regexToStream, 0, 1));
    }

    private function createEscapedCharacter(): RegexPartInterface
    {
        if (strlen($this->regexToStream) === 1) {
            return new StaticCharacter('\\');
        }

        return new EscapedCharacter(substr($this->regexToStream, 1, 1));
    }

    private function createStartMarker(): RegexPartInterface
    {
        if ($this->previousPart) {
            return $this->createStaticCharacterMarker();
        }
        return new StartOfRegex();
    }

    private function createCaptureGroup(): RegexPartInterface
    {
        $ptr = 1;
        $counter = 1;
        while ($ptr < strlen($this->regexToStream)) {
            $character = substr($this->regexToStream, $ptr, 1);
            if ($character === '\\') {
                $ptr++;
            }
            $ptr++;
            if ($character === ')') {
                $counter--;
                if ($counter === 0) {
                    break;
                }
            } elseif ($character === '(') {
                $counter++;
            }
        }
        $insideCaptureGroup = substr($this->regexToStream, 1, $ptr - 2);
        return new CaptureGroup(
            iterator_to_array(new self($insideCaptureGroup))
        );
    }

    private function createAnyMatch(): RegexPartInterface
    {
        $ptr = 1;
        $counter = 1;
        while ($ptr < strlen($this->regexToStream)) {
            $character = substr($this->regexToStream, $ptr, 1);
            if ($character === '\\') {
                $ptr++;
            }
            $ptr++;
            if ($character === ']') {
                $counter--;
                if ($counter === 0) {
                    break;
                }
            } elseif ($character === '[') {
                $counter++;
            }
        }
        $insideAnyMatch = substr($this->regexToStream, 1, $ptr - 2);
        return new AnyMatch(
            iterator_to_array(new self($insideAnyMatch))
        );
    }

    private function createEndMarker(): RegexPartInterface
    {
        return new EndOfRegex();
    }
}
