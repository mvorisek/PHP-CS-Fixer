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

namespace PhpCsFixer\Tests\Fixer\Basic;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Basic\SingleLineEmptyBodyFixer
 */
final class SingleLineEmptyBodyFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'non-empty class' => [
            <<<'EOD'
                <?php class Foo
                            {
                                public function bar () {}
                            }
                EOD,
        ];

        yield 'non-empty function body' => [
            <<<'EOD'
                <?php
                                function f1()
                                { /* foo */ }
                                function f2()
                                { /** foo */ }
                                function f3()
                                { // foo
                                }
                                function f4()
                                {
                                    return true;
                                }
                EOD,
        ];

        yield 'classes' => [
            <<<'EOD'
                <?php
                            class Foo {}
                            class Bar extends BarParent {}
                            class Baz implements BazInterface {}
                            abstract class A {}
                            final class F {}
                EOD,
            <<<'EOD'
                <?php
                            class Foo
                            {
                            }
                            class Bar extends BarParent
                            {}
                            class Baz implements BazInterface    {}
                            abstract class A
                            {}
                            final class F
                            {

                            }
                EOD,
        ];

        yield 'multiple functions' => [
            <<<'EOD'
                <?php
                                function notThis1()    { return 1; }
                                function f1() {}
                                function f2() {}
                                function f3() {}
                                function notThis2(){ return 1; }
                EOD,
            <<<'EOD'
                <?php
                                function notThis1()    { return 1; }
                                function f1()
                                {}
                                function f2() {
                                }
                                function f3()
                                {
                                }
                                function notThis2(){ return 1; }
                EOD,
        ];

        yield 'remove spaces' => [
            <<<'EOD'
                <?php
                                function f1() {}
                                function f2() {}
                                function f3() {}
                EOD,
            <<<'EOD'
                <?php
                                function f1() { }
                                function f2() {  }
                                function f3() {    }
                EOD,
        ];

        yield 'add spaces' => [
            <<<'EOD'
                <?php
                                function f1() {}
                                function f2() {}
                                function f3() {}
                EOD,
            <<<'EOD'
                <?php
                                function f1(){}
                                function f2(){}
                                function f3(){}
                EOD,
        ];

        yield 'with return types' => [
            <<<'EOD'
                <?php
                                function f1(): void {}
                                function f2(): \Foo\Bar {}
                                function f3(): ?string {}
                EOD,
            <<<'EOD'
                <?php
                                function f1(): void
                                {}
                                function f2(): \Foo\Bar    {    }
                                function f3(): ?string {


                                }
                EOD,
        ];

        yield 'abstract functions' => [
            <<<'EOD'
                <?php abstract class C {
                                abstract function f1();
                                function f2() {}
                                abstract function f3();
                            }
                            if (true)    {    }
                EOD,
            <<<'EOD'
                <?php abstract class C {
                                abstract function f1();
                                function f2()    {    }
                                abstract function f3();
                            }
                            if (true)    {    }
                EOD,
        ];

        yield 'every token in separate line' => [
            <<<'EOD'
                <?php
                                function
                                foo
                                (
                                )
                                :
                                void {}
                EOD,
            <<<'EOD'
                <?php
                                function
                                foo
                                (
                                )
                                :
                                void
                                {
                                }
                EOD,
        ];

        yield 'comments before body' => [
            <<<'EOD'
                <?php
                                function f1()
                                // foo
                                {}
                                function f2()
                                /* foo */
                                {}
                                function f3()
                                /** foo */
                                {}
                                function f4()
                                /** foo */
                                /** bar */
                                {}
                EOD,
            <<<'EOD'
                <?php
                                function f1()
                                // foo
                                {
                                }
                                function f2()
                                /* foo */
                                {

                                }
                                function f3()
                                /** foo */
                                {
                                }
                                function f4()
                                /** foo */
                                /** bar */
                                {    }
                EOD,
        ];

        yield 'anonymous class' => [
            <<<'EOD'
                <?php
                                $o = new class() {};
                EOD,
            <<<'EOD'
                <?php
                                $o = new class() {
                                };
                EOD,
        ];

        yield 'anonymous function' => [
            <<<'EOD'
                <?php
                                $x = function () {};
                EOD,
            <<<'EOD'
                <?php
                                $x = function () {
                                };
                EOD,
        ];

        yield 'interface' => [
            '<?php interface Foo {}',
            <<<'EOD'
                <?php interface Foo
                                {
                                }
                EOD,
        ];

        yield 'trait' => [
            '<?php trait Foo {}',
            <<<'EOD'
                <?php trait Foo
                                {
                                }
                EOD,
        ];
    }

    /**
     * @requires PHP 8.0
     *
     * @dataProvider provideFix80Cases
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield 'single-line promoted properties' => [
            <<<'EOD'
                <?php class Foo
                                {
                                    public function __construct(private int $x, private int $y) {}
                                }
                EOD,
            <<<'EOD'
                <?php class Foo
                                {
                                    public function __construct(private int $x, private int $y)
                                    {
                                    }
                                }
                EOD,
        ];

        yield 'multi-line promoted properties' => [
            <<<'EOD'
                <?php class Foo
                                {
                                    public function __construct(
                                        private int $x,
                                        private int $y,
                                    ) {}
                                }
                EOD,
            <<<'EOD'
                <?php class Foo
                                {
                                    public function __construct(
                                        private int $x,
                                        private int $y,
                                    ) {
                                    }
                                }
                EOD,
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFix81Cases(): iterable
    {
        yield 'enum' => [
            '<?php enum Foo {}',
            <<<'EOD'
                <?php enum Foo
                                {
                                }
                EOD,
        ];
    }
}
