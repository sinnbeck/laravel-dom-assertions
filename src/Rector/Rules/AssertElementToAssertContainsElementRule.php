<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions\Rector\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertElementToAssertContainsElementRule extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Convert assertElement() with simple find/contains/containsText/has closures to assertContainsElement()',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
                    ->assertElement('#content', function (AssertElement $element) use ($barcode) {
                        $element->find('h1', function (AssertElement $element) use ($barcode) {
                            $element->containsText($barcode->id);
                        });
                        $element->contains('p', ['class' => 'foo']);
                    });
                    CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
                    ->assertContainsElement('#content h1', ['text' => $barcode->id])
                    ->assertContainsElement('#content p', ['class' => 'foo']);
                    CODE_SAMPLE,
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (! $this->isName($node->name, 'assertElement') || count($node->args) !== 2) {
            return null;
        }

        $closure = $node->args[1]->value;

        if (! $closure instanceof Closure) {
            return null;
        }

        $entries = $this->resolveEntries([$node->args[0]->value], $closure);

        if (! $entries) {
            return null;
        }

        $chain = $node->var;

        foreach ($entries as $entry) {
            $chain = new MethodCall(
                $chain,
                new Identifier('assertContainsElement'),
                [
                    new Arg($this->joinSelectors($entry['selectors'])),
                    new Arg(new Array_($entry['items'])),
                ],
            );
        }

        return $chain;
    }

    /**
     * Recursively converts a closure into a flat list of entries, each becoming one assertContainsElement() call.
     *
     * @param  Expr[]  $selectors  CSS selectors accumulated from parent calls
     * @return array{selectors: Expr[], items: ArrayItem[]}[]|null null if the closure cannot be converted
     */
    private function resolveEntries(array $selectors, Closure $closure): ?array
    {
        $items = $this->extractLeafItems($closure);

        if ($items !== null) {
            return [['selectors' => $selectors, 'items' => $items]];
        }

        $entries = [];

        foreach ($closure->stmts as $stmt) {
            if (! $stmt instanceof Expression || ! $stmt->expr instanceof MethodCall) {
                return null;
            }

            $call = $stmt->expr;

            if ($this->isFindCall($call)) {
                $inner = $this->resolveEntries([...$selectors, $call->args[0]->value], $call->args[1]->value);

                if ($inner === null) {
                    return null;
                }

                $entries = [...$entries, ...$inner];
            } elseif ($this->isContainsCall($call)) {
                $entries[] = [
                    'selectors' => [...$selectors, $call->args[0]->value],
                    'items' => array_values(array_filter($call->args[1]->value->items)),
                ];
            } else {
                return null;
            }
        }

        return $entries ?: null;
    }

    private function isFindCall(MethodCall $call): bool
    {
        return $this->isName($call->name, 'find')
            && count($call->args) === 2
            && $call->args[1]->value instanceof Closure;
    }

    private function isContainsCall(MethodCall $call): bool
    {
        return $this->isName($call->name, 'contains')
            && count($call->args) === 2
            && $call->args[1]->value instanceof Array_;
    }

    /**
     * If the closure only contains containsText/has calls, returns their ArrayItems.
     * Returns null if any other call is found (meaning we need to recurse deeper).
     *
     * @return ArrayItem[]|null
     */
    private function extractLeafItems(Closure $closure): ?array
    {
        $items = [];

        foreach ($closure->stmts as $stmt) {
            if (! $stmt instanceof Expression || ! $stmt->expr instanceof MethodCall) {
                return null;
            }

            $call = $stmt->expr;

            if ($this->isName($call->name, 'containsText') && count($call->args) === 1) {
                $items[] = new ArrayItem($call->args[0]->value, new String_('text'));
            } elseif ($this->isName($call->name, 'has') && count($call->args) === 2) {
                $items[] = new ArrayItem($call->args[1]->value, $call->args[0]->value);
            } else {
                return null;
            }
        }

        return $items ?: null;
    }

    /**
     * Joins multiple selector Expr nodes into one space-separated CSS selector string.
     *
     * @param  Expr[]  $selectors
     */
    private function joinSelectors(array $selectors): Expr
    {
        $combined = $selectors[0];

        foreach (array_slice($selectors, 1) as $selector) {
            $combined = $combined instanceof String_ && $selector instanceof String_
                ? new String_($combined->value.' '.$selector->value)
                : new Concat(new Concat($combined, new String_(' ')), $selector);
        }

        return $combined;
    }
}
