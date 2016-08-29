<?php

use InstanteTests\ExtendedFormMacros\Latte\MacroTester;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/MacroTester.inc';

$tester = new MacroTester('{form theForm}{input.errors foo}{/form}');

$foo = $tester->getForm()->addText('foo', 'Foo field');

$tester->getMockRenderer()->shouldReceive('renderBegin');
$tester->getMockRenderer()->shouldReceive('renderEnd');

$tester->getMockRenderer()->shouldReceive('renderControlErrors')->with($foo)->once()->andReturn('CONTROL ERRORS');

$rendered = $tester->render();
Assert::contains('CONTROL ERRORS', $rendered);
