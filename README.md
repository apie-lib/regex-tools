<img src="https://raw.githubusercontent.com/apie-lib/apie-lib-monorepo/main/docs/apie-logo.svg" width="100px" align="left" />
<h1>regex-tools</h1>






 [![Latest Stable Version](https://poser.pugx.org/apie/regex-tools/v)](https://packagist.org/packages/apie/regex-tools) [![Total Downloads](https://poser.pugx.org/apie/regex-tools/downloads)](https://packagist.org/packages/apie/regex-tools) [![Latest Unstable Version](https://poser.pugx.org/apie/regex-tools/v/unstable)](https://packagist.org/packages/apie/regex-tools) [![License](https://poser.pugx.org/apie/regex-tools/license)](https://packagist.org/packages/apie/regex-tools) [![PHP Composer](https://apie-lib.github.io/projectCoverage/coverage-regex-tools.svg)](https://apie-lib.github.io/projectCoverage/regex-tools/index.html)  

[![PHP Composer](https://github.com/apie-lib/regex-tools/actions/workflows/php.yml/badge.svg?event=push)](https://github.com/apie-lib/regex-tools/actions/workflows/php.yml)

This package is part of the [Apie](https://github.com/apie-lib) library.
The code is maintained in a monorepo, so PR's need to be sent to the [monorepo](https://github.com/apie-lib/apie-lib-monorepo/pulls)

## Documentation
Parses PHP regular expressions into an ordered sequence of inspectable tokens (literals,
groups, repetitions, anchors, escapes) and computes metadata such as the minimal and
maximum possible match length. No Apie or framework dependency.

### Standalone usage
```bash
composer require apie/regex-tools
```

Use `Apie\RegexTools\CompiledRegularExpression::createFromRegexWithoutDelimiters()` to
parse a pattern body and inspect it:
```php
use Apie\RegexTools\CompiledRegularExpression;

$compiled = CompiledRegularExpression::createFromRegexWithoutDelimiters('^[A-Z]{2,4}\d+$');
$min = $compiled->getMinimalPossibleLength();
$max = $compiled->getMaximumPossibleLength(); // null when unbounded
```

Iterate the individual tokens with `Apie\RegexTools\RegexPartIterator`, which yields
`Apie\RegexTools\Parts\RegexPartInterface` implementations such as `StaticCharacter`,
`CaptureGroup`, `RepeatToken`, and `OptionalToken`. `apie/regex-value-objects` builds on
this package to validate and describe regex-based value objects.
