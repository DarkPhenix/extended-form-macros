<?php

use InstanteTests\ExtendedFormMacros\Latte\MacroTester;
use Nette\Utils\Html;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/MacroTester.inc';

$tester = new MacroTester('{form theForm}{label foo}bar{/label}{/form}');

$tester->getForm()->addText('foo', 'Foo field');

$tester->getMockRenderer()->shouldReceive('renderBegin');
$tester->getMockRenderer()->shouldReceive('renderEnd');

$tester->getMockRenderer()->shouldReceive('renderLabel')->andReturn(Html::el('label')->setText('zzz'));
$rendered = $tester->render();
Assert::same('<label>bar</label>', $rendered);
