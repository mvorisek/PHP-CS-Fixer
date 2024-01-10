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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Denis Sokolov <denis@sokolov.cc>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer
 */
final class FunctionDeclarationFixerTest extends AbstractFixerTestCase
{
    /**
     * @var array<string, string>
     */
    private static $configurationClosureSpacingNone = ['closure_function_spacing' => FunctionDeclarationFixer::SPACING_NONE];

    /**
     * @var array<string, string>
     */
    private static $configurationArrowSpacingNone = ['closure_fn_spacing' => FunctionDeclarationFixer::SPACING_NONE];

    public function testInvalidConfigurationClosureFunctionSpacing(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches(
            '#^\[function_declaration\] Invalid configuration: The option "closure_function_spacing" with value "neither" is invalid\. Accepted values are: "none", "one"\.$#'
        );

        $this->fixer->configure(['closure_function_spacing' => 'neither']);
    }

    public function testInvalidConfigurationClosureFnSpacing(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches(
            '#^\[function_declaration\] Invalid configuration: The option "closure_fn_spacing" with value "neither" is invalid\. Accepted values are: "none", "one"\.$#'
        );

        $this->fixer->configure(['closure_fn_spacing' => 'neither']);
    }

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
        yield [
            // non-PHP test
            'function foo () {}',
        ];

        yield [
            '<?php function foo() {}',
            '<?php function	foo() {}',
        ];

        yield [
            '<?php function foo() {}',
            '<?php function foo	() {}',
        ];

        yield [
            '<?php function foo() {}',
            '<?php function foo () {}',
        ];

        yield [
            '<?php function foo() {}',
            <<<'EOD'
                <?php function
                foo () {}
                EOD,
        ];

        yield [
            '<?php function ($i) {};',
            '<?php function($i) {};',
        ];

        yield [
            '<?php function _function() {}',
            '<?php function _function () {}',
        ];

        yield [
            '<?php function foo($a, $b = true) {}',
            '<?php function foo($a, $b = true){}',
        ];

        yield [
            '<?php function foo($a, $b = true) {}',
            '<?php function foo($a, $b = true)    {}',
        ];

        yield [
            <<<'EOD'
                <?php function foo($a)
                {}
                EOD,
        ];

        yield [
            '<?php function ($a) use ($b) {};',
            '<?php function ($a) use ($b)     {};',
        ];

        yield [
            '<?php $foo = function ($foo) use ($bar, $baz) {};',
            '<?php $foo = function ($foo) use($bar, $baz) {};',
        ];

        yield [
            '<?php $foo = function ($foo) use ($bar, $baz) {};',
            '<?php $foo = function ($foo)use ($bar, $baz) {};',
        ];

        yield [
            '<?php $foo = function ($foo) use ($bar, $baz) {};',
            '<?php $foo = function ($foo)use($bar, $baz) {};',
        ];

        yield [
            '<?php function &foo($a) {}',
            '<?php function &foo( $a ) {}',
        ];

        yield [
            <<<'EOD'
                <?php function foo($a)
                	{}
                EOD,
            <<<'EOD'
                <?php function foo( $a)
                	{}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                    function foo(
                        $a,
                        $b,
                        $c
                    ) {}
                EOD,
        ];

        yield [
            '<?php $function = function () {};',
            '<?php $function = function(){};',
        ];

        yield [
            '<?php $function("");',
        ];

        yield [
            '<?php function ($a) use ($b) {};',
            '<?php function($a)use($b) {};',
        ];

        yield [
            '<?php function ($a) use ($b) {};',
            '<?php function($a)         use      ($b) {};',
        ];

        yield [
            '<?php function ($a) use ($b) {};',
            '<?php function ($a) use ( $b ) {};',
        ];

        yield [
            '<?php function &($a) use ($b) {};',
            '<?php function &(  $a   ) use (   $b      ) {};',
        ];

        yield [
            <<<'EOD'
                <?php
                    interface Foo
                    {
                        public function setConfig(ConfigInterface $config);
                    }
                EOD,
        ];

