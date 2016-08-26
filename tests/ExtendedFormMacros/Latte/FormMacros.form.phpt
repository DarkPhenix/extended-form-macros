<?php

use InstanteTests\ExtendedFormMacros\Latte\MacroTester;
use Nette\Forms\Form;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/MacroTester.inc';

$tester = new MacroTester('{form theForm foo => bar, bar => baz}{/form}');

$tester->getMockRenderer()->shouldReceive('renderBegin')->with(Mockery::type(Form::class), [
    'foo' => 'bar',
    'bar' => 'baz',
])->once();
$tester->getMockRenderer()->shouldReceive('renderEnd')->withNoArgs()->once();
$tester->render();
