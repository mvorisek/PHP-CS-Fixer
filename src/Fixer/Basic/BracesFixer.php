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

namespace PhpCsFixer\Fixer\Basic;

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\ControlStructure\ControlStructureBracesFixer;
use PhpCsFixer\Fixer\ControlStructure\ControlStructureContinuationPositionFixer;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\LanguageConstruct\DeclareParenthesesFixer;
use PhpCsFixer\Fixer\LanguageConstruct\SingleSpaceAroundConstructFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use PhpCsFixer\Fixer\Whitespace\StatementIndentationFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;

/**
 * Fixer for rules defined in PSR2 ¶4.1, ¶4.4, ¶5.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @deprecated
 */
final class BracesFixer extends AbstractProxyFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface, DeprecatedFixerInterface
{
    /**
     * @internal
     */
    public const LINE_NEXT = 'next';

    /**
     * @internal
     */
    public const LINE_SAME = 'same';

    /**
     * @var null|CurlyBracesPositionFixer
     */
    private $curlyBracesPositionFixer;

    /**
     * @var null|ControlStructureContinuationPositionFixer
     */
    private $controlStructureContinuationPositionFixer;

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'The body of each structure MUST be enclosed by braces. Braces should be properly placed. Body of braces should be properly indented.',
            [
                new CodeSample(
                    <<<'EOD'
                        <?php

                        class Foo {
                            public function bar($baz) {
                                if ($baz = 900) echo "Hello!";

                                if ($baz = 9000)
                                    echo "Wait!";

                                if ($baz == true)
                                {
                                    echo "Why?";
                                }
                                else
                                {
                                    echo "Ha?";
                                }

                                if (is_array($baz))
                                    foreach ($baz as $b)
                                    {
                                        echo $b;
                                    }
                            }
                        }

                        EOD
                ),
                new CodeSample(
                    <<<'EOD'
                        <?php
                        $positive = function ($item) { return $item >= 0; };
                        $negative = function ($item) {
                                        return $item < 0; };

                        EOD,
                    ['allow_single_line_closure' => true]
                ),
                new CodeSample(
                    <<<'EOD'
                        <?php

                        class Foo
                        {
                            public function bar($baz)
                            {
                                if ($baz = 900) echo "Hello!";

                                if ($baz = 9000)
                                    echo "Wait!";

                                if ($baz == true)
                                {
                                    echo "Why?";
                                }
                                else
                                {
                                    echo "Ha?";
                                }

                                if (is_array($baz))
                                    foreach ($baz as $b)
                                    {
                                        echo $b;
                                    }
                            }
                        }

                        EOD,
                    ['position_after_functions_and_oop_constructs' => self::LINE_SAME]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before HeredocIndentationFixer.
     * Must run after ClassAttributesSeparationFixer, ClassDefinitionFixer, EmptyLoopBodyFixer, NoAlternativeSyntaxFixer, NoEmptyStatementFixer, NoUselessElseFixer, SingleLineThrowFixer, SingleSpaceAfterConstructFixer, SingleSpaceAroundConstructFixer, SingleTraitInsertPerStatementFixer.
     */
    public function getPriority(): int
    {
        return 35;
    }

    public function getSuccessorsNames(): array
    {
        return array_keys($this->proxyFixers);
    }

    public function configure(array $configuration = null): void
    {
        parent::configure($configuration);

        $this->getCurlyBracesPositionFixer()->configure([
            'control_structures_opening_brace' => $this->translatePositionOption($this->configuration['position_after_control_structures']),
            'functions_opening_brace' => $this->translatePositionOption($this->configuration['position_after_functions_and_oop_constructs']),
            'anonymous_functions_opening_brace' => $this->translatePositionOption($this->configuration['position_after_anonymous_constructs']),
            'classes_opening_brace' => $this->translatePositionOption($this->configuration['position_after_functions_and_oop_constructs']),
            'anonymous_classes_opening_brace' => $this->translatePositionOption($this->configuration['position_after_anonymous_constructs']),
            'allow_single_line_empty_anonymous_classes' => $this->configuration['allow_single_line_anonymous_class_with_empty_body'],
            'allow_single_line_anonymous_functions' => $this->configuration['allow_single_line_closure'],
        ]);

        $this->getControlStructureContinuationPositionFixer()->configure([
            'position' => self::LINE_NEXT === $this->configuration['position_after_control_structures']
                ? ControlStructureContinuationPositionFixer::NEXT_LINE
                : ControlStructureContinuationPositionFixer::SAME_LINE,
        ]);
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('allow_single_line_anonymous_class_with_empty_body', 'Whether single line anonymous class with empty body notation should be allowed.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder('allow_single_line_closure', 'Whether single line lambda notation should be allowed.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder('position_after_functions_and_oop_constructs', 'Whether the opening brace should be placed on "next" or "same" line after classy constructs (non-anonymous classes, interfaces, traits, methods and non-lambda functions).'))
                ->setAllowedValues([self::LINE_NEXT, self::LINE_SAME])
                ->setDefault(self::LINE_NEXT)
                ->getOption(),
            (new FixerOptionBuilder('position_after_control_structures', 'Whether the opening brace should be placed on "next" or "same" line after control structures.'))
                ->setAllowedValues([self::LINE_NEXT, self::LINE_SAME])
                ->setDefault(self::LINE_SAME)
                ->getOption(),
            (new FixerOptionBuilder('position_after_anonymous_constructs', 'Whether the opening brace should be placed on "next" or "same" line after anonymous constructs (anonymous classes and lambda functions).'))
                ->setAllowedValues([self::LINE_NEXT, self::LINE_SAME])
                ->setDefault(self::LINE_SAME)
                ->getOption(),
        ]);
    }

