<?php

use InstanteTests\ExtendedFormMacros\Latte\MacroTester;
use Nette\Utils\Html;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/MacroTester.inc';

$tester = new MacroTester('{form theForm}<select n:name=foo attr1=val1, attr2=val2 />{/form}');

$tester->getForm()->addSelect('foo', 'Foo field', []);

$tester->getMockRenderer()->shouldReceive('renderBegin');
$tester->getMockRenderer()->shouldReceive('renderEnd');

$tester->getMockRenderer()->shouldReceive('renderControl')->with(
    $tester->getForm()['foo'],
    ['attr1' => NULL, 'attr2' => NULL],
    NULL
)->once()->andReturn(Html::el('select')->addHtml('<content>'));
$rendered = $tester->render();
Assert::contains('<content>', $rendered);
