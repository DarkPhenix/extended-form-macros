<?php

use InstanteTests\ExtendedFormMacros\Latte\MacroTester;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Rendering\DefaultFormRenderer;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/MacroTester.inc';

$tester = new MacroTester('{form theForm}{input foo}{input foo:xxx}{/form}');

$foo = mock(BaseControl::class . '[getControl,getControlPart]');
$foo->shouldReceive('getControl')->withNoArgs()->once();
$foo->shouldReceive('getControlPart')->with('xxx')->once();
$tester->getForm()->addComponent($foo, 'foo');
$tester->getForm()->setRenderer(new DefaultFormRenderer);


$tester->render();
