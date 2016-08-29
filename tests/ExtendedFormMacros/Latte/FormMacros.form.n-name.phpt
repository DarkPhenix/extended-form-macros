<?php

use InstanteTests\ExtendedFormMacros\Latte\MacroTester;
use Nette\Forms\Form;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/MacroTester.inc';

$tester = new MacroTester('<form n:name="theForm" foo="bar" bar="baz"></form>');

$tester->getMockRenderer()->shouldReceive('renderBegin')->with(
    Mockery::type(Form::class),
    [
        'foo' => NULL,
        'bar' => NULL,
    ],
    FALSE
)->once();
$tester->getMockRenderer()->shouldReceive('renderEnd')->with(FALSE)->once();
$tester->render();
