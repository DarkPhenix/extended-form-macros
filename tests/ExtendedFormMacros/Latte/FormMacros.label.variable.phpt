<?php

use InstanteTests\ExtendedFormMacros\Latte\MacroTester;
use Nette\Forms\Controls\TextInput;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/MacroTester.inc';

$tester = new MacroTester('{form theForm}{label $ctrl}{/form}');

$foo = $tester->getForm()->addText('foo', 'Foo field');
$tester->getTemplate()->{'ctrl'} = $foo;

$tester->getMockRenderer()->shouldReceive('renderBegin');
$tester->getMockRenderer()->shouldReceive('renderEnd');

$tester->getMockRenderer()->shouldReceive('renderLabel')->with(
    $tester->getForm()['foo'],
    [],
    NULL
)->once();
$tester->render();
