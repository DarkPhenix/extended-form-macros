<?php

use InstanteTests\ExtendedFormMacros\Latte\MacroTester;
use Nette\Forms\Controls\RadioList;
use Nette\Forms\Form;
use Nette\Forms\IControl;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/MacroTester.inc';

$tester = new MacroTester(<<<EOT
{form theForm}
{input foo:bar}
{var \$var = 'baz'}
{input foo:\$var}
{/form}
EOT
);
$f = $tester->getForm();
$f->addRadioList('foo', 'Foo', [
    'bar' => 'baz',
]);
$tester->getMockRenderer()->shouldReceive('renderBegin');
$tester->getMockRenderer()->shouldReceive('renderEnd');
$tester->getMockRenderer()->shouldReceive('renderControl')->with(
    Mockery::type(RadioList::class),
    [],
    'bar'
);
$tester->getMockRenderer()->shouldReceive('renderControl')->with(
    Mockery::type(RadioList::class),
    [],
    'baz'
);

\Tester\Environment::$checkAssertions = false; //only mockery expectations will be tested
$tester->render();
