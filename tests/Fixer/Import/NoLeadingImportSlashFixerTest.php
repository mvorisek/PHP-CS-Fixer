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

namespace PhpCsFixer\Tests\Fixer\Import;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Import\NoLeadingImportSlashFixer
 */
final class NoLeadingImportSlashFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                                use A\B;
                EOD,
            <<<'EOD'
                <?php
                                use \A\B;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                use/*1*/A\C;
                EOD,
            <<<'EOD'
                <?php
                                use/*1*/\A\C;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                $a = function(\B\C $a) use ($b){

                                };
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                namespace NS;
                                use A\B;
                EOD,
            <<<'EOD'
                <?php
                                namespace NS;
                                use \A\B;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                namespace NS{
                                    use A\B;
                                }
                                namespace NS2{
                                    use C\D;
                                }
                EOD,
            <<<'EOD'
                <?php
                                namespace NS{
                                    use \A\B;
                                }
                                namespace NS2{
                                    use \C\D;
                                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                namespace Foo {
                                    use A;
                                    use A\X;

                                    new X();
                                }

                                namespace Bar {
                                    use B;
                                    use B\X;

                                    new X();
                                }
                EOD,
            <<<'EOD'
                <?php
                                namespace Foo {
                                    use \A;
                                    use \A\X;

                                    new X();
                                }

                                namespace Bar {
                                    use \B;
                                    use \B\X;

                                    new X();
                                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                namespace Foo\Bar;
                                use Baz;
                                class Foo implements Baz {}
                EOD,
            <<<'EOD'
                <?php
                                namespace Foo\Bar;
                                use \Baz;
                                class Foo implements Baz {}
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                trait SomeTrait {
                                    use \A;
                                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                namespace NS{
                                    use A\B;
                                    trait Tr8A{
                                        use \B, \C;
                                    }
                                }
                                namespace NS2{
                                    use C\D;
                                }
                EOD,
            <<<'EOD'
                <?php
                                namespace NS{
                                    use \A\B;
                                    trait Tr8A{
                                        use \B, \C;
                                    }
                                }
                                namespace NS2{
                                    use \C\D;
                                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                trait Foo {}
                                class Bar {
                                    use \Foo;
                                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    use function a\b;
                                    use const d\e;
                EOD,
            <<<'EOD'
                <?php
                                    use function \a\b;
                                    use const \d\e;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                namespace AAA;
                use some\a\{ClassA, ClassB, ClassC as C,};
                use function some\a\{fn_a, fn_b, fn_c,};
                use const some\a\{ConstA,ConstB,ConstC
                ,
                };
                use const some\Z\{ConstX,ConstY,ConstZ,};

                EOD,
            <<<'EOD'
                <?php
                namespace AAA;
                use \some\a\{ClassA, ClassB, ClassC as C,};
                use function \some\a\{fn_a, fn_b, fn_c,};
                use const \some\a\{ConstA,ConstB,ConstC
                ,
                };
                use const \some\Z\{ConstX,ConstY,ConstZ,};

                EOD,
        ];
    }

    /**
     * @dataProvider provideFixPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixPre80Cases(): iterable
    {
        yield [
            '<?php use /*1*/A\D;',
            '<?php use\/*1*/A\D;',
        ];

        yield 'no space case' => [
            <<<'EOD'
                <?php
                                use Events\Payment\Base as PaymentEvent;
                                use const d\e;
                EOD,
            <<<'EOD'
                <?php
                                use\Events\Payment\Base as PaymentEvent;
                                use const\d\e;
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                            use C;
                            use C\X;

                            namespace Foo {
                                use A;
                                use A\X;

                                new X();
                            }

                            namespace Bar {
                                use B;
                                use B\X;

                                new X();
                            }
                EOD,
            <<<'EOD'
                <?php
                            use \C;
                            use \C\X;

                            namespace Foo {
                                use \A;
                                use \A\X;

                                new X();
                            }

                            namespace Bar {
                                use \B;
                                use \B\X;

                                new X();
                            }
                EOD,
        ];
    }
}