    protected function createProxyFixers(): array
    {
        $singleSpaceAroundConstructFixer = new SingleSpaceAroundConstructFixer();
        $singleSpaceAroundConstructFixer->configure([
            'constructs_contain_a_single_space' => [],
            'constructs_followed_by_a_single_space' => ['elseif', 'for', 'foreach', 'if', 'match', 'while', 'use_lambda'],
            'constructs_preceded_by_a_single_space' => ['use_lambda'],
        ]);

        $noExtraBlankLinesFixer = new NoExtraBlankLinesFixer();
        $noExtraBlankLinesFixer->configure([
            'tokens' => ['curly_brace_block'],
        ]);

        return [
            $singleSpaceAroundConstructFixer,
            new ControlStructureBracesFixer(),
            $noExtraBlankLinesFixer,
            $this->getCurlyBracesPositionFixer(),
            $this->getControlStructureContinuationPositionFixer(),
            new DeclareParenthesesFixer(),
            new NoMultipleStatementsPerLineFixer(),
            new StatementIndentationFixer(true),
        ];
    }

    private function getCurlyBracesPositionFixer(): CurlyBracesPositionFixer
    {
        if (null === $this->curlyBracesPositionFixer) {
            $this->curlyBracesPositionFixer = new CurlyBracesPositionFixer();
        }

        return $this->curlyBracesPositionFixer;
    }

    private function getControlStructureContinuationPositionFixer(): ControlStructureContinuationPositionFixer
    {
        if (null === $this->controlStructureContinuationPositionFixer) {
            $this->controlStructureContinuationPositionFixer = new ControlStructureContinuationPositionFixer();
        }

        return $this->controlStructureContinuationPositionFixer;
    }

    private function translatePositionOption(string $option): string
    {
        return self::LINE_NEXT === $option
            ? CurlyBracesPositionFixer::NEXT_LINE_UNLESS_NEWLINE_AT_SIGNATURE_END
            : CurlyBracesPositionFixer::SAME_LINE;
    }
}
