<?php
/**
 * BuzzurlのAPIを扱うためのライブラリ 
 * 
 * @category   Services
 * @package    Buzzurl
 * @author     tell-k <ffk2005@gmail.com> 
 * @since      PHP5.2
 * @link       http://labs.ecnavi.jp/developer/buzzurl/api/
 * @version    $Id$
 */

/**
 * BuzzurlのAPIを扱うためのライブラリ 
 *
 * Brief example of use:
 * <code>
 * <?php
 *
 * $id = 'tell-k' //<= your buzzurl id
 *
 * $api = Services_Buzzurl::getInstance();
 * $result = $api->getReaders($id);
 *
 * foreach($result as $user)  {
 *      print $user . "<br />\n";
 * }
 * $api->setFormat('json');
 * $result = $api->getReaders($id);
 *
 * print $result . "<br />\n"; //print json data
 *
 * </code>
 * 
 * @category   Services
 * @package    Buzzurl
 * @author     tell-k <ffk2005@gmail.com> 
 * @since      PHP5.2
 * @link       http://labs.ecnavi.jp/developer/buzzurl/api/
 * @version    $Id$
 */
class Services_Buzzurl 
{
    static private $instance;

    private $version = 'v1';
    private $versions  = array('v1');

    private $format  = 'array';
    private $formats  = array('array', 'json');

    private $responseType = 'json';
    private $responseTypes = array('json', 'image');

    private $commands = array('counter', 'readers', 'favorites', 'ariticles');

    const API_URL  = 'http://api.buzzurl.jp/api/%s/%s/%s/%s';
    const ADD_URL  = 'https://buzzurl.jp/posts/add/%s/';

    // {{{ __construct() 

    /**
     * コンストラクタ 直接newしない 
     * 
     * @access private
     * @return void
     */
    private function __construct() {

    }
    
    // }}}
    // {{{ getInstance() 

    /**
     * Service_Buzzurlのインスタンスを取得 
     * 
     * @static
     * @access public
     * @return object Service_Buzzurl
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Services_Buzzurl();
        }
        return self::$instance;
    }

    // }}}

    // {{{ setVersion() 

    /**
     * BuzzurlAPIのバージョンをセット
     * 
     * @param  string $version 
     * @access public
     * @return void
     */
    public function setVersion($version) {
        if (!$version || !in_array($version, $this->versions)) {
            $err = '[' . __CLASS__ . '] argument error';
            throw new InvalidArgumentException($err);
        }
        $this->version = $version;
    }

    // }}}
    // {{{ setFormat() 

    /**
     * 結果データフォーマットの設定
     * 
     * @param  string $format 
     * @access public
     * @return void
     */
    public function setFormat($format) {
        if (!$format || !in_array($format, $this->formats)) {
            $err = '[' . __CLASS__ . '] argument error';
            throw new InvalidArgumentException($err);
        }
        $this->format = $format;

    }

    // }}}
    // {{{ setResponseType() 

    /**
     * APIからのデータ取得形式 をセット
     * 
     * @param string $type 
     * @access public
     * @return void
     */
    protected function setResponseType($type) {
        if (!$type || !in_array($type, $this->responseTypes)) {
            $err = '[' . __CLASS__ . '] argument error';
            throw new InvalidArgumentException($err);
        }
        $this->responseType = $type;
    }

    // }}}
    // {{{ getApiUrl() 

    /**
     * getApiUrl
     * 
     * @param  string $command APIの種別
     * @param  mixed  $param 
     * @access public
     * @return string 各APIのURL
     */
    public function getApiUrl($command, $param) {
        return sprintf(self::API_URL, $command, $this->version, $this->responseType, $param);
    }

    // }}}

//api command
    // {{{ getArticles() 

    /**
     * ユーザーの最近のエントリー一覧取得
     *
     * キーワードでの絞り込みが可能
     * 
     * @link   http://labs.ecnavi.jp/developer/2007/01/jsonapi.html
     * @link   http://labs.ecnavi.jp/developer/2007/03/jsonapi_5.html
     * @param  string $userId 
     * @param  string $keywords 
     * @access public
     * @return mixed エントリー情報
     */
    public function getArticles($userId, $keyword = null) {

        if (!$userId) {
            $err = '[' . __CLASS__ . '] argument error';
            throw new InvalidArgumentException($err);
        }

        $param = ($keyword) ? $userId .'/keyword/' . urlencode($keyword) 
                            : $userId;

        $result = $this->doGet($this->getApiUrl('articles', $param));
        return $this->formatResult($result);
    }

    //}}}
    // {{{ getRecentArticles() 

