<?php

namespace Instante\ExtendedFormMacros;

use Nette\Forms\IControl;
use Nette\Utils\Html;

interface IControlRenderer
{
    /**
     * @param IControl $control
     * @return Html
     */
    public function renderPair(IControl $control);

    /**
     * @param IControl $control
     * @param bool $renderedDescription if control description was or will be rendered too
     *  (for linking the description by Html element id)
     * @return Html
     */
    public function renderControl(IControl $control, $renderedDescription = FALSE);

    /**
     * @param IControl $control
     * @return Html
     */
    public function renderLabel(IControl $control);
}
