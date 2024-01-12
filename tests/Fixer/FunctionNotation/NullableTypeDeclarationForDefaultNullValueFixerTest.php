<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author HypeMC <hypemc@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\NullableTypeDeclarationForDefaultNullValueFixer
 */
final class NullableTypeDeclarationForDefaultNullValueFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield ['<?php function foo($param = null) {}'];

        yield ['<?php function foo($param1 = null, $param2 = null) {}'];

        yield ['<?php function foo(&$param = null) {}'];

        yield ['<?php function foo(& $param = null) {}'];

        yield ['<?php function foo(/**int*/ $param = null) {}'];

        yield ['<?php function foo(/**int*/ &$param = null) {}'];

        yield ['<?php $foo = function ($param = null) {};'];

        yield ['<?php $foo = function (&$param = null) {};'];

        yield ['<?php function foo(?string $param = null) {}'];

        yield ['<?php function foo(?string $param= null) {}'];

        yield ['<?php function foo(?string $param =null) {}'];

        yield ['<?php function foo(?string $param=null) {}'];

        yield ['<?php function foo(?string $param1 = null, ?string $param2 = null) {}'];

        yield ['<?php function foo(?string &$param = null) {}'];

        yield ['<?php function foo(?string & $param = null) {}'];

        yield ['<?php function foo(?string /*comment*/$param = null) {}'];

        yield ['<?php function foo(?string /*comment*/&$param = null) {}'];

        yield ['<?php function foo(? string $param = null) {}'];

        yield ['<?php function foo(?/*comment*/string $param = null) {}'];

        yield ['<?php function foo(? /*comment*/ string $param = null) {}'];

        yield ['<?php $foo = function (?string $param = null) {};'];

        yield ['<?php $foo = function (?string &$param = null) {};'];

        yield ['<?php function foo(?Baz $param = null) {}'];

        yield ['<?php function foo(?\Baz $param = null) {}'];

        yield ['<?php function foo(?Bar\Baz $param = null) {}'];

        yield ['<?php function foo(?\Bar\Baz $param = null) {}'];

        yield ['<?php function foo(?Baz &$param = null) {}'];

        yield ['<?php function foo(?\Baz &$param = null) {}'];

        yield ['<?php function foo(?Bar\Baz &$param = null) {}'];

        yield ['<?php function foo(?\Bar\Baz &$param = null) {}'];

        yield ['<?php function foo(?Baz & $param = null) {}'];

        yield ['<?php function foo(?\Baz & $param = null) {}'];

        yield ['<?php function foo(?Bar\Baz & $param = null) {}'];

        yield ['<?php function foo(?\Bar\Baz & $param = null) {}'];

        yield ['<?php function foo(?array &$param = null) {}'];

        yield ['<?php function foo(?array & $param = null) {}'];

        yield ['<?php function foo(?callable &$param = null) {}'];

        yield ['<?php function foo(?callable & $param = null) {}'];

        yield ['<?php $foo = function (?Baz $param = null) {};'];

        yield ['<?php $foo = function (?Baz &$param = null) {};'];

        yield ['<?php $foo = function (?Baz & $param = null) {};'];

        yield ['<?php class Test { public function foo(?Bar\Baz $param = null) {} }'];

        yield ['<?php class Test { public function foo(?self $param = null) {} }'];

        yield ['<?php function foo(...$param) {}'];

        yield ['<?php function foo(array ...$param) {}'];

        yield ['<?php function foo(?array ...$param) {}'];

        yield ['<?php function foo(mixed $param = null) {}'];

        yield from self::createBothWaysCases(self::provideBothWaysCases());

        yield [
            '<?php function foo( ?string $param = null) {}',
            '<?php function foo( string $param = null) {}',
        ];

        yield [
            '<?php function foo(/*comment*/?string $param = null) {}',
            '<?php function foo(/*comment*/string $param = null) {}',
        ];

        yield [
            '<?php function foo( /*comment*/ ?string $param = null) {}',
            '<?php function foo( /*comment*/ string $param = null) {}',
        ];

        yield [
            '<?php function foo(string $param = null) {}',
            '<?php function foo(? string $param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo(/*comment*/string $param = null) {}',
            '<?php function foo(?/*comment*/string $param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo(/*comment*/ string $param = null) {}',
            '<?php function foo(? /*comment*/ string $param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield from self::createBothWaysCases(self::provideBothWays80Cases());

        yield [
            '<?php function foo(string $param = null) {}',
            '<?php function foo(string|null $param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo(string $param= null) {}',
            '<?php function foo(string | null $param= null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo(string $param =null) {}',
            '<?php function foo(string| null $param =null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo(string $param=null) {}',
            '<?php function foo(string |null $param=null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo(string $param1 = null, string $param2 = null) {}',
            '<?php function foo(null|string $param1 = null, null | string $param2 = null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo(string &$param = null) {}',
            '<?php function foo(null| string &$param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo(string & $param = null) {}',
            '<?php function foo(null |string & $param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo(string|int /*comment*/$param = null) {}',
            '<?php function foo(string|null|int /*comment*/$param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo(string | int /*comment*/&$param = null) {}',
            '<?php function foo(string | null | int /*comment*/&$param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php $foo = function (string $param = null) {};',
            '<?php $foo = function (NULL | string $param = null) {};',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php $foo = function (string|int &$param = null) {};',
            '<?php $foo = function (string|NULL|int &$param = null) {};',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo(Bar\Baz $param = null) {}',
            '<?php function foo(Bar\Baz|null $param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo(\Bar\Baz $param = null) {}',
            '<?php function foo(null | \Bar\Baz $param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo(Bar\Baz &$param = null) {}',
            '<?php function foo(Bar\Baz | NULL &$param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo(\Bar\Baz &$param = null) {}',
            '<?php function foo(NULL|\Bar\Baz &$param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php $foo = function(array $a = null,
                    array $b = null, array     $c = null, array
                    $d = null) {};',
            '<?php $foo = function(array|null $a = null,
                    array | null $b = null, array | NULL     $c = null, NULL|array
                    $d = null) {};',
            ['use_nullable_type_declaration' => false],
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @requires PHP <8.0
     *
     * @dataProvider provideFixPre81Cases
     */
    public function testFixPre81(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public static function provideFixPre81Cases(): iterable
    {
        yield 'do not fix pre PHP 8.1' => [
            '<?php
                function foo1(&/*comment*/$param = null) {}
                function foo2(?string &/*comment*/$param2 = null) {}'."\n            ",
        ];

        yield [
            '<?php function foo(?string &/* comment */$param = null) {}',
            '<?php function foo(string &/* comment */$param = null) {}',
        ];

        yield [
            '<?php function foo(string &/* comment */$param = null) {}',
            '<?php function foo(?string &/* comment */$param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, array $config = []): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected);
    }

    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php
class Foo
{
    public function __construct(
        protected readonly ?bool $nullable = null,
    ) {}
}
',
        ];

        yield [
            '<?php

            class Foo {
                public function __construct(
                   public readonly ?string $readonlyString = null,
                   readonly public ?int $readonlyInt = null,
                ) {}
            }',
            ['use_nullable_type_declaration' => false],
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFix82Cases
     *
     * @requires PHP 8.2
     */
    public function testFix82(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public static function provideFix82Cases(): iterable
    {
        yield from self::createBothWaysCases(self::provideBothWays82Cases());

        yield [
            '<?php function foo(\Bar\Baz&\Bar\Qux $param = null) {}',
            '<?php function foo((\Bar\Baz&\Bar\Qux)|NULL $param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo(\Bar\Baz&\Bar\Qux $param = null) {}',
            '<?php function foo(null|(\Bar\Baz&\Bar\Qux) $param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo((\Bar\Baz&\Bar\Qux)|(\Bar\Quux&\Bar\Corge) $param = null) {}',
            '<?php function foo((\Bar\Baz&\Bar\Qux)|null|(\Bar\Quux&\Bar\Corge) $param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo((\Bar\Baz&\Bar\Qux)|\Bar\Quux $param = null) {}',
            '<?php function foo(null|(\Bar\Baz&\Bar\Qux)|\Bar\Quux $param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo(    \Bar\Baz&\Bar\Qux     $param = null) {}',
            '<?php function foo(    (    \Bar\Baz&\Bar\Qux    )   |   null     $param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo(\Bar\Baz&\Bar\Qux    $param = null) {}',
            '<?php function foo(null    |    (    \Bar\Baz&\Bar\Qux    )    $param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo((    \Bar\Baz&\Bar\Qux    )|\Bar\Quux     $param = null) {}',
            '<?php function foo(null    |    (    \Bar\Baz&\Bar\Qux    )|\Bar\Quux     $param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo((    \Bar\Baz  &   \Bar\Qux    )|\Bar\Quux     & $param = null) {}',
            '<?php function foo(null    |    (    \Bar\Baz  &   \Bar\Qux    )|\Bar\Quux     & $param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];

        yield [
            '<?php function foo((\Bar\Baz&\Bar\Qux)    |  (\Bar\Quux&\Bar\Corge) $param = null) {}',
            '<?php function foo((\Bar\Baz&\Bar\Qux)  |   null    |  (\Bar\Quux&\Bar\Corge) $param = null) {}',
            ['use_nullable_type_declaration' => false],
        ];
    }

    private static function provideBothWaysCases(): iterable
    {
        yield [
            '<?php function foo(?string $param = null) {}',
            '<?php function foo(string $param = null) {}',
        ];

        yield [
            '<?php function foo(?string $param= null) {}',
            '<?php function foo(string $param= null) {}',
        ];

        yield [
            '<?php function foo(?string $param =null) {}',
            '<?php function foo(string $param =null) {}',
        ];

        yield [
            '<?php function foo(?string $param=null) {}',
            '<?php function foo(string $param=null) {}',
        ];

        yield [
            '<?php function foo(?string $param1 = null, ?string $param2 = null) {}',
            '<?php function foo(string $param1 = null, string $param2 = null) {}',
        ];

        yield [
            '<?php function foo(?string &$param = null) {}',
            '<?php function foo(string &$param = null) {}',
        ];

        yield [
            '<?php function foo(?string & $param = null) {}',
            '<?php function foo(string & $param = null) {}',
        ];

        yield [
            '<?php function foo(?string /*comment*/$param = null) {}',
            '<?php function foo(string /*comment*/$param = null) {}',
        ];

        yield [
            '<?php function foo(?string /*comment*/&$param = null) {}',
            '<?php function foo(string /*comment*/&$param = null) {}',
        ];

        yield [
            '<?php $foo = function (?string $param = null) {};',
            '<?php $foo = function (string $param = null) {};',
        ];

        yield [
            '<?php $foo = function (?string &$param = null) {};',
            '<?php $foo = function (string &$param = null) {};',
        ];

        yield [
            '<?php function foo(?Baz $param = null) {}',
            '<?php function foo(Baz $param = null) {}',
        ];

        yield [
            '<?php function foo(?\Baz $param = null) {}',
            '<?php function foo(\Baz $param = null) {}',
        ];

        yield [
            '<?php function foo(?Bar\Baz $param = null) {}',
            '<?php function foo(Bar\Baz $param = null) {}',
        ];

        yield [
            '<?php function foo(?\Bar\Baz $param = null) {}',
            '<?php function foo(\Bar\Baz $param = null) {}',
        ];

        yield [
            '<?php function foo(?Baz &$param = null) {}',
            '<?php function foo(Baz &$param = null) {}',
        ];

        yield [
            '<?php function foo(?\Baz &$param = null) {}',
            '<?php function foo(\Baz &$param = null) {}',
        ];

        yield [
            '<?php function foo(?Bar\Baz &$param = null) {}',
            '<?php function foo(Bar\Baz &$param = null) {}',
        ];

        yield [
            '<?php function foo(?\Bar\Baz &$param = null) {}',
            '<?php function foo(\Bar\Baz &$param = null) {}',
        ];

        yield [
            '<?php function foo(?Baz & $param = null) {}',
            '<?php function foo(Baz & $param = null) {}',
        ];

        yield [
            '<?php function foo(?\Baz & $param = null) {}',
            '<?php function foo(\Baz & $param = null) {}',
        ];

        yield [
            '<?php function foo(?Bar\Baz & $param = null) {}',
            '<?php function foo(Bar\Baz & $param = null) {}',
        ];

        yield [
            '<?php function foo(?\Bar\Baz & $param = null) {}',
            '<?php function foo(\Bar\Baz & $param = null) {}',
        ];

        yield [
            '<?php function foo(?array &$param = null) {}',
            '<?php function foo(array &$param = null) {}',
        ];

        yield [
            '<?php function foo(?array & $param = null) {}',
            '<?php function foo(array & $param = null) {}',
        ];

        yield [
            '<?php function foo(?callable $param = null) {}',
            '<?php function foo(callable $param = null) {}',
        ];

        yield [
            '<?php $foo = function (?Baz $param = null) {};',
            '<?php $foo = function (Baz $param = null) {};',
        ];

        yield [
            '<?php $foo = function (?Baz &$param = null) {};',
            '<?php $foo = function (Baz &$param = null) {};',
        ];

        yield [
            '<?php $foo = function (?Baz & $param = null) {};',
            '<?php $foo = function (Baz & $param = null) {};',
        ];

        yield [
            '<?php class Test { public function foo(?Bar\Baz $param = null) {} }',
            '<?php class Test { public function foo(Bar\Baz $param = null) {} }',
        ];

        yield [
            '<?php class Test { public function foo(?self $param = null) {} }',
            '<?php class Test { public function foo(self $param = null) {} }',
        ];

        yield [
            '<?php function foo(?iterable $param = null) {}',
            '<?php function foo(iterable $param = null) {}',
        ];

        yield [
            '<?php $foo = function(?array $a = null,
                    ?array $b = null, ?array     $c = null, ?array
                    $d = null) {};',
            '<?php $foo = function(array $a = null,
                    array $b = null, array     $c = null, array
                    $d = null) {};',
        ];

        yield [
            '<?php function foo(?string $param = NULL) {}',
            '<?php function foo(string $param = NULL) {}',
        ];

        yield [
            '<?php $foo = fn (?string $param = null) => null;',
            '<?php $foo = fn (string $param = null) => null;',
        ];

        yield [
            '<?php $foo = fn (?string &$param = null) => null;',
            '<?php $foo = fn (string &$param = null) => null;',
        ];

        yield [
            '<?php $foo = fn (?Baz $param = null) => null;',
            '<?php $foo = fn (Baz $param = null) => null;',
        ];

        yield [
            '<?php $foo = fn (?Baz &$param = null) => null;',
            '<?php $foo = fn (Baz &$param = null) => null;',
        ];

        yield [
            '<?php $foo = fn (?Baz & $param = null) => null;',
            '<?php $foo = fn (Baz & $param = null) => null;',
        ];

        yield [
            '<?php $foo = fn(?array $a = null,
                    ?array $b = null, ?array     $c = null, ?array
                    $d = null) => null;',
            '<?php $foo = fn(array $a = null,
                    array $b = null, array     $c = null, array
                    $d = null) => null;',
        ];
    }

    private static function provideBothWays80Cases(): iterable
    {
        yield [
            '<?php function foo(string|int|null $param = null) {}',
            '<?php function foo(string|int $param = null) {}',
        ];

        yield [
            '<?php function foo(string|int|null $param = NULL) {}',
            '<?php function foo(string|int $param = NULL) {}',
        ];

        yield [
            '<?php function foo(string|int|null /*comment*/$param = null) {}',
            '<?php function foo(string|int /*comment*/$param = null) {}',
        ];

        yield [
            '<?php function foo(string | int|null &$param = null) {}',
            '<?php function foo(string | int &$param = null) {}',
        ];

        yield [
            '<?php function foo(string | int|null & $param = null) {}',
            '<?php function foo(string | int & $param = null) {}',
        ];

        yield [
            '<?php function foo(string | int|null /*comment*/&$param = null) {}',
            '<?php function foo(string | int /*comment*/&$param = null) {}',
        ];

        yield [
            '<?php function foo(string|int|null $param1 = null, string | int|null /*comment*/&$param2 = null) {}',
            '<?php function foo(string|int $param1 = null, string | int /*comment*/&$param2 = null) {}',
        ];

        yield 'trailing comma' => [
            '<?php function foo(?string $param = null,) {}',
            '<?php function foo(string $param = null,) {}',
        ];

        yield 'property promotion' => [
            '<?php class Foo {
                public function __construct(
                    public ?string $paramA = null,
                    protected ?string $paramB = null,
                    private ?string $paramC = null,
                    ?string $paramD = null,
                    $a = []
                ) {}
            }',
            '<?php class Foo {
                public function __construct(
                    public ?string $paramA = null,
                    protected ?string $paramB = null,
                    private ?string $paramC = null,
                    string $paramD = null,
                    $a = []
                ) {}
            }',
        ];

        yield 'attribute' => [
            '<?php function foo(#[AnAttribute] ?string $param = null) {}',
            '<?php function foo(#[AnAttribute] string $param = null) {}',
        ];

        yield 'attributes' => [
            '<?php function foo(
                #[AnAttribute] ?string $a = null,
                #[AnAttribute] ?string $b = null,
                #[AnAttribute] ?string $c = null
            ) {}',
            '<?php function foo(
                #[AnAttribute] string $a = null,
                #[AnAttribute] string $b = null,
                #[AnAttribute] string $c = null
            ) {}',
        ];
    }

    private static function provideBothWays82Cases(): iterable
    {
        yield 'Skip standalone null types' => [
            '<?php function foo(null $param = null) {}',
        ];

        yield 'Skip standalone NULL types' => [
            '<?php function foo(NULL $param = null) {}',
        ];

        yield [
            '<?php function foo((\Bar\Baz&\Bar\Qux)|null $param = null) {}',
            '<?php function foo(\Bar\Baz&\Bar\Qux $param = null) {}',
        ];

        yield [
            '<?php function foo((\Bar\Baz&\Bar\Qux)|\Bar\Quux|null $param = null) {}',
            '<?php function foo((\Bar\Baz&\Bar\Qux)|\Bar\Quux $param = null) {}',
        ];

        yield [
            '<?php function foo((\Bar\Baz&\Bar\Qux)|(\Bar\Quux&\Bar\Corge)|null $param = null) {}',
            '<?php function foo((\Bar\Baz&\Bar\Qux)|(\Bar\Quux&\Bar\Corge) $param = null) {}',
        ];

        yield [
            '<?php function foo(    (\Bar\Baz&\Bar\Qux)|null    $param = null) {}',
            '<?php function foo(    \Bar\Baz&\Bar\Qux    $param = null) {}',
        ];

        yield [
            '<?php function foo((\Bar\Baz&\Bar\Qux)|\Bar\Quux|null &$param = null) {}',
            '<?php function foo((\Bar\Baz&\Bar\Qux)|\Bar\Quux &$param = null) {}',
        ];

        yield [
            '<?php function foo(    (\Bar\Baz&\Bar\Qux)|null    &  $param = null) {}',
            '<?php function foo(    \Bar\Baz&\Bar\Qux    &  $param = null) {}',
        ];

        yield [
            '<?php function foo(    (\Bar\Baz&\Bar\Qux)|null/*comment*/&$param = null) {}',
            '<?php function foo(    \Bar\Baz&\Bar\Qux/*comment*/&$param = null) {}',
        ];
    }

    /**
     * @param iterable<array{string, 1?: string}> $cases
     *
     * @return iterable<array{string, null|string, 2?: array<string, bool>}>
     */
    private static function createBothWaysCases(iterable $cases): iterable
    {
        foreach ($cases as $key => $case) {
            yield $key => $case;

            if (\count($case) > 2) {
                throw new \BadMethodCallException(sprintf('Method "%s" does not support handling "configuration" input yet, please implement it.', __METHOD__));
            }

            $reversed = array_reverse($case);

            yield sprintf('Inversed %s', $key) => [
                $reversed[0],
                $reversed[1] ?? null,
                ['use_nullable_type_declaration' => false],
            ];
        }
    }
}
