<?php

namespace Instante\ExtendedFormMacros\Latte;

use Latte\CompileException;
use Latte\Compiler;
use Latte\Macros\MacroSet;
use Latte\MacroNode;
use Latte\PhpWriter;

/**
 * Provides extra form macros:
 *
 * <code>
 * {pair name|$control} as {$form->getRenderer()->renderPair($form['name'])}
 * {group name|$group} as {$form->getRenderer()->renderGroup($form['name'])}
 * {container name|$container} as {$form->getRenderer()->renderContainer($form['name'])}
 * TODO {form.errors $ownOnly=true} as {$form->getRenderer()->renderGlobalErrors($ownOnly)}
 * TODO {form.body} as {$form->getRenderer()->renderBody()}
 * TODO {control.errors name|$control} as {$form->getRenderer()->renderControlErrors($form['name'])}
 * </code>
 *
 * Overrides form macros:
 *
 * <code>
 * TODO {form} to render form begin and end using custom renderer
 *        (FormsLatte\FormMacros uses FormsLatte\Runtime::renderFormBegin directly)
 *
 * TODO {label}
 * TODO {control} to enable custom renderers of labels and controls
 *           (FormsLatte\FormMacros renders the controls directly
 *           without renderer processing)
 *
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
