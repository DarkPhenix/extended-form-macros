<?php

namespace Instante\ExtendedFormMacros;

use Nette\Forms\Container;
use Nette\Forms\ControlGroup;
use Nette\Forms\IControl;
use Nette\Forms\IFormRenderer;

interface IExtendedFormRenderer extends IFormRenderer
{
    public function renderPair(IControl $control);

    public function renderGroup(ControlGroup $control);

    public function renderContainer(Container $control);
}