    /**
     * 新着エントリー一覧取得
     *
     * @link   http://labs.ecnavi.jp/developer/2007/01/jsonapi_4.html
     * @param  int $num       取得件数
     * @param  int $of        ページ数
     * @param  int $threshold ブックマークユーザー数閾値
     * @access public
     * @return mixed エントリー一覧
     */
    public function getRecentArticles($num = null, $of = null, $threshold = null) {

        if (
            ($num && !preg_match('/^[1-9][0-9]*$/', $num))
            || ($of  && !preg_match('/^[1-9][0-9]*$/', $of)) 
            || ($threshold && !preg_match('/^[1-9][0-9]*$/', $threshold))
        ) {
            $err = '[' . __CLASS__ . '] argument error';
            throw new InvalidArgumentException($err);
        }

        $params = array('num', 'of', 'threshold'); 
        $tmp = array();
        foreach ($params as $v) {
            if (!$$v) continue;
        
            if ($v === 'of' || $v === 'threshold') $$v++;
            
            $tmp[] = $v . '=' . urlencode($$v); 
        }
        $param = (count($tmp) > 0) ? '?' . implode('&', $tmp) : null;

        $result = $this->doGet($this->getApiUrl('articles/recent', $param));
        return $this->formatResult($result);
    }

    //}}}
    // {{{ getPostsInfo() 

    /**
     * $url のブクマ登録情報を取得
     * 
     * @link   http://labs.ecnavi.jp/developer/2007/01/api_1.html
     * @param  string $url
     * @access public
     * @return mixed 登録情報
     */
    public function getPostsInfo($url) {

        if (!$url) {
            $err = '[' . __CLASS__ . '] argument error';
            throw new InvalidArgumentException($err);
        }

        $param = '?url=' . urlencode($url);
        $result = $this->doGet($this->getApiUrl('posts/get', $param));
        return $this->formatResult($result);
    }

    //}}}
    // {{{ getCounter() 

    /**
     * $url の ブックマークユーザー数を取得
     * 
     * @link   http://labs.ecnavi.jp/developer/2007/01/jsonapi_1.html
     * @param  mixed $url 文字列 or 配列でURLを渡す
     * @access public
     * @return mixed ブックマークユーザー数
     */
    public function getCounter($url) {

        if (!$url) {
            $err = '[' . __CLASS__ . '] argument error';
            throw new InvalidArgumentException($err);
        }

        $param = null;
        if (is_array($url)) {
            foreach ($url as $v) {
                $param[] = 'url=' . urlencode($v);
            }
            $param = '?' . implode('&', $param);
        } else {
            $param = '?url=' . urlencode($url);
        }

        $result = $this->doGet($this->getApiUrl('counter', $param));
        return $this->formatResult($result);
    }

    //}}}
    // {{{ getFavarites() 

    /**
     * $userId が お気に入り登録しているユーザー一覧を取得
     * 
     * @link   http://labs.ecnavi.jp/developer/2007/01/jsonapi_2.html
     * @param  string $userId 
     * @access public
     * @return mixed ユーザー一覧
     */
    public function getFavarites($userId) {

        if (!$userId) {
            $err = '[' . __CLASS__ . '] argument error';
            throw new InvalidArgumentException($err);
        }

        $result = $this->doGet($this->getApiUrl('favorites', $userId));
        return $this->formatResult($result);
    }

    //}}}
    // {{{ getReaders() 

    /**
     * $userId を お気に入り登録しているユーザー一覧を取得
     * 
     * @link   http://labs.ecnavi.jp/developer/2007/01/jsonapi_3.html
     * @param  string $userId 
     * @access public
     * @return mixed ユーザー一覧
     */
    public function getReaders($userId) {

        if (!$userId) {
            $err = '[' . __CLASS__ . '] argument error';
            throw new InvalidArgumentException($err);
        }

        $result = $this->doGet($this->getApiUrl('readers', $userId));
        return $this->formatResult($result);
    }

    //}}}

//other
    // {{{ getCounterImgUrl() 

    /**
     * ブクマユーザー数イメージ取得APIのURLを取得
     *
     * imgタグのsrcに渡す
     *
     * @link   http://labs.ecnavi.jp/developer/2007/01/api.html
     * @param  string $userId 
     * @access public
     * @return string イメージ取得APIのURL
     */
    public function getCounterImgUrl($url) {

        if (!$url) {
            $err = '[' . __CLASS__ . '] argument error';
            throw new InvalidArgumentException($err);
        }

        $param = '?url=' . urlencode($url);
        $this->setResponseType('image');
        return $this->getApiUrl('counter', $param);
    }

    //}}}
    // {{{ formatResult() 

    /**
     * APIから取得したデータをformatに合わせて整形
     * 
     * @param  string $result JSONデータ
     * @access public
     * @return mixed 整形後のデータ
     */
    public function formatResult($result) {
        switch($this->format) {
            case 'array': 
                return json_decode($result);
            case 'json': 
            default: 
                return $result;
        }
    }

    //}}}

//request method
    // {{{ doGet() 
    
    /**
     * GETリクエスト
     * 
     * @param  string $url 
     * @access protected
     * @return string レスポンスボディ
     */
    protected function doGet($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    //}}}
//    // {{{ doPost() 
//
//    /**
//     * POSTリクエスト
//     * 
//     * @param  string $url 
//     * @param  array  $postData
//     * @access protected
//     * @return string レスポンスボディ
//     */
//    private function doPost($url, $postData = null) {
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//        //curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
//        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
//        $result = curl_exec($ch);
//        curl_close($ch);
//        return $result;
//    }
//
//    //}}}
}
