<?php
require_once dirname(__FILE__) . '/../lib/t.php';

$id = 'tell-k'; //your buzzurl_id is here 
                //ex) http://buzzurl.jp/user/[youre buzzurl_id]

$t = new lime_test(16, new lime_output_color());

require_once 'Services/Buzzurl.php';

$api = Services_Buzzurl::getInstance();
$t->ok($api instanceof Services_Buzzurl, 'create instance is ok');

$t->diag('getAirticles');
$api->setFormat('array');
$t->ok(is_array($api->getArticles($id)), 'get articles is ok');
$api->setFormat('json');
$t->ok(is_string($api->getArticles($id, null)), 'get articles(json) is ok');

//please set search keywords
$keywords = 'php';
$api->setFormat('array');
$t->ok(is_array($api->getArticles($id, 'php')), 'get articles by keywords is ok');
$api->setFormat('json');
$t->ok(is_string($api->getArticles($id, 'php')), 'get articles by keywords(json) is ok');

$t->diag('getReaders');
$api->setFormat('array');
$t->ok(is_array($api->getReaders($id)), 'get readers is ok');
$api->setFormat('json');
$t->ok(is_string($api->getReaders($id)), 'get readers(json) is ok');

$t->diag('getFavaites');
$api->setFormat('array');
$t->ok(is_array($api->getFavarites($id)), 'get favorites is ok');
$api->setFormat('json');
$t->ok(is_string($api->getFavarites($id)), 'get favorites(json) is ok');

$t->diag('getPostsInfo');
$url = 'http://ecnavi.jp/';
$api->setFormat('array');
$t->ok(is_array($api->getPostsInfo($url)), 'get posts info is ok');
$api->setFormat('json');
$t->ok(is_string($api->getPostsInfo($url)), 'get posts(json) info is ok');

$t->diag('getCounter');
$url = 'http://ecnavi.jp/';
$api->setFormat('array');
$t->ok(is_array($api->getCounter($url)), 'get counter by url string is ok');
$api->setFormat('json');
$t->ok(is_string($api->getCounter($url)), 'get counter by url string(json) is ok');

$url = array('http://ecnavi.jp/', 'http://buzzurl.jp/');
$api->setFormat('array');
$t->ok(is_array($api->getCounter($url)), 'get counter by url array is ok');
$api->setFormat('json');
$t->ok(is_string($api->getCounter($url)), 'get counter by url array(json) is ok');

$t->diag('getCounterImgUrl');
$url = 'http://ecnavi.jp/';
$t->is($api->getCounterImgUrl($url), 'http://api.buzzurl.jp/api/counter/v1/image/?url=http%3A%2F%2Fecnavi.jp%2F',  'get counter is ok');
