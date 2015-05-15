<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     MIT
 */

define('TESTS_ROOT', dirname(__FILE__));
define('TESTS_TMP', TESTS_ROOT . '/tmp');

include(TESTS_ROOT . '/../vendor/autoload.php');
include(TESTS_ROOT . '/../vendor/atoum/atoum/scripts/runner.php');

//Common tests for each implementation
include(TESTS_ROOT . '/BBQ/Queue/AbstractQueue.php');