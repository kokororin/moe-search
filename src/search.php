<?php
error_reporting(0);
header("Content-Type: text/html;charset=utf-8");
if (!isset($_GET['word'])) {
    header('Location: /');
} elseif ($_GET['word'] == '') {
    header('Location: /');
} else {
    $word = $_GET['word'];
}

$page = isset($_GET['page']) ? $_GET['page'] : 1;

include './includes/phpQuery.php';

//phpQuery::newDocumentFile(get_base() . 'fetch.php?word=' . urlencode($word) . '&page=' . $page);
$prefix = 'http://www.guge.link/';
$start  = ($page - 1) * 10;
phpQuery::newDocumentFile($prefix . 'search?q=' . urlencode($word) . '&start=' . $start);

$list = pq('.g');
foreach ($list as $li) {
    $data['name'][] = pq($li)->find('.r')->find('a')->html();
    $data['_url'][] = pq($li)->find('.r')->find('a')->attr('href');
    $data['desc'][] = pq($li)->find('.s')->find('.st')->html();
}
$data['num'][0] = pq('#resultStats')->html();
$data['num'][1] = get_number($data['num'][0]);

foreach ($data['_url'] as $key => $value) {
    $data['url'][] = get_true_url($data['_url'][$key]);
}

foreach ($data['url'] as $key => $value) {
    if (substr($value, 0, 1) == '/') {
        unset($data['url'][$key], $data['name'][$key], $data['desc'][$key]);
    }
}


include './includes/page.class.php';
$pager      = new Page(10, $data['num'][1], $page, 10);
$pager_html = $pager->show();

function get_true_url($string)
{
    $start = '/url?q=';
    $end   = '&sa=';
    if (($start_pos = strpos($string, $start)) !== false) {
        if ($end) {
            if (($end_pos = strpos($string, $end, $start_pos + strlen($start))) !== false) {
                return urldecode(substr($string, $start_pos + strlen($start), $end_pos - ($start_pos + strlen($start))));
            }
        } else {
            return urldecode(substr($string, $start_pos));
        }
    }
    return $string;
}

function get_number($str)
{
    return preg_replace('/\D/s', '', $str);
}

function get_base()
{
    if (isset($_SERVER['HTTP_HOST']) && preg_match('/^((\[[0-9a-f:]+\])|(\d{1,3}(\.\d{1,3}){3})|[a-z0-9\-\.]+)(:\d+)?$/i', $_SERVER['HTTP_HOST'])) {
        $base_url = (is_secure() ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']
        . substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], basename($_SERVER['SCRIPT_FILENAME'])));
    } else {
        $base_url = 'http://localhost/';
    }
    return rtrim($base_url, '/') . '/';
}
function is_secure()
{
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return true;
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO']) {
        return true;
    } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        return true;
    }
    return false;
}

include './tpl/results.html';
