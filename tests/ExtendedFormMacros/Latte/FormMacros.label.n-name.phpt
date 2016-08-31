<?php

use InstanteTests\ExtendedFormMacros\Latte\MacroTester;
use Nette\Utils\Html;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/MacroTester.inc';

$tester = new MacroTester('{form theForm}<label n:name="foo:w1:w2" attr1=val1 attr2=val2 />{/form}');

$tester->getForm()->addText('foo', 'Foo field');

$tester->getMockRenderer()->shouldReceive('renderBegin');
$tester->getMockRenderer()->shouldReceive('renderEnd');

$tester->getMockRenderer()->shouldReceive('renderLabel')->with(
    $tester->getForm()['foo'],
    ['attr1' => NULL, 'attr2' => NULL],
    'w1'
)->once()->andReturn(Html::el('label')->addText('hello'));
$rendered = $tester->render();
Assert::contains('hello', $rendered);
