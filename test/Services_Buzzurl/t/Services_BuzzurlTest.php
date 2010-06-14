<?php
/**
 * unit test for Services_Buzzurl  
 * 
 * @author     tell-k <ffk2005@gmail.com> 
 * @since      PHP5.2
 * @version    $Id$
 */
require_once dirname(__FILE__) . '/../lib/t.php';

$id = 'tell-k'; //set your buzzurl_id
                //ex) http://buzzurl.jp/user/[youre buzzurl_id]

$t = new lime_test(68, new lime_output_color());

require_once 'Services/Buzzurl.php';

$t->diag('getInstance');
$api = Services_Buzzurl::getInstance();
$t->ok($api instanceof Services_Buzzurl, 'create instance is ok');

$t->diag('setVersion');
$validCase = array('v1');
foreach ($validCase as $case) {
    try {
        $api->setVersion($case);
        $t->pass('set version is ok');
    } catch (Exception $e) {
        $t->fail('set version not ok');
    }
}
$errCase = array(null, 'hoge');
foreach ($errCase as $case) {
    try {
        $api->setVersion($case);
        $t->fail('set version exception test not ok');
    } catch (InvalidArgumentException $e) {
        $t->pass('set version exception test is ok');
    }
}

$t->diag('setFormat');
$validCase = array('array', 'json');
foreach ($validCase as $case) {
    try {
        $api->setFormat($case);
        $t->pass('set format is ok');
    } catch (Exception $e) {
        $t->fail('set format not ok');
    }
}
$errCase = array(null, 'hoge');
foreach ($errCase as $case) {
    try {
        $api->setFormat($case);
        $t->fail('set format exception test not ok');
    } catch (InvalidArgumentException $e) {
        $t->pass('set foramat exception test is ok');
    }
}

$t->diag('getAirticles');
$api->setFormat('array');
$t->ok(is_array($api->getArticles($id)), 'get articles is ok');
$api->setFormat('json');
$t->ok(is_string($api->getArticles($id, null)), 'get articles(json) is ok');
try {
    $api->getArticles(null);
    $t->fail('get articles exception test not ok');
} catch (InvalidArgumentException $e) {
    $t->pass('get articles exception test is ok');
}

//please set search keywords
$keywords = 'php';
$api->setFormat('array');
$t->ok(is_array($api->getArticles($id, 'php')), 'get articles by keywords is ok');
$api->setFormat('json');
$t->ok(is_string($api->getArticles($id, 'php')), 'get articles by keywords(json) is ok');

$t->diag('getRecentAirticles');
$api->setFormat('array');
$t->ok(is_array($api->getRecentArticles()), 'get recent articles is ok');
$t->ok(is_array($api->getRecentArticles(null, null, null)), 'get recent articles is ok');
$t->ok(is_array($api->getRecentArticles(1)), 'get recent articles is ok');
$t->ok(is_array($api->getRecentArticles(1, 1)), 'get recent articles is ok');
$t->ok(is_array($api->getRecentArticles(1, 1, 1)), 'get recent articles is ok');
$t->ok(is_array($api->getRecentArticles(2, 2, 2)), 'get recent articles is ok');
$t->ok(is_array($api->getRecentArticles(2, 3, 3)), 'get recent articles is ok');
$t->is(count($api->getRecentArticles(10)), 10, 'get recent articles count is ok');

$api->setFormat('json');
$t->ok(is_string($api->getRecentArticles()), 'get recent articles(json) is ok');
$t->ok(is_string($api->getRecentArticles(null, null, null)), 'get recent articles(json) is ok');
$t->ok(is_string($api->getRecentArticles(1)), 'get recent articles(json) is ok');
$t->ok(is_string($api->getRecentArticles(1, 1)), 'get recent articles(json) is ok');
$t->ok(is_string($api->getRecentArticles(1, 1, 1)), 'get recent articles(json) is ok');

$errCase = array(
        array('num' => -1, 'of' => -1, 'threshold' => -1),
        array('num' => 1, 'of' => -1, 'threshold' => -1),
        array('num' => -1, 'of' => 1, 'threshold' => -1),
        array('num' => -1, 'of' => -1, 'threshold' => 1),
        array('num' => 'hoge', 'of' => 1, 'threshold' => 1),
        array('num' => 1.1, 'of' => 1, 'threshold' => 1),
        array('num' => '01', 'of' => 1, 'threshold' => 1),
        array('num' => '1a', 'of' => 1, 'threshold' => 1),
        );
foreach ($errCase as $v) {
    try {
        $api->getRecentArticles($v['num'], $v['of'], $v['threshold']);
        $t->fail('get recent articles exception test not ok');
    } catch (InvalidArgumentException $e) {
        $t->pass('get recent articles exception test is ok');
    }
}

$t->diag('getReaders');
$api->setFormat('array');
$t->ok(is_array($api->getReaders($id)), 'get readers is ok');
$api->setFormat('json');
$t->ok(is_string($api->getReaders($id)), 'get readers(json) is ok');
try {
    $api->getReaders(null);
    $t->fail('get readers exception test not ok');
} catch (InvalidArgumentException $e) {
    $t->pass('get readers exception test is ok');
}

