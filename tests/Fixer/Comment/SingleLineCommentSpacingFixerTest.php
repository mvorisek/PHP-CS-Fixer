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

namespace PhpCsFixer\Tests\Fixer\Comment;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Comment\SingleLineCommentSpacingFixer
 */
final class SingleLineCommentSpacingFixerTest extends AbstractFixerTestCase
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
        yield 'comment list' => [
            <<<'EOD'
                <?php
                                // following:
                                //     1 :
                                //     2 :

                                # Test:
                                #   - abc
                                #   - fgh

                                # Title:
                                #   | abc1
                                #   | xyz

                                // Point:
                                //  * first point
                                //  * some other point

                                // Matrix:
                                //   [1,2]
                                //   [3,4]
                EOD."\n            ",
        ];

        yield [
            '<?php /*    XYZ */',
            '<?php /*    XYZ   */',
        ];

        yield [
            '<?php // /',
            '<?php ///',
        ];

        yield [
            '<?php // //',
            '<?php ////',
        ];

        yield 'hash open slash asterisk close' => [
            '<?php # A*/',
            '<?php #A*/',
        ];

        yield [
            <<<EOD
                <?php
                // a
                # b
                /* ABC */

                //     \t d
                #\te
                /* f */

                EOD,
            <<<EOD
                <?php
                //a
                #b
                /*ABC*/

                //     \t d
                #\te
                /* f     */

                EOD,
        ];

        yield 'do not fix multi line comments' => [
            <<<'EOD'
                <?php
                /*
                */

                /*A
                B*/

                EOD,
        ];

        yield 'empty double slash' => [
            '<?php //',
        ];

        yield 'empty hash' => [
            '<?php #',
        ];

        yield [
            '<?php /**/',
        ];

        yield [
            '<?php /***/',
        ];

        yield 'do not fix PHPDocs' => [
            "<?php /**\n*/ /**\nX1*/ /**  Y1  */",
        ];

        yield 'do not fix comments looking like PHPDocs' => [
            '<?php /**/ /**X1*/ /**  Y1  */',
        ];

        yield 'do not fix annotation' => [
            <<<'EOD'
                <?php
                namespace PhpCsFixer\Tests\Fixer\Basic;
                new
                #[Foo]
                class extends stdClass {};

                EOD,
        ];
    }
}
