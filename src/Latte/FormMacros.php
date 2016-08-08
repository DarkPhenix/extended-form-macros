<?php

namespace Instante\ExtendedFormMacros\Latte;

use Latte\CompileException;
use Latte\Compiler;
use Latte\Macros\MacroSet;
use Latte\MacroNode;
use Latte\PhpWriter;

/**
 * Provides extra form macros:
 * Provides extra form macros:
 *
 * <code>
 * {pair name} as {$form->getRenderer()->renderPair('name')}
 * {group name} as {$form->getRenderer()->renderGroup('name')}
 * {container name} as {$form->getRenderer()->renderContainer('name')}
 * </code>
 */
class FormMacros extends MacroSet
{

    /**
     * @param Compiler $compiler
     * @return MacroSet
     */
    public static function install(Compiler $compiler)
    {
        $me = new static($compiler);
        $me->addMacro('pair', [$me, 'macroPair']);
        $me->addMacro('group', [$me, 'macroGroup']);
        $me->addMacro('container', [$me, 'macroContainer']);
        return $me;
    }

    /**
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     */
    public function macroPair(MacroNode $node, PhpWriter $writer)
    {
        return sprintf('$this->global->formRenderingDispatcher->renderPair($this->global->formsStack, %s)',
            $this->renderFormComponent($node, $writer));
    }

    /**
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     */
    public function macroGroup(MacroNode $node, PhpWriter $writer)
    {
        return $writer->write('$this->global->formRenderingDispatcher->renderGroup($this->global->formsStack,'
            . 'is_object(%node.word) ? %node.word : reset($this->global->formsStack)->getGroup(%node.word))');
    }

    /**
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     */
    public function macroContainer(MacroNode $node, PhpWriter $writer)
    {
        return sprintf('$this->global->formRenderingDispatcher->renderContainer($this->global->formsStack, %s)',
            $this->renderFormComponent($node, $writer));
    }


    protected function renderFormComponent(MacroNode $node, PhpWriter $writer)
    {
        if ($node->modifiers) {
            throw new CompileException('Modifiers are not allowed in ' . $node->getNotation());
        }
        $words = $node->tokenizer->fetchWords();
        if (!$words) {
            throw new CompileException('Missing name in ' . $node->getNotation());
        }
        $node->replaced = TRUE;
        $name = array_shift($words);
        return $writer->write($name[0] === '$' ?
            'is_object(%0.word) ? %0.word : end($this->global->formsStack)[%0.word]' :
            'end($this->global->formsStack)[%0.word]',
            $name
        );
    }
}
