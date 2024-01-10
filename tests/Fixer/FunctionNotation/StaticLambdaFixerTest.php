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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\StaticLambdaFixer
 */
final class StaticLambdaFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'sample' => [
            "<?php\n\$a = static function () use (\$b)\n{   echo \$b;\n};",
            "<?php\n\$a = function () use (\$b)\n{   echo \$b;\n};",
        ];

        yield 'minimal double fix case' => [
            '<?php $a=static function(){};$b=static function(){};',
            '<?php $a=function(){};$b=function(){};',
        ];

        yield [
            '<?php $a  /**/  =   /**/     static function(){};',
            '<?php $a  /**/  =   /**/     function(){};',
        ];

        yield [
            '<?php $a  /**/  =   /**/ static function(){};',
            '<?php $a  /**/  =   /**/ function(){};',
        ];

        yield [
            '<?php $a  /**/  =   /**/static function(){};',
            '<?php $a  /**/  =   /**/function(){};',
        ];

        yield [
            '<?php $a=static fn() => null;$b=static fn() => null;',
            '<?php $a=fn() => null;$b=fn() => null;',
        ];

        yield [
            '<?php $a  /**/  =   /**/     static fn() => null;',
            '<?php $a  /**/  =   /**/     fn() => null;',
        ];

        yield [
            '<?php $a  /**/  =   /**/ static fn() => null;',
            '<?php $a  /**/  =   /**/ fn() => null;',
        ];

        yield [
            '<?php $a  /**/  =   /**/static fn() => null; echo $this->foo();',
            '<?php $a  /**/  =   /**/fn() => null; echo $this->foo();',
        ];

        yield [
            '<?php $a  /**/  =   /**/ static fn() => null ?> <?php echo $this->foo();',
            '<?php $a  /**/  =   /**/ fn() => null ?> <?php echo $this->foo();',
        ];

        yield [
            <<<'EOD'
                <?php
                                    class B
                                    {
                                        public function C()
                                        {
                                            $a = fn () => var_dump($this);
                                            $a();
                                        }
                                    }
                EOD,
        ];

        yield [
            '<?php static fn($a = ["foo" => "bar"]) => [];',
            '<?php fn($a = ["foo" => "bar"]) => [];',
        ];

        yield [
            <<<'EOD'
                <?php class Foo {
                                    public function getNames()
                                    {
                                        return \array_map(
                                            static fn ($item) => $item->getName(),
                                            $this->getItems()
                                        );
                                    }
                                }
                EOD,
            <<<'EOD'
                <?php class Foo {
                                    public function getNames()
                                    {
                                        return \array_map(
                                            fn ($item) => $item->getName(),
                                            $this->getItems()
                                        );
                                    }
                                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php class Foo {
                                    public function getNames()
                                    {
                                        return \array_map(
                                            fn ($item) => $item->getName(1, $this->foo()),
                                            $this->getItems()
                                        );
                                    }
                                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    class A
                                    {
                                        public function B()
                                        {
                                            $a = function () {
                                                $b = 'this';
                                                var_dump($$b);
                                            };
                                            $a();
                                        }
                                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    class B
                                    {
                                        public function C()
                                        {
                                            $a = function () {
                                                var_dump($THIS);
                                            };
                                            $a();
                                        }
                                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    class D
                                    {
                                        public function E()
                                        {
                                            $a = function () {
                                                $c = include __DIR__.'/return_this.php';
                                                var_dump($c);
                                            };

                                            $a();
                                        }
                                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    class F
                                    {
                                        public function G()
                                        {
                                            $a = function () {
                                                $d = include_once __DIR__.'/return_this.php';
                                                var_dump($d);
                                            };
                                            $a();
                                        }
                                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    class H
                                    {
                                        public function I()
                                        {
                                            $a = function () {
                                                $e = require_once __DIR__.'/return_this.php';
                                                var_dump($e);
                                            };
                                            $a();
                                        }
                                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    class J
                                    {
                                        public function K()
                                        {
                                            $a = function () {
                                                $f = require __DIR__.'/return_this.php';
                                                var_dump($f);
                                            };
                                            $a();
                                        }
                                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    class L
                                    {
                                        public function M()
                                        {
                                            $a = function () {
                                                $g = 'this';
                                                $h = ${$g};
                                                var_dump($h);
                                            };
                                            $a();
                                        }
                                    }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    class N
                                    {
                                        public function O()
                                        {
                                            $a = function () {
                                                $a = [0 => 'this'];
                                                var_dump(${$a[0]});
                                            };
                                        }
                                    }
                EOD,
        ];

        yield [
            '<?php function test(){} test();',
        ];

        yield [
            <<<'EOD'
                <?php abstract class P
                                {
                                    function test0(){}
                                    public function test1(){}
                                    protected function test2(){}
                                    protected abstract function test3();
                                    private function test4(){}
                                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                class Q
                {
                    public function abc()
                    {
                        $b = function () {
                            $c = eval('return $this;');
                            var_dump($c);
                        };

                        $b();
                    }
                }

                $b = new Q();
                $b->abc();

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php

                                class A {}
                                class B extends A {
                                    public function foo()
                                    {
                                        $c = function () {
                                            return parent::foo();
                                        };
                                    }
                                }
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                    class B
                                    {
                                        public function C()
                                        {
                                            return array_map(
                                                fn () => $this,
                                                []
                                            );
                                        }
                                    }
                EOD,
        ];
    }
}
