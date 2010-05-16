<?php
require_once dirname(__FILE__) . '/t.php';

$h = new lime_harness(null);
$h->register_glob(dirname(__FILE__) . '/../t/*Test.php');
$h->run();

