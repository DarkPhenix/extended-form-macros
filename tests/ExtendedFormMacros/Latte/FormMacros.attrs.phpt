<?php

use InstanteTests\ExtendedFormMacros\Latte\MacroTester;
use Nette\Forms\Form;
use Nette\Forms\IControl;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/MacroTester.inc';

$tester = new MacroTester(<<<EOT
{form theForm}
{input foo attr1=>val1}
{label bar attr2=>val2}
{/form}
EOT
);
$f = $tester->getForm();
$f->addText('foo');
$f->addText('bar');
$f->addText('baz');

$tester->getMockRenderer()->shouldReceive('renderBegin')->once();
$tester->getMockRenderer()->shouldReceive('renderEnd')->once();
$tester->getMockRenderer()
    ->shouldReceive('renderControl')
    ->with($f['foo'], ['attr1' => 'val1'], NULL)
    ->once();
$tester->getMockRenderer()
    ->shouldReceive('renderLabel')
    ->with($f['bar'], ['attr2' => 'val2'], NULL)
    ->once();
$tester->render();
