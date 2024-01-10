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

namespace PhpCsFixer\Tests\Fixer\ControlStructure;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\NoUnneededBracesFixer
 */
final class NoUnneededBracesFixerTest extends AbstractFixerTestCase
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
        yield 'simple sample, last token candidate' => [
            '<?php  echo 1;',
            '<?php { echo 1;}',
        ];

        yield 'minimal sample, first token candidate' => [
            '<?php  // {}',
            '<?php {} // {}',
        ];

        yield [
            <<<'EOD'
                <?php
                                      echo 0;   //
                                    echo 1;
                                    switch($a) {
                                        case 2: echo 3; break;
                                    }
                                    echo 4;  echo 5; //
                EOD,
            <<<'EOD'
                <?php
                                    { { echo 0; } } //
                                    {echo 1;}
                                    switch($a) {
                                        case 2: {echo 3; break;}
                                    }
                                    echo 4; { echo 5; }//
                EOD,
        ];

        yield 'no fixes' => [
            <<<'EOD'
                <?php
                                    foreach($a as $b){}
                                    while($a){}
                                    do {} while($a);

                                    if ($c){}
                                    if ($c){}else{}
                                    if ($c){}elseif($d){}
                                    if ($c) {}elseif($d)/**  */{ } else/**/{  }

                                    try {} catch(\Exception $e) {}

                                    function test(){}
                                    $a = function() use ($c){};

                                    class A extends B {}
                                    interface D {}
                                    trait E {}
                EOD,
        ];

        yield 'no fixes II' => [
            <<<'EOD'
                <?php
                                declare(ticks=1) {
                                // entire script here
                                }
                                #
                EOD,
        ];

        yield 'no fix catch/try/finally' => [
            <<<'EOD'
                <?php
                                    try {

                                    } catch(\Exception $e) {

                                    } finally {

                                    }
                EOD,
        ];

        yield 'no fix namespace block' => [
            <<<'EOD'
                <?php
                                    namespace {
                                    }
                                    namespace A {
                                    }
                                    namespace A\B {
                                    }
                EOD,
        ];

        yield 'provideNoFix7Cases' => [
            <<<'EOD'
                <?php
                                    use some\a\{ClassA, ClassB, ClassC as C};
                                    use function some\a\{fn_a, fn_b, fn_c};
                                    use const some\a\{ConstA, ConstB, ConstC};
                                    use some\x\{ClassD, function CC as C, function D, const E, function A\B};
                                    class Foo
                                    {
                                        public function getBar(): array
                                        {
                                        }
                                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace Foo;
                    function Bar(){}


                EOD,
            <<<'EOD'
                <?php
                namespace Foo {
                    function Bar(){}
                }

                EOD,
            ['namespaces' => true],
        ];

        yield [
            <<<'EOD'
                <?php
                            namespace A5 {
                                function AA(){}
                            }
                            namespace B6 {
                                function BB(){}
                            }
                EOD,
            null,
            ['namespaces' => true],
        ];

        yield [
            <<<'EOD'
                <?php
                            namespace Foo7;
                                function Bar(){}
                EOD."\n            ",
            <<<'EOD'
                <?php
                            namespace Foo7 {
                                function Bar(){}
                            }
                EOD,
            ['namespaces' => true],
        ];

        yield [
            <<<'EOD'
                <?php
                            namespace Foo8\A;
                                function Bar(){}
                             ?>
                EOD,
            <<<EOD
                <?php
                            namespace Foo8\\A\t \t {
                                function Bar(){}
                            } ?>
                EOD,
            ['namespaces' => true],
        ];

        yield [
            <<<'EOD'
                <?php
                            namespace A;
                                class X {}
                EOD."\n            ",
            <<<'EOD'
                <?php
                            namespace A {
                                class X {}
                            }
                EOD,
            ['namespaces' => true],
        ];

        yield [
            <<<'EOD'
                <?php
                            namespace {
                                class X {}
                            }
                EOD,
            null,
            ['namespaces' => true],
        ];
    }

    /**
     * @dataProvider provideFixPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixPre80Cases(): iterable
    {
        yield 'no fixes, offset access syntax with curly braces' => [
            <<<'EOD'
                <?php
                                    echo ${$a};
                                    echo $a{1};
                EOD,
        ];
    }
}
