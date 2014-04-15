<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */


//Inspired by tech.m6web
require_once __DIR__ . '/../vendor/atoum/atoum/classes/autoloader.php';

/*
* CLI report.
*/
$stdOutWriter = new \mageekguy\atoum\writers\std\out();
$cli = new \mageekguy\atoum\reports\realtime\cli();
$cli->addWriter($stdOutWriter);

$basedir = __DIR__;

/*
* Xunit report
*/
$xunit = new \mageekguy\atoum\reports\asynchronous\xunit();
/*
* Xunit writer
*/
$writer = new \mageekguy\atoum\writers\file($basedir.'/logs/junit.xml');
$xunit->addWriter($writer);

$runner->addReport($xunit);
$runner->addReport($cli);