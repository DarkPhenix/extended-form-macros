<?php

use InstanteTests\ExtendedFormMacros\Latte\MacroTester;
use Latte\CompileException;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/MacroTester.inc';

$tester = new MacroTester('{form theForm}<select n:name=foo attr1=val1, attr2=val2>xoxoxo</select>{/form}');

Assert::exception(function () use ($tester) {
    $tester->render();
}, CompileException::class, 'Element <select n:name=...> must not have any content, use empty variant <select n:name=... />');