$t->diag('getFavaites');
$api->setFormat('array');
$t->ok(is_array($api->getFavarites($id)), 'get favorites is ok');
$api->setFormat('json');
$t->ok(is_string($api->getFavarites($id)), 'get favorites(json) is ok');
try {
    $api->getFavarites(null);
    $t->fail('get favorites exception test not ok');
} catch (InvalidArgumentException $e) {
    $t->pass('get favorites exception test is ok');
}

$t->diag('getPostsInfo');
$url = 'http://ecnavi.jp/';
$api->setFormat('array');
$t->ok(is_array($api->getPostsInfo($url)), 'get posts info is ok');
$api->setFormat('json');
$t->ok(is_string($api->getPostsInfo($url)), 'get posts(json) info is ok');
try {
    $api->getPostsInfo(null);
    $t->fail('get posts info exception test not ok');
} catch (InvalidArgumentException $e) {
    $t->pass('get posts info exception test is ok');
}

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

try {
    $api->getCounter(null);
    $t->fail('get counter exception test not ok');
} catch (InvalidArgumentException $e) {
    $t->pass('get counter exception test is ok');
}

$t->diag('getCounterImgUrl');
$url = 'http://buzzurl.jp/';
$expected = 'http://api.buzzurl.jp/api/counter/v1/image/?url=http%3A%2F%2Fbuzzurl.jp%2F';
$t->is($api->getCounterImgUrl($url), $expected, 'get counter is ok');
try {
    $api->getCounterImgUrl(null);
    $t->fail('get counter image url exception test not ok');
} catch (InvalidArgumentException $e) {
    $t->pass('get counter image url exception test is ok');
}

$t->diag('makePostData');
$case = array(
        array(
            'args'     => array('url' => 'http://test.co.jp'),
            'expected' => 'url=http://test.co.jp',
            ),
        array(
            'args'     => array('url' => 'http://test.co.jp', 'keyword' => 'test'),
            'expected' => 'url=http://test.co.jp&keyword=test',
            ),
        array(
            'args'     => array('url' => 'http://test.co.jp', 'keyword' => array('test', 'test2')),
            'expected' => 'url=http://test.co.jp&keyword=test&keyword=test2',
            ),
        array(
            'args'     => array('hoge' => 'http://test.co.jp', 'keyword' => array('test', 'test2')),
            'expected' => 'keyword=test&keyword=test2',
            ),
        array(
            'args'     => array('keyword' => array('test', 'test2', 'test3', 'test4', 'test5', 'test6', 'test7', 'test8', 'test9')),
            'expected' => 'keyword=test&keyword=test2&keyword=test3&keyword=test4&keyword=test5&keyword=test6&keyword=test7&keyword=test8',
            ),
        array(
            'args'     => array('reply' => '1'),
            'expected' => 'reply=1',
            ),
        array(
            'args'     => array('reply' => '0'),
            'expected' => 'reply=0',
            ),
        array(
            'args'     => array('reply' => '-1'),
            'expected' => '',
            ),
        array(
            'args'     => array('reply' => '0.1'),
            'expected' => '',
            ),
        array(
            'args'     => array('reply' => '1.1'),
            'expected' => '',
            ),
        array(
            'args'     => array('reply' => 'hoge'),
            'expected' => '',
            ),
        array(
            'args'     => array('access' => 'anonymous'),
            'expected' => 'access=anonymous',
            ),
        array(
            'args'     => array('access' => 'private'),
            'expected' => 'access=private',
            ),
        array(
            'args'     => array('access' => 'unknowon'),
            'expected' => '',
            ),
        );

foreach($case as $v) {
    $result = $api->makePostData($v['args']);
    $t->is($result, $v['expected'], 'make post data test is ok');
}

//$t->diag('add');
//
// 実際に投稿のテストをする時 addのテストのコメント合うとは外して行ってください。
//
//$email  = ''; //<= your buzzurl login id(email) 
//$passwd = ''; //<= your buzzurl password
//
//$case = array(
//        array('url' => 'http://buzzurl.jp'),
//        array('url' => 'http://buzzurl.jp', 'title' => 'Buzzurl', 'comment' => 'buzzurl 投稿テスト', 'keyword' => array('SBM', 'social'), 'reply' => '0', 'access' => 'anonymous'),
//        array('url' => 'http://buzzurl.jp', 'title' => 'Buzzurl', 'comment' => 'buzzurl 投稿テスト', 'keyword' => 'tagtest', 'reply' => '1', 'access' => 'private'),
//        );
//foreach($case as $args) {
//    $result = $api->add($email, $passwd, $args);
//    $t->is($result, true, 'add test is ok');
//}


$t->diag('add - exception test');
try {
    $args = null;
    $api->add($email, $passwd, $args);
    $t->fail('add exception test is fail');
} catch (Exception $e) {
    $t->pass('add exception test is ok');
}

try {
    $args = array();
    $api->add($email, $passwd, $args);
    $t->fail('add exception test is fail');
} catch (Exception $e) {
    $t->pass('add exception test is ok');
}

try {
    $args = array('url' => 'http://buzzurl.jp');
    $api->add(null, $passwd, $args);
    $t->fail('add exception test is fail');
} catch (Exception $e) {
    $t->pass('add exception test is ok');
}

try {
    $args = array('url' => 'http://buzzurl.jp');
    $api->add($email, null, $args);
    $t->fail('add exception test is fail');
} catch (Exception $e) {
    $t->pass('add exception test is ok');
}

