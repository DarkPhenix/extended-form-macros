<?php

use InstanteTests\ExtendedFormMacros\Latte\MacroTester;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Rendering\DefaultFormRenderer;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/MacroTester.inc';

$tester = new MacroTester('{form theForm}{label foo}{label foo:xxx}{/form}');

$foo = mock(BaseControl::class . '[getLabel,getLabelPart]');
$foo->shouldReceive('getLabel')->withNoArgs()->once();
$foo->shouldReceive('getLabelPart')->with('xxx')->once();
$tester->getForm()->addComponent($foo, 'foo');
$tester->getForm()->setRenderer(new DefaultFormRenderer);


$tester->render();
