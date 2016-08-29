<?php

use InstanteTests\ExtendedFormMacros\Latte\MacroTester;
use Nette\Forms\Controls\TextInput;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/MacroTester.inc';

$tester = new MacroTester([
    'global' => '{form theForm}{form.errors}{/form}',
    'all' => '{form theForm}{form.errors all}{/form}',
]);

$tester->getForm()->addText('foo', 'Foo field')->addError('E00 this error should not be rendered');

$tester->getForm()->addError('E01 error 1');
$tester->getForm()->addError('E02 error 2');

$tester->getMockRenderer()->shouldReceive('renderBegin');
$tester->getMockRenderer()->shouldReceive('renderEnd');

$tester->getMockRenderer()->shouldReceive('renderGlobalErrors')->with(TRUE)->once()->andReturn('GLOBAL ERRORS');
$tester->getMockRenderer()->shouldReceive('renderGlobalErrors')->with(FALSE)->once()->andReturn('ALL ERRORS');

$rendered = $tester->render('global');
Assert::contains('GLOBAL ERRORS', $rendered);

$rendered = $tester->render('all');
Assert::contains('ALL ERRORS', $rendered);
