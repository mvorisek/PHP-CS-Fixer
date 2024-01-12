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

namespace PhpCsFixer\Tests\Differ;

use PhpCsFixer\Differ\DiffConsoleFormatter;
use PhpCsFixer\Tests\TestCase;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Differ\DiffConsoleFormatter
 */
final class DiffConsoleFormatterTest extends TestCase
{
    /**
     * @dataProvider provideDiffConsoleFormatterCases
     */
    public function testDiffConsoleFormatter(string $expected, bool $isDecoratedOutput, string $template, string $diff, string $lineTemplate): void
    {
        $diffFormatter = new DiffConsoleFormatter($isDecoratedOutput, $template);

        self::assertSame(
            str_replace(PHP_EOL, "\n", $expected),
            str_replace(PHP_EOL, "\n", $diffFormatter->format($diff, $lineTemplate))
        );
    }

    public static function provideDiffConsoleFormatterCases(): iterable
    {
        yield [
            sprintf(
                '<comment>   ---------- begin diff ----------</comment>'."\n   ".<<<'EOD'

                       <fg=cyan>%s</fg=cyan>
                        no change
                       <fg=red>%s</fg=red>
                       <fg=green>%s</fg=green>
                       <fg=green>%s</fg=green>
                    EOD."\n   ".<<<'EOD'

                    <comment>   ----------- end diff -----------</comment>
                    EOD,
                OutputFormatter::escape('@@ -12,51 +12,151 @@'),
                OutputFormatter::escape('-/**\\'),
                OutputFormatter::escape('+/*\\'),
                OutputFormatter::escape('+A')
            ),
            true,
            sprintf(
                '<comment>   ---------- begin diff ----------</comment>%s%%s%s<comment>   ----------- end diff -----------</comment>',
                PHP_EOL,
                PHP_EOL
            ),
            <<<'EOD'

                @@ -12,51 +12,151 @@
                 no change
                -/**\
                +/*\
                +A

                EOD,
            '   %s',
        ];

        yield [
            <<<'EOD'
                [start]
                |
                EOD.' '.<<<'EOD'

                | @@ -12,51 +12,151 @@
                |  no change
                |
                EOD.'  '.<<<'EOD'

                | -/**\
                | +/*\
                | +A
                |
                EOD.' '.<<<'EOD'

                [end]
                EOD,
            false,
            sprintf('[start]%s%%s%s[end]', PHP_EOL, PHP_EOL),
            <<<'EOD'

                @@ -12,51 +12,151 @@
                 no change
                EOD."\n ".<<<'EOD'

                -/**\
                +/*\
                +A

                EOD,
            '| %s',
        ];

        yield [
            mb_convert_encoding("<fg=red>--- Original</fg=red>\n<fg=green>+ausgefüllt</fg=green>", 'ISO-8859-1'),
            true,
            '%s',
            mb_convert_encoding("--- Original\n+ausgefüllt", 'ISO-8859-1'),
            '%s',
        ];

        yield [
            mb_convert_encoding("<fg=red>--- Original</fg=red>\n<fg=green>+++ New</fg=green>\n<fg=cyan>@@ @@</fg=cyan>\n<fg=red>-ausgefüllt</fg=red>", 'ISO-8859-1'),
            true,
            '%s',
            mb_convert_encoding("--- Original\n+++ New\n@@ @@\n-ausgefüllt", 'ISO-8859-1'),
            '%s',
        ];

        yield [
            mb_convert_encoding("--- Original\n+++ New\n@@ @@\n-ausgefüllt", 'ISO-8859-1'),
            false,
            '%s',
            mb_convert_encoding("--- Original\n+++ New\n@@ @@\n-ausgefüllt", 'ISO-8859-1'),
            '%s',
        ];
    }
}