        // do not remove multiline space before { when end of previous line is a comment
        yield [
            <<<'EOD'
                <?php
                function foo() // bar
                {              // baz
                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo() /* bar */
                {              /* baz */
                }
                EOD,
        ];

        yield [
            // non-PHP test
            'function foo () {}',
            null,
            self::$configurationClosureSpacingNone,
        ];

        yield [
            '<?php function foo() {}',
            '<?php function	foo() {}',
            self::$configurationClosureSpacingNone,
        ];

        yield [
            '<?php function foo() {}',
            '<?php function foo () {}',
            self::$configurationClosureSpacingNone,
        ];

        yield [
            '<?php function foo() {}',
            '<?php function foo	() {}',
            self::$configurationClosureSpacingNone,
        ];

        yield [
            '<?php function foo() {}',
            <<<'EOD'
                <?php function
                foo () {}
                EOD,
            self::$configurationClosureSpacingNone,
        ];

        yield [
            '<?php function($i) {};',
            null,
            self::$configurationClosureSpacingNone,
        ];

        yield [
            '<?php function _function() {}',
            '<?php function _function () {}',
            self::$configurationClosureSpacingNone,
        ];

        yield [
            '<?php function foo($a, $b = true) {}',
            '<?php function foo($a, $b = true){}',
            self::$configurationClosureSpacingNone,
        ];

        yield [
            '<?php function foo($a, $b = true) {}',
            '<?php function foo($a, $b = true)    {}',
            self::$configurationClosureSpacingNone,
        ];

        yield [
            <<<'EOD'
                <?php function foo($a)
                {}
                EOD,
            null,
            self::$configurationClosureSpacingNone,
        ];

        yield [
            '<?php function($a) use ($b) {};',
            '<?php function ($a) use ($b)     {};',
            self::$configurationClosureSpacingNone,
        ];

        yield [
            '<?php $foo = function($foo) use ($bar, $baz) {};',
            '<?php $foo = function ($foo) use($bar, $baz) {};',
            self::$configurationClosureSpacingNone,
        ];

        yield [
            '<?php $foo = function($foo) use ($bar, $baz) {};',
            '<?php $foo = function ($foo)use ($bar, $baz) {};',
            self::$configurationClosureSpacingNone,
        ];

        yield [
            '<?php $foo = function($foo) use ($bar, $baz) {};',
            '<?php $foo = function ($foo)use($bar, $baz) {};',
            self::$configurationClosureSpacingNone,
        ];

        yield [
            '<?php function &foo($a) {}',
            '<?php function &foo( $a ) {}',
            self::$configurationClosureSpacingNone,
        ];

        yield [
            <<<'EOD'
                <?php function foo($a)
                	{}
                EOD,
            <<<'EOD'
                <?php function foo( $a)
                	{}
                EOD,
            self::$configurationClosureSpacingNone,
        ];

        yield [
            <<<'EOD'
                <?php
                    function foo(
                        $a,
                        $b,
                        $c
                    ) {}
                EOD,
            null,
            self::$configurationClosureSpacingNone,
        ];

        yield [
            '<?php $function = function() {};',
            '<?php $function = function (){};',
            self::$configurationClosureSpacingNone,
        ];

        yield [
            '<?php $function("");',
            null,
            self::$configurationClosureSpacingNone,
        ];

        yield [
            '<?php function($a) use ($b) {};',
            '<?php function ($a)use($b) {};',
            self::$configurationClosureSpacingNone,
        ];

        yield [
            '<?php function($a) use ($b) {};',
            '<?php function ($a)         use      ($b) {};',
            self::$configurationClosureSpacingNone,
        ];

        yield [
            '<?php function($a) use ($b) {};',
            '<?php function ($a) use ( $b ) {};',
            self::$configurationClosureSpacingNone,
        ];

        yield [
            '<?php function&($a) use ($b) {};',
            '<?php function &(  $a   ) use (   $b      ) {};',
            self::$configurationClosureSpacingNone,
        ];

        yield [
            <<<'EOD'
                <?php
                    interface Foo
                    {
                        public function setConfig(ConfigInterface $config);
                    }
                EOD,
            null,
            self::$configurationClosureSpacingNone,
        ];

        // do not remove multiline space before { when end of previous line is a comment
        yield [
            <<<'EOD'
                <?php
                function foo() // bar
                {              // baz
                }
                EOD,
            null,
            self::$configurationClosureSpacingNone,
        ];

        yield [
            <<<'EOD'
                <?php
                function foo() /* bar */
                {              /* baz */
                }
                EOD,
            null,
            self::$configurationClosureSpacingNone,
        ];

        yield [
            <<<'EOD'
                <?php function #
                foo#
                 (#
                 ) #
                {#
                }#
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    $b = static function ($a) {
                                        echo $a;
                                    };
                EOD,
            <<<'EOD'
                <?php
                                    $b = static     function( $a )   {
                                        echo $a;
                                    };
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    $b = static function($a) {
                                        echo $a;
                                    };
                EOD,
            <<<'EOD'
                <?php
                                    $b = static     function ( $a )   {
                                        echo $a;
                                    };
                EOD,
            self::$configurationClosureSpacingNone,
        ];

        yield ['<?php use function Foo\bar; bar ( 1 );'];

        yield ['<?php use function some\test\{fn_a, fn_b, fn_c};'];

        yield ['<?php use function some\test\{fn_a, fn_b, fn_c} ?>'];

        yield ['<?php use function Foo\bar; bar ( 1 );', null, self::$configurationClosureSpacingNone];

        yield ['<?php use function some\test\{fn_a, fn_b, fn_c};', null, self::$configurationClosureSpacingNone];

        yield ['<?php use function some\test\{fn_a, fn_b, fn_c} ?>', null, self::$configurationClosureSpacingNone];

        yield [
            '<?php fn ($i) => null;',
            '<?php fn($i) => null;',
        ];

        yield [
            '<?php fn ($a) => null;',
            '<?php fn ($a)     => null;',
        ];

        yield [
            '<?php $fn = fn () => null;',
            '<?php $fn = fn()=> null;',
        ];

        yield [
            '<?php fn &($a) => null;',
            '<?php fn &(  $a   ) => null;',
        ];

        yield [
            '<?php fn($i) => null;',
            null,
            self::$configurationArrowSpacingNone,
        ];

        yield [
            '<?php fn($a) => null;',
            '<?php fn ($a)      => null;',
            self::$configurationArrowSpacingNone,
        ];

        yield [
            '<?php $fn = fn() => null;',
            '<?php $fn = fn ()=> null;',
            self::$configurationArrowSpacingNone,
        ];

        yield [
            '<?php $fn("");',
            null,
            self::$configurationArrowSpacingNone,
        ];

        yield [
            '<?php fn&($a) => null;',
            '<?php fn &(  $a   ) => null;',
            self::$configurationArrowSpacingNone,
        ];

        yield [
            '<?php fn&($a,$b) => null;',
            '<?php fn &(  $a,$b  ) => null;',
            self::$configurationArrowSpacingNone,
        ];

        yield [
            '<?php $b = static fn ($a) => $a;',
            '<?php $b = static     fn( $a )   => $a;',
        ];

        yield [
            '<?php $b = static fn($a) => $a;',
            '<?php $b = static     fn ( $a )   => $a;',
            self::$configurationArrowSpacingNone,
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixPhp80Cases
     *
     * @requires PHP 8.0
     */
    public function testFixPhp80(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public static function provideFixPhp80Cases(): iterable
    {
        yield [
            '<?php function ($i) {};',
            '<?php function(   $i,   ) {};',
        ];

        yield [
            <<<'EOD'
                <?php function (
                                $a,
                                $b,
                                $c,
                            ) {};
                EOD,
            <<<'EOD'
                <?php function(
                                $a,
                                $b,
                                $c,
                            ) {};
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php function foo(
                                $a,
                                $b,
                                $c,
                            ) {}
                EOD,
            <<<'EOD'
                <?php function foo    (
                                $a,
                                $b,
                                $c,
                            ){}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    $b = static function ($a,$b) {
                                        echo $a;
                                    };
                EOD,
            <<<'EOD'
                <?php
                                    $b = static     function(  $a,$b,   )   {
                                        echo $a;
                                    };
                EOD,
        ];

        yield [
            '<?php fn&($a,$b) => null;',
            '<?php fn &(  $a,$b,   ) => null;',
            self::$configurationArrowSpacingNone,
        ];

        yield [
            <<<'EOD'
                <?php
                                function ($a) use ($b) {};
                                function ($y) use (
                                    $b,
                                    $c,
                                ) {};
                EOD,
            <<<'EOD'
                <?php
                                function ($a) use ($b  ,  )     {};
                                function ($y) use (
                                    $b,
                                    $c,
                                ) {};
                EOD,
        ];

        yield [
            '<?php function ($i,) {};',
            '<?php function(   $i,   ) {};',
            ['trailing_comma_single_line' => true],
        ];
    }
}
