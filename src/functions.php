<?php
if (!defined('MOE_SEARCH'))
{
    exit('Access Denied.');
}

function get_true_url($string)
{
    $string = htmlspecialchars_decode($string);
    preg_match('/\/url\?q=(.*?)\&sa=/i', $string, $matches);
    if (isset($matches[1]))
    {
        return $matches[1];
    }
    return $string;
}

function get_number($string)
{
    return preg_replace('/\D/s', '', $string);
}

function get_base()
{
    if (isset($_SERVER['HTTP_HOST']) && preg_match('/^((\[[0-9a-f:]+\])|(\d{1,3}(\.\d{1,3}){3})|[a-z0-9\-\.]+)(:\d+)?$/i', $_SERVER['HTTP_HOST']))
    {
        $base_url = (is_secure() ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']
        . substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], basename($_SERVER['SCRIPT_FILENAME'])));
    }
    else
    {
        $base_url = 'http://localhost/';
    }
    return rtrim($base_url, '/') . '/';
}

function is_secure()
{
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
    {
        return true;
    }
    elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'])
    {
        return true;
    }
    elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off')
    {
        return true;
    }
    return false;
}

function google($key, $start, $cookie_file)
{
    $url = 'https://www.google.com/search?q=' . $key . '&start=' . $start;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Cookie: PREF=ID=fef74816681e7898:U=9ea73b7f54aa9005:FF=2:LD=en-US:NW=1:TM=1295952619:LM=1296005167:S=Dk6Hp_5SDKZ3OhJy;'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    $contents = curl_exec($ch);
    curl_close($ch);
    unlink($cookie_file);
    return $contents;
}

function is_utf8($word)
{
    if (preg_match("/^([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}/", $word) == true || preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}\$/", $word) == true || preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){2,}/", $word) == true)
    {
        return true;
    }
    return false;
}

function query($word)
{
    $word = strtr($word, ' ', "+");
    $word = strtr($word, '_', "+");
    $start = ($page - 1) * $rows;

    if ($word == '')
    {
        return false;
    }

    if (is_utf8($word))
    {
        $word = rawurlencode($word);
    }
    else
    {
        $word = mb_convert_encoding($word, "UTF-8", "GBK");
        $word = rawurlencode($word);
    }
    return preg_replace("/%2B/i", "+", $word);
}

function need($name)
{
    include dirname(__FILE__) . '/templates/' . $name . '.html';
}
