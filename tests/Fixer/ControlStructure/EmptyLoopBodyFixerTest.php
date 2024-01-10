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
 * @covers \PhpCsFixer\Fixer\ControlStructure\EmptyLoopBodyFixer
 */
final class EmptyLoopBodyFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        if ([] === $config) {
            $this->doTest($expected, $input);

            $this->fixer->configure(['style' => 'braces']);

            if (null === $input) {
                $this->doTest($expected, $input);
            } else {
                $this->doTest($input, $expected);
            }
        } else {
            $this->fixer->configure($config);
            $this->doTest($expected, $input);
        }
    }

    public static function provideFixCases(): iterable
    {
        yield 'simple "while"' => [
            '<?php while(foo());',
            '<?php while(foo()){}',
        ];

        yield 'simple "for"' => [
            '<?php for($i = 0;foo();++$i);',
            '<?php for($i = 0;foo();++$i){}',
        ];

        yield 'simple "foreach"' => [
            '<?php foreach (Foo() as $f);',
            '<?php foreach (Foo() as $f){}',
        ];

        yield '"while" followed by "do-while"' => [
            '<?php while(foo(static function(){})); do{ echo 1; }while(bar());',
            '<?php while(foo(static function(){})){} do{ echo 1; }while(bar());',
        ];

        yield 'empty "while" after "if"' => [
            <<<'EOD'
                <?php
                if ($foo) {
                    echo $bar;
                } while(foo());

                EOD,
            <<<'EOD'
                <?php
                if ($foo) {
                    echo $bar;
                } while(foo()){}

                EOD,
        ];

        yield 'nested and mixed loops' => [
            <<<'EOD'
                <?php

                do {
                    while($foo()) {
                        while(B()); // fix
                        for($i = 0;foo();++$i); // fix

                        for($i = 0;foo();++$i) {
                            foreach (Foo() as $f); // fix
                        }
                    }
                } while(foo());

                EOD,
            <<<'EOD'
                <?php

                do {
                    while($foo()) {
                        while(B()){} // fix
                        for($i = 0;foo();++$i){} // fix

                        for($i = 0;foo();++$i) {
                            foreach (Foo() as $f){} // fix
                        }
                    }
                } while(foo());

                EOD,
        ];

        yield 'not empty "while"' => [
            '<?php while(foo()){ bar(); };',
        ];

        yield 'not empty "for"' => [
            '<?php for($i = 0; foo(); ++$i){ bar(); }',
        ];

        yield 'not empty "foreach"' => [
            '<?php foreach (foo() as $f){ echo 1; }',
        ];

        yield 'test with lot of space' => [
            <<<'EOD'
                <?php while (foo1())
                ;



                echo 1;

                EOD,
            <<<'EOD'
                <?php while (foo1())
                {

                }

                echo 1;

                EOD,
            ['style' => 'semicolon'],
        ];

        yield 'empty "foreach" with comment' => [
            <<<'EOD'
                <?php foreach (Foo() as $f) {
                    // $this->add($f);
                }
                EOD,
        ];
    }
}
