<?php

use InstanteTests\ExtendedFormMacros\Latte\MacroTester;
use Latte\CompileException;
use Nette\Utils\Html;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/MacroTester.inc';

$okMacros = [
    'form' => '<form n:name=foo />',
    'label' => '{form theForm}<label n:name=foo />{/form}',
    'input' => '{form theForm}<input n:name=foo />{/form}',
    'select' => '{form theForm}<select n:name=foo />{/form}',
    'textarea' => '{form theForm}<textarea n:name=foo />{/form}',
    'button' => '{form theForm}<button n:name=foo />{/form}',
];

$tester = new MacroTester(
    $okMacros + [
        'wrong' => '{form theForm}<wrong n:name=foo />{/form}',
    ]);

foreach ($okMacros as $key => $val) {
    $tester->compile($key);
}

Assert::exception(function () use ($tester) {
    $tester->compile('wrong');
}, CompileException::class, '~^Unsupported tag <wrong n:name>~');
