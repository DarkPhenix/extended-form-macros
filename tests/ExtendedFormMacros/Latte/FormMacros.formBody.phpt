<?php

use InstanteTests\ExtendedFormMacros\Latte\MacroTester;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/MacroTester.inc';

$tester = new MacroTester('{form theForm}{form.body}{/form}');

$tester->getMockRenderer()->shouldReceive('renderBegin');
$tester->getMockRenderer()->shouldReceive('renderEnd');

$tester->getMockRenderer()->shouldReceive('renderBody')->withNoArgs()->once()->andReturn('FORM BODY');

$rendered = $tester->render();
Assert::contains('FORM BODY', $rendered);
