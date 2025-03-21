<?php
namespace Apie\Tests\RegexTools;

use Apie\RegexTools\CompiledRegularExpression;
use Generator;
use PHPUnit\Framework\TestCase;

class CompiledRegularExpressionTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('provideRegularExpressions')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_define_maximum_and_minimal_string_length_of_a_regex(
        int $expectedMinimal,
        ?int $expectedMaximum,
        string $expectedToString,
        string $regex
    ) {
        $testItem = CompiledRegularExpression::createFromRegexWithoutDelimiters($regex);
        $this->assertEquals($expectedMinimal, $testItem->getMinimalPossibleLength());
        $this->assertEquals($expectedMaximum, $testItem->getMaximumPossibleLength());
        $this->assertEquals($expectedToString, $testItem->__toString());
    }

    public static function provideRegularExpressions(): Generator
    {
        yield 'empty regex' => [0, 0, '', ''];
        yield 'match only empty string' => [0, 0, '^$', '^$'];
        yield 'single character' => [1, 1, 'a', 'a'];
        yield 'escaped character' => [3, 3, '\\$\\\\\\', '\\$\\\\\\'];
        yield 'capture group' => [2, 2, '((a)a)', '((a)a)'];
        yield 'optional' => [0, 1, 'a?', 'a?'];
        yield 'regex with *' => [0, null, 'a*', 'a*'];
        yield 'regex with +' => [1, null, 'a+', 'a+'];
        yield 'repeat static' => [8, 8, 'a{8}', 'a{8}'];
        yield 'repeat static (with spaces)' => [8, 8, 'a{8}', 'a{ 8 }'];
        yield 'repeat range' => [8, 10, 'a{8,10}', 'a{8,10}'];
        yield 'repeat range (with spaces)' => [8, 10, 'a{8,10}', 'a{ 8 , 10 }'];
        yield '[] regex' => [1, 1, '[ab[de]]', '[ab[de]]'];
        yield 'not [] regex' => [1, 1, '[^ab]', '[^ab]'];
        yield 'a or b or c' => [1, 1, 'a|b|c', 'a|b|c'];
    }
}
