<?php
namespace Apie\Tests\RegexTools;

use Apie\RegexTools\Parts\AnyMatch;
use Apie\RegexTools\Parts\CaptureGroup;
use Apie\RegexTools\Parts\EndOfRegex;
use Apie\RegexTools\Parts\EscapedCharacter;
use Apie\RegexTools\Parts\MatchOrMatch;
use Apie\RegexTools\Parts\OptionalToken;
use Apie\RegexTools\Parts\RepeatToken;
use Apie\RegexTools\Parts\RepetitionToken;
use Apie\RegexTools\Parts\StartOfRegex;
use Apie\RegexTools\Parts\StaticCharacter;
use Apie\RegexTools\RegexStream;
use Generator;
use PHPUnit\Framework\TestCase;

class RegexStreamTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('regularExpressionProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function i_can_stream_a_regular_expression(array $expected, string $input)
    {
        $testItem = new RegexStream($input);
        $data = iterator_to_array($testItem);
        $this->assertEquals($expected, $data);
    }

    public static function regularExpressionProvider(): Generator
    {
        yield 'empty regex' => [
            [],
            ''
        ];
        yield 'static character' => [
            [new StaticCharacter('a')],
            'a',
        ];
        yield 'escaped characters' => [
            [new EscapedCharacter('$'), new EscapedCharacter('\\'), new StaticCharacter('\\')],
            '\\$\\\\\\'
        ];
        yield 'empty string match only' => [
            [new StartOfRegex(), new EndOfRegex()],
            '^$',
        ];
        yield 'capture group' => [
            [new CaptureGroup([new CaptureGroup([new StaticCharacter('a')]), new StaticCharacter('a')])],
            '((a)a)'
        ];
        yield 'optional' => [
            [new OptionalToken(new StaticCharacter('a'))],
            'a?',
        ];
        yield 'static repetition' => [
            [new RepeatToken(new StaticCharacter('a'), 1, 1, '{1}')],
            'a{1}'
        ];
        yield 'static minimum repetition' => [
            [new RepeatToken(new StaticCharacter('a'), 1, null, '{1,}')],
            'a{1,}'
        ];
        yield 'static maximum repetition' => [
            [new RepeatToken(new StaticCharacter('a'), null, 1, '{,1}')],
            'a{,1}'
        ];
        yield 'static repetition limited range' => [
            [new RepeatToken(new StaticCharacter('a'), 0, 9, '{0,9}')],
            'a{0,9}'
        ];
        yield '2 repetitions in row' => [
            [
                new StartOfRegex(),
                new RepeatToken(new StaticCharacter('a'), 5, 8, '{5,8}'),
                new RepeatToken(new StaticCharacter('b'), 2, 3, '{2,3}'),
                new EndOfRegex(),
            ],
            '^a{5,8}b{2,3}$'
        ];
        yield '* regex' => [
            [new RepetitionToken(new StaticCharacter('a'))],
            'a*',
        ];
        yield '* regex (double)' => [
            [new RepetitionToken(new StaticCharacter('a')), new RepetitionToken(new StaticCharacter('a'))],
            'a*a*',
        ];
        yield '[] regex' => [
            [
                new AnyMatch([
                    new StaticCharacter('a'),
                    new StaticCharacter('b'),
                    new AnyMatch([
                        new StaticCharacter('d'),
                        new StaticCharacter('e')
                    ])
                ])
            ],
            '[ab[de]]',
        ];
        yield 'not [] regex' => [
            [
                new AnyMatch([
                    new StartOfRegex(),
                    new StaticCharacter('a'),
                    new StaticCharacter('b'),
                ])
            ],
            '[^ab]',
        ];
        yield '+ regex' => [
            [new RepetitionToken(new StaticCharacter('a'), true)],
            'a+',
        ];
        yield '+ regex (double)' => [
            [new RepetitionToken(new StaticCharacter('a'), true), new RepetitionToken(new StaticCharacter('a'), true)],
            'a+a+',
        ];
        yield 'a or b or c' => [
            [
                new MatchOrMatch(
                    [new StaticCharacter('a')],
                    [new MatchOrMatch(
                        [new StaticCharacter('b')],
                        [new StaticCharacter('c')]
                    )]
                )
            ],
            'a|b|c',
        ];
    }
}
