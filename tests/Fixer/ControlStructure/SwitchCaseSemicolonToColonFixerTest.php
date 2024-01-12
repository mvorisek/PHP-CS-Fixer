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
 * @covers \PhpCsFixer\Fixer\ControlStructure\SwitchCaseSemicolonToColonFixer
 */
final class SwitchCaseSemicolonToColonFixerTest extends AbstractFixerTestCase
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
            '<?php
                switch (1) {
                    case f(function () { return; }):
                        break;
                }'."\n                ",
            '<?php
                switch (1) {
                    case f(function () { return; });
                        break;
                }'."\n                ",
        ];

        yield [
            '<?php
                switch ($a) {
                    case 42:
                        break;
                }'."\n                ",
            '<?php
                switch ($a) {
                    case 42;
                        break;
                }'."\n                ",
        ];

        yield [
            '<?php
                switch ($a) {
                    case ["foo" => "bar"]:
                        break;
                }'."\n                ",
            '<?php
                switch ($a) {
                    case ["foo" => "bar"];
                        break;
                }'."\n                ",
        ];

        yield [
            '<?php
                    switch ($a) {
                        case 42:
                            break;
                        case 1:
                            switch ($a) {
                                case 42:
                                    break;
                                default :
                                    echo 1;
                            }
                    }',
            '<?php
                    switch ($a) {
                        case 42;
                            break;
                        case 1:
                            switch ($a) {
                                case 42;
                                    break;
                                default ;
                                    echo 1;
                            }
                    }',
        ];

        yield [
            '<?php
                switch ($a) {
                    case 42:;;// NoEmptyStatementFixer should clean this up (partly)
                        break;
                }'."\n                ",
            '<?php
                switch ($a) {
                    case 42;;;// NoEmptyStatementFixer should clean this up (partly)
                        break;
                }'."\n                ",
        ];

        yield [
            '<?php
                switch ($a) {
                    case $b ? "c" : "d" :
                        break;
                }'."\n                ",
            '<?php
                switch ($a) {
                    case $b ? "c" : "d" ;
                        break;
                }'."\n                ",
        ];

        yield [
            '<?php
                switch ($a) {
                    case $b ? "c" : "d": break;
                }'."\n                ",
            '<?php
                switch ($a) {
                    case $b ? "c" : "d"; break;
                }'."\n                ",
        ];

        yield [
            '<?php
                switch($a) {
                    case (int) $a < 1: {
                        echo "leave ; alone";
                        break;
                    }
                    case ($a < 2)/* test */ : {
                        echo "fix 1";
                        break;
                    }
                    case (3):{
                        echo "fix 2";
                        break;
                    }
                    case /**/(/**/ // test
                        4
                        /**/)//
                        /**/: {
                        echo "fix 3";
                        break;
                    }
                    case (((int)$b) + 4.1) : {
                        echo "fix 4";
                        break;
                    }
                    case ($b + 1) * 2 : {;;
                        echo "leave alone";
                        break;
                    }
                }'."\n                ",
            '<?php
                switch($a) {
                    case (int) $a < 1; {
                        echo "leave ; alone";
                        break;
                    }
                    case ($a < 2)/* test */ ; {
                        echo "fix 1";
                        break;
                    }
                    case (3);{
                        echo "fix 2";
                        break;
                    }
                    case /**/(/**/ // test
                        4
                        /**/)//
                        /**/; {
                        echo "fix 3";
                        break;
                    }
                    case (((int)$b) + 4.1) ; {
                        echo "fix 4";
                        break;
                    }
                    case ($b + 1) * 2 ; {;;
                        echo "leave alone";
                        break;
                    }
                }'."\n                ",
        ];

        yield 'nested switch in switch case' => [
            '<?php
                    switch (1) {
                        case new class {public function A(){echo 1;switch(time()){case 1: echo 2;}}}:break;}'."\n                ",
            '<?php
                    switch (1) {
                        case new class {public function A(){echo 1;switch(time()){case 1; echo 2;}}};break;}'."\n                ",
        ];

        yield [
            '<?php
                switch (1) {
                    case $b ? f(function () { return; }) : new class {public function A(){echo 1;}} :
                        break;
                }'."\n                ",
            '<?php
                switch (1) {
                    case $b ? f(function () { return; }) : new class {public function A(){echo 1;}} ;
                        break;
                }'."\n                ",
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
        yield [
            '<?php
                switch ($a) {
                    case $b ? "c" : "this" ? "is" : "ugly":
                        break;
                }'."\n                ",
            '<?php
                switch ($a) {
                    case $b ? "c" : "this" ? "is" : "ugly";
                        break;
                }'."\n                ",
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield 'Simple match' => [
            '<?php
                    echo match ($a) {
                        default => "foo",
                    };'."\n                ",
        ];

        yield 'Match in switch' => [
            '<?php
                    switch ($foo) {
                        case "bar":
                            echo match ($a) {
                                default => "foo",
                            };
                            break;
                    }'."\n                ",
        ];

        yield 'Match in case value' => [
            '<?php
                    switch ($foo) {
                        case match ($bar) {
                            default => "foo",
                        }: echo "It works!";
                    }'."\n                ",
            '<?php
                    switch ($foo) {
                        case match ($bar) {
                            default => "foo",
                        }; echo "It works!";
                    }'."\n                ",
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

    public static function provideFix81Cases(): iterable
    {
        yield 'enums' => [
            '<?php
enum Suit {
    case Hearts; // do not fix
}

enum UserStatus: string {
  case Pending = "P"; // do not fix

  public function label(): string {
    switch (foo()) {
        case 42: // do fix
            bar();

            $a = new class() {
                public function bar() {
                    switch (foo()) {
                        case 43: // do fix
                        bar();
                    }

                    $expressionResult = match ($condition) {
                        default => baz(),
                    };
                }
            };

            $a->bar();

            break;
    }

    return "label";
  }
}

$expressionResult = match ($condition) {
    default => baz(),
};
',
            '<?php
enum Suit {
    case Hearts; // do not fix
}

enum UserStatus: string {
  case Pending = "P"; // do not fix

  public function label(): string {
    switch (foo()) {
        case 42; // do fix
            bar();

            $a = new class() {
                public function bar() {
                    switch (foo()) {
                        case 43; // do fix
                        bar();
                    }

                    $expressionResult = match ($condition) {
                        default => baz(),
                    };
                }
            };

            $a->bar();

            break;
    }

    return "label";
  }
}

$expressionResult = match ($condition) {
    default => baz(),
};
',
        ];
    }
}
