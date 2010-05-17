<?php
require_once dirname(__FILE__) . '/lib/t.php';

//test harnes
$h = new lime_harness();
$h->register_glob(dirname(__FILE__) . '/t/*Test.php');
$h->run();

//test coverage(required xdebug)
//$c = new lime_coverage($h);
//$c->base_dir = realpath(dirname(__FILE__) . '/../../');
//$c->register('Services/Buzzurl.php');
//$c->run();


