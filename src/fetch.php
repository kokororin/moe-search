<?php
header("Content-Type: text/html;charset=utf-8");
$key = $_GET['word'];
$key = query($key);

if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}
$start = ($page - 1) * 10;
function google($key, $start)
{
    $url = 'https://www.google.com/search?q=' . $key . '&start=' . $start;
//exit($url);
    $cookie_file = dirname(__FILE__) . "/temp/google.txt";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Cookie: PREF=ID=fef74816681e7898:U=9ea73b7f54aa9005:FF=2:LD=en-US:NW=1:TM=1295952619:LM=1296005167:S=Dk6Hp_5SDKZ3OhJy;'));
	//加上这句抓取才不会乱码
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    $contents = curl_exec($ch);
    curl_close($ch);
    return $contents;

}
$html = google($key, $start);
echo $html;

function is_utf8($word)
{
    if (preg_match("/^([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}/", $word) == true || preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}\$/", $word) == true || preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){2,}/", $word) == true) {
        return true;
    }
    return false;
}

function query($word)
{
    $word = strtr($word, ' ', "+");
    $word = strtr($word, '_', "+");
    $start = ($page - 1) * $rows;

    if ($word == '') {
        return false;
    }

    if (is_utf8($word)) {
        $word = rawurlencode($word);
    } else {
        $word = mb_convert_encoding($word, "UTF-8", "GBK");
        $word = rawurlencode($word);
    }
    return preg_replace("/%2B/i", "+", $word);
}
