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

namespace PhpCsFixer\Tests\Fixer\LanguageConstruct;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\FunctionToConstantFixer
 */
final class FunctionToConstantFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'Minimal case, alternative casing, alternative statement end.' => [
            '<?php echo PHP_VERSION?>',
            '<?php echo PHPversion()?>',
        ];

        yield 'With embedded comment.' => [
            '<?php echo PHP_VERSION/**/?>',
            '<?php echo phpversion(/**/)?>',
        ];

        yield 'With white space.' => [
            '<?php echo PHP_VERSION      ;',
            '<?php echo phpversion  (  )  ;',
        ];

        yield 'With multi line whitespace.' => [
            <<<'EOD'
                <?php echo
                                PHP_VERSION
                EOD."\n                ".''."\n                ".<<<'EOD'

                                ;
                EOD,
            <<<'EOD'
                <?php echo
                                phpversion
                                (
                                )
                                ;
                EOD,
        ];

        yield 'Global namespaced.' => [
            '<?php echo \PHP_VERSION;',
            '<?php echo \phpversion();',
        ];

        yield 'Wrong number of arguments.' => [
            '<?php phpversion($a);',
        ];

        yield 'Wrong namespace.' => [
            '<?php A\B\phpversion();',
        ];

        yield 'Class creating.' => [
            '<?php new phpversion();',
        ];

        yield 'Class static method call.' => [
            '<?php A::phpversion();',
        ];

        yield 'Class method call.' => [
            '<?php $a->phpversion();',
        ];

        yield 'Overridden function.' => [
            '<?php if (!function_exists("phpversion")){function phpversion(){}}?>',
        ];

        yield 'phpversion only' => [
            '<?php echo PHP_VERSION; echo php_sapi_name(); echo pi();',
            '<?php echo phpversion(); echo php_sapi_name(); echo pi();',
            ['functions' => ['phpversion']],
        ];

        yield 'php_sapi_name only' => [
            '<?php echo phpversion(); echo PHP_SAPI; echo pi();',
            '<?php echo phpversion(); echo php_sapi_name(); echo pi();',
            ['functions' => ['php_sapi_name']],
        ];

        yield 'php_sapi_name in conditional' => [
            '<?php if ("cli" === PHP_SAPI && $a){ echo 123;}',
            '<?php if ("cli" === php_sapi_name() && $a){ echo 123;}',
            ['functions' => ['php_sapi_name']],
        ];

        yield 'pi only' => [
            '<?php echo phpversion(); echo php_sapi_name(); echo M_PI;',
            '<?php echo phpversion(); echo php_sapi_name(); echo pi();',
            ['functions' => ['pi']],
        ];

        yield 'multi line pi' => [
            <<<'EOD'
                <?php
                $a =
                    $b
                    || $c < M_PI
                ;
                EOD,
            <<<'EOD'
                <?php
                $a =
                    $b
                    || $c < pi()
                ;
                EOD,
            ['functions' => ['pi']],
        ];

        yield 'phpversion and pi' => [
            '<?php echo PHP_VERSION; echo php_sapi_name(); echo M_PI;',
            '<?php echo phpversion(); echo php_sapi_name(); echo M_PI;',
            ['functions' => ['pi', 'phpversion']],
        ];

        yield 'diff argument count than native allows' => [
            <<<'EOD'
                <?php
                                    echo phpversion(1);
                                    echo php_sapi_name(1,2);
                                    echo pi(1);
                EOD,
        ];

        yield 'get_class => T_CLASS' => [
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        public function echoClassName($notMe)
                                        {
                                            echo get_class($notMe);
                                            echo __CLASS__/** 1 *//* 2 */;
                                            echo __CLASS__;
                                        }
                                    }

                                    class B
                                    {
                                        use A;
                                    }
                EOD,
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        public function echoClassName($notMe)
                                        {
                                            echo get_class($notMe);
                                            echo get_class(/** 1 *//* 2 */);
                                            echo GET_Class();
                                        }
                                    }

                                    class B
                                    {
                                        use A;
                                    }
                EOD,
        ];

        yield 'get_class with leading backslash' => [
            '<?php __CLASS__;',
            '<?php \get_class();',
        ];

        yield [
            '<?php class A { function B(){ echo static::class; }}',
            '<?php class A { function B(){ echo get_called_class(); }}',
            ['functions' => ['get_called_class']],
        ];

        yield [
            <<<'EOD'
                <?php class A { function B(){
                echo#.
                #0
                static::class#1
                #2
                #3
                #4
                #5
                #6
                ;#7
                }}
                EOD,
            <<<'EOD'
                <?php class A { function B(){
                echo#.
                #0
                get_called_class#1
                #2
                (#3
                #4
                )#5
                #6
                ;#7
                }}
                EOD,
            ['functions' => ['get_called_class']],
        ];

        yield 'get_called_class with leading backslash' => [
            '<?php class A { function B(){echo static::class; }}',
            '<?php class A { function B(){echo \get_called_class(); }}',
            ['functions' => ['get_called_class']],
        ];

        yield 'get_called_class overridden' => [
            '<?php echo get_called_class(1);',
            null,
            ['functions' => ['get_called_class']],
        ];

        yield [
            '<?php class Foo{ public function Bar(){ echo static::class  ; }}',
            '<?php class Foo{ public function Bar(){ echo get_class( $This ); }}',
            ['functions' => ['get_class_this']],
        ];

        yield [
            '<?php class Foo{ public function Bar(){ echo static::class; get_class(1, 2); get_class($a); get_class($a, $b);}}',
            '<?php class Foo{ public function Bar(){ echo get_class($this); get_class(1, 2); get_class($a); get_class($a, $b);}}',
            ['functions' => ['get_class_this']],
        ];

        yield [
            '<?php class Foo{ public function Bar(){ echo static::class /* 0 */  /* 1 */ ;}}',
            '<?php class Foo{ public function Bar(){ echo \get_class( /* 0 */ $this /* 1 */ );}}',
            ['functions' => ['get_class_this']],
        ];

        yield [
            '<?php class Foo{ public function Bar(){ echo static::class; echo __CLASS__; }}',
            '<?php class Foo{ public function Bar(){ echo \get_class((($this))); echo get_class(); }}',
            ['functions' => ['get_class_this', 'get_class']],
        ];

        yield [
            <<<'EOD'
                <?php
                                    class Foo{ public function Bar(){ echo $reflection = new \ReflectionClass(get_class($this->extension)); }}
                                    class Foo{ public function Bar(){ echo $reflection = new \ReflectionClass(get_class($this() )); }}
                EOD,
            null,
            ['functions' => ['get_class_this']],
        ];

        yield [
            "<?php namespace Foo;\nfunction &PHPversion(){}",
        ];
    }

    /**
     * @param array<mixed> $config
     *
     * @dataProvider provideInvalidConfigurationKeysCases
     */
    public function testInvalidConfigurationKeys(array $config): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[function_to_constant\] Invalid configuration: The option "functions" with value array is invalid\.$#');

        $this->fixer->configure($config);
    }

    public static function provideInvalidConfigurationKeysCases(): iterable
    {
        yield [['functions' => ['a']]];

        yield [['functions' => [false => 1]]];

        yield [['functions' => ['abc' => true]]];
    }

    public function testInvalidConfigurationValue(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[function_to_constant\] Invalid configuration: The option "0" does not exist\. Defined options are: "functions"\.$#');

        // @phpstan-ignore-next-line
        $this->fixer->configure(['pi123']);
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield 'first callable class' => [
            '<?php $a = get_class(...);',
        ];
    }
}
