<?php
namespace Apie\Tests\RegexTools;

use Apie\RegexTools\CompiledRegularExpression;
use Generator;
use PHPUnit\Framework\TestCase;

class CompiledRegularExpressionTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideRegularExpressions
     */
    public function it_can_define_maximum_and_minimal_string_length_of_a_regex(
        int $expectedMinimal,
        ?int $expectedMaximum,
        string $regex
    ) {
        $testItem = CompiledRegularExpression::createFromRegexWithoutDelimiters($regex);
        $this->assertEquals($expectedMinimal, $testItem->getMinimalPossibleLength());
        $this->assertEquals($expectedMaximum, $testItem->getMaximumPossibleLength());
    }

    public function provideRegularExpressions(): Generator
    {
        yield 'empty regex' => [0, 0, ''];
        yield 'single character' => [1, 1, 'a'];
        yield 'escaped character' => [3, 3, '\\$\\\\\\'];
        yield 'matches empty string only' => [0, 0, ''];
        yield 'capture group' => [2, 2, '((a)a)'];
        yield 'optional' => [0, 1, 'a?'];
        yield 'regex with *' => [0, null, 'a*'];
        yield 'regex with +' => [1, null, 'a+'];
        yield 'repeat static' => [8, 8, 'a{8}'];
        yield 'repeat static (with spaces)' => [8, 8, 'a{ 8 }'];
        yield '[] regex' => [1, 1, '[ab[de]]'];
        yield 'not [] regex' => [1, 1, '[^ab]'];
        yield 'a or b or c' => [1, 1, 'a|b|c'];
    }
}
