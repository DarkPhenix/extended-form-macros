<?php

namespace Instante\ExtendedFormMacros\Latte;

use Latte\CompileException;
use Latte\Compiler;
use Latte\Macros\MacroSet;
use Latte\MacroNode;
use Latte\PhpWriter;
use Nette\Bridges\FormsLatte\FormMacros as NFormMacros;

/**
 * Provides extra form macros:
 *
 * <code>
 * {pair name|$control} as {$form->getRenderer()->renderPair($form['name'])}
 * {group name|$group} as {$form->getRenderer()->renderGroup($form['name'])}
 * {container name|$container} as {$form->getRenderer()->renderContainer($form['name'])}
 * {form.errors [all]]} as {$form->getRenderer()->renderGlobalErrors(!$all)}
 * TODO {form.body} as {$form->getRenderer()->renderBody()}
 * TODO {input.errors name|$control} as {$form->getRenderer()->renderControlErrors($form['name'])}
 * </code>
 *
 * Overrides form macros:
 *
 * <code>
 * {form} to render form begin and end using custom renderer
 *        (FormsLatte\FormMacros uses FormsLatte\Runtime::renderFormBegin directly)
 *
 * {label}
 * {input} to enable custom renderers of labels and controls
 *           (FormsLatte\FormMacros renders the controls directly without renderer processing)
 *
 * </code>
 */
class FormMacros extends NFormMacros
{

    private $renderingDispatcher = '$this->global->formRenderingDispatcher';

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
        $me->addMacro('form', [$me, 'macroForm'], [$me, 'macroFormEnd']);
        $me->addMacro('form.errors', [$me, 'macroFormErrors']);
        $me->addMacro('label', [$me, 'macroLabel'], [$me, 'macroLabelEnd'], NULL, self::AUTO_EMPTY);
        $me->addMacro('input', [$me, 'macroInput']);
        return $me;
    }

    /**
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     */
    public function macroPair(MacroNode $node, PhpWriter $writer)
    {
        return sprintf(
            $this->ln($node)
            . 'echo '
            . $this->renderingDispatcher
            . '->renderPair($this->global->formsStack, %s)',
            $this->renderFormComponent($node, $writer));
    }

    /**
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     */
    public function macroGroup(MacroNode $node, PhpWriter $writer)
    {
        return $writer->write(
            $this->ln($node)
            . 'echo '
            . $this->renderingDispatcher
            . '->renderGroup($this->global->formsStack,'
            . 'is_object(%node.word) ? %node.word : reset($this->global->formsStack)->getGroup(%node.word))');
    }

    /**
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     */
    public function macroContainer(MacroNode $node, PhpWriter $writer)
    {
        // writer intentionally not used - already processed by renderFormComponent
        return sprintf(
            $this->ln($node)
            . 'echo '
            . $this->renderingDispatcher
            . '->renderContainer($this->global->formsStack, %s)',
            $this->renderFormComponent($node, $writer));
    }

    /**
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     * @throws CompileException
     */
    public function macroForm(MacroNode $node, PhpWriter $writer)
    {
        parent::macroForm($node, $writer); //to use argument validations from Nette and set node->replaced
        $name = $node->tokenizer->fetchWord();
        $node->tokenizer->reset();

        $formRetrievalCode = ($name[0] === '$' ? 'is_object(%node.word) ? %node.word : ' : '')
            . '$this->global->uiControl[%node.word]';
        return $writer->write(
            $this->ln($node)
            . 'echo '
            . $this->renderingDispatcher
            . '->renderBegin($form = $_form = $this->global->formsStack[] = '
            . $formRetrievalCode
            . ', %node.array)');
    }

    /**
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     */
    public function macroFormEnd(MacroNode $node, PhpWriter $writer)
    {
        return $writer->write(
            $this->ln($node)
            . 'echo ' . $this->renderingDispatcher . '->renderEnd(array_pop($this->global->formsStack))');
    }

    /**
     * {label ...}
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     * @throws CompileException
     */
    public function macroLabel(MacroNode $node, PhpWriter $writer)
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
        $formattedWords = implode(',', array_map([$writer, 'formatWord'], $words));

        $ctrlExpr = ($name[0] === '$' ? 'is_object(%0.word) ? %0.word : ' : '')
            . 'end($this->global->formsStack)[%0.word]';
        return $writer->write(
            $this->ln($node)
            . '$_label = ' // $_label is used by macroLabelEnd
            . $this->renderingDispatcher
            . "->renderLabel(\$this->global->formsStack, $ctrlExpr, %node.array, [$formattedWords]); "
            . 'echo $_label',
            $name
        );
    }

    /**
     * {input ...}
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     * @throws CompileException
     */
    public function macroInput(MacroNode $node, PhpWriter $writer)
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
        $formattedWords = implode(',', array_map([$writer, 'formatWord'], $words));

        $ctrlExpr = ($name[0] === '$' ? 'is_object(%0.word) ? %0.word : ' : '')
            . 'end($this->global->formsStack)[%0.word]';
        return $writer->write(
            $this->ln($node)
            . 'echo '
            . $this->renderingDispatcher
            . "->renderControl(\$this->global->formsStack, $ctrlExpr, %node.array, [$formattedWords])",
            $name
        );
    }

    /**
     * {form.errors}
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     * @throws CompileException
     */
    public function macroFormErrors(MacroNode $node, PhpWriter $writer)
    {
        if ($node->modifiers) {
            throw new CompileException('Modifiers are not allowed in ' . $node->getNotation());
        }
        $node->replaced = TRUE;
        return $writer->write(
            $this->ln($node)
            . 'echo '
            . $this->renderingDispatcher . '->renderGlobalErrors($this->global->formsStack%0.raw);',
            $node->args === 'all' ? ', FALSE' : ''
        );
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

    private function ln(MacroNode $node)
    {
        return "/* line $node->startLine */\n";
    }
}
