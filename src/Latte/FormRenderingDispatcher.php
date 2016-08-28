<?php

namespace Instante\ExtendedFormMacros\Latte;

use Instante\ExtendedFormMacros\IExtendedFormRenderer;
use Latte\RuntimeException;
use Nette\Bridges\FormsLatte\Runtime;
use Nette\Forms\Container;
use Nette\Forms\ControlGroup;
use Nette\Forms\Form;
use Nette\Forms\IControl;
use Nette\InvalidStateException;
use Nette\NotImplementedException;
use Nette\UnexpectedValueException;

class FormRenderingDispatcher
{
    public function renderPair(array $formsStack, IControl $control)
    {
        $this->assertInForm($formsStack, 'pair');
        $this->getExtendedRenderer($formsStack, 'pair')->renderPair($control);
    }

    public function renderGroup(array $formsStack, ControlGroup $group)
    {
        $this->assertInForm($formsStack, 'group')->checkInsideTopLevelForm($formsStack, 'group');
        $this->getExtendedRenderer($formsStack, 'group')->renderGroup($group);
    }

    public function renderContainer(array $formsStack, Container $container)
    {
        $this->assertInForm($formsStack, 'container');
        $this->getExtendedRenderer($formsStack, 'container')->renderContainer($container);
    }

    public function renderBegin(Form $form, array $attrs)
    {
        $renderer = $form->getRenderer();
        if ($renderer instanceof IExtendedFormRenderer) {
            $renderer->renderBegin($form, $attrs);
        } else {
            /** @noinspection PhpInternalEntityUsedInspection */
            Runtime::renderFormBegin($form, $attrs);
        }
    }

    public function renderEnd(Form $form)
    {
        $renderer = $form->getRenderer();
        if ($renderer instanceof IExtendedFormRenderer) {
            $renderer->renderEnd();
        } else {
            /** @noinspection PhpInternalEntityUsedInspection */
            Runtime::renderFormEnd($form);
        }
    }

    public function renderLabel(array $formsStack, IControl $control, array $attrs, array $parts)
    {
        $renderer = reset($formsStack)->getRenderer();
        if ($renderer instanceof IExtendedFormRenderer) {
            $renderer->renderLabel($control, $attrs, $parts ? $parts[0] : NULL);
        } else {
            if ($parts && method_exists($control, 'getLabelPart')) {
                echo $control->getLabelPart($parts[0]);
            } elseif (!$parts && method_exists($control, 'getLabel')) {
                echo $control->getLabel();
            } else {
                throw new InvalidStateException('No getLabel[Part] method available to render ' . get_class($control));
            }
        }
    }

    protected function checkInsideTopLevelForm($formsStack, $macro)
    {
        if (count($formsStack) > 1) {
            throw new RuntimeException(sprintf('Macro %s must not be used in nested form container', $macro));
        }
        return $this;
    }

    protected function assertInForm($formsStack, $macro)
    {
        if (count($formsStack) === 0) {
            throw new RuntimeException(sprintf('Cannot use %s macro outside form', $macro));
        }
        return $this;
    }

    /**
     * @param array $formsStack
     * @param string $macro
     * @return IExtendedFormRenderer
     * @throws RuntimeException
     */
    protected function getExtendedRenderer(array $formsStack, $macro)
    {
        $renderer = $this->getRenderer($formsStack);
        if (!$renderer instanceof IExtendedFormRenderer) {
            throw new RuntimeException(sprintf('%s does not support {%s} macro, please use %s as form renderer',
                get_class($renderer),
                $macro,
                IExtendedFormRenderer::class
            ));
        }
        return $renderer;
    }

    /**
     * @param array $formsStack
     * @return IExtendedFormRenderer
     * @throws RuntimeException
     */
    protected function getRenderer(array $formsStack)
    {
        return reset($formsStack)->getRenderer();
    }
}
