<?php

namespace Instante\ExtendedFormMacros;

use Nette\Forms\Container;
use Nette\Forms\ControlGroup;
use Nette\Forms\Form;
use Nette\Forms\IControl;
use Nette\Forms\IFormRenderer;
use Nette\Utils\Html;

interface IExtendedFormRenderer extends IFormRenderer
{
    /**
     * @param IControl $control
     * @return Html
     */
    public function renderPair(IControl $control);

    /**
     * @param ControlGroup $group
     * @return Html
     */
    public function renderGroup(ControlGroup $group);

    /**
     * @param Container $container
     * @return Html
     */
    public function renderContainer(Container $container);

    /**
     * @param bool $ownOnly - render only global errors (false: include all controls' errors)
     * @return Html
     */
    public function renderGlobalErrors($ownOnly = TRUE);

    /**
     * @param IControl $control
     * @return Html
     */
    public function renderControlErrors(IControl $control);

    /**
     * @param IControl $control
     * @return Html
     */
    public function renderLabel(IControl $control);

    /**
     * @param IControl $control
     * @return Html
     */
    public function renderControl(IControl $control);

    /**
     * @param Form $form
     * @return Html
     */
    public function renderBegin(Form $form);

    /** @return Html */
    public function renderEnd();
}
