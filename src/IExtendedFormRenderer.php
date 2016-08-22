<?php

namespace Instante\ExtendedFormMacros;

use Nette\Forms\Container;
use Nette\Forms\ControlGroup;
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
}
