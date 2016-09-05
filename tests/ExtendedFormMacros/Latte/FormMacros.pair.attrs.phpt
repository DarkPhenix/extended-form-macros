<?php

use Instante\ExtendedFormMacros\PairAttributes;
use InstanteTests\ExtendedFormMacros\Latte\MacroTester;
use Nette\Forms\Controls\TextInput;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/MacroTester.inc';

$tester = new MacroTester(
    '{form theForm}{pair foo class=>"pclass", input-class=>"iclass", label-class=>"lclass"}{/form}'
);

$tester->getForm()->addText('foo', 'Foo field');

$tester->getMockRenderer()->shouldReceive('renderBegin');
$tester->getMockRenderer()->shouldReceive('renderEnd');

$tester->getMockRenderer()->shouldReceive('renderPair')->withArgs(function (TextInput $input, PairAttributes $attrs) {
    return
        $attrs->container['class'] === 'pclass'
        && $attrs->input['class'] === 'iclass'
        && $attrs->label['class'] === 'lclass';
})->once();
$tester->render();
