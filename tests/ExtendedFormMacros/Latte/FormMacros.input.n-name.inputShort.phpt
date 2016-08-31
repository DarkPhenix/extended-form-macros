<?php

use InstanteTests\ExtendedFormMacros\Latte\MacroTester;
use Nette\Utils\Html;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/MacroTester.inc';

$tester = new MacroTester('{form theForm}<button n:name=foo />{/form}');

$tester->getForm()->addButton('foo', 'Foo button');

$tester->getMockRenderer()->shouldReceive('renderBegin');
$tester->getMockRenderer()->shouldReceive('renderEnd');

$tester->getMockRenderer()->shouldReceive('renderControl')->with(
    $tester->getForm()['foo'],
    [],
    NULL
)->once()->andReturn(Html::el('input'));
Assert::contains('Foo button', $tester->render());
