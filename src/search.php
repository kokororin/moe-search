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

if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}


include './includes/phpQuery.php';
phpQuery::newDocumentFile('http://serverName/fetch.php?word=' . urlencode($word) . '&page=' . $page);
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

/*echo '<pre>';
var_dump($data);
echo '</pre>';*/

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

include './tpl/results.html';
