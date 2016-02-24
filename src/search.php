<?php
require dirname(__FILE__) . '/common.php';
require dirname(__FILE__) . '/functions.php';
require dirname(__FILE__) . '/includes/simple_html_dom.php';
require dirname(__FILE__) . '/includes/page.php';

header("Content-Type: text/html;charset=utf-8");

if (!isset($_GET['word']))
{
    header('Location: ' . get_base());
}
elseif ($_GET['word'] == '')
{
    header('Location: ' . get_base());
}
else
{
    $word = $_GET['word'];
}

if (isset($_GET['page']))
{
    $page = $_GET['page'];
}
else
{
    $page = 1;
}

$html = file_get_html(get_base() . 'fetch.php?word=' . urlencode($word) . '&page=' . $page);

foreach ($html->find('.g') as $i => $li)
{
    $data[] = array(
        'title' => $li->find('.r', '0')->find('a', '0')->plaintext,
        'original_url' => $li->find('.r', '0')->find('a', '0')->href,
        'content' => $li->find('.s', '0')->find('.st', '0')->plaintext,
    );
    $data[$i]['url'] = get_true_url($data[$i]['original_url']);
}

$count['word'] = $html->find('#resultStats', '0')->plaintext;
$count['number'] = get_number($count['word']);

foreach ($data as $key => $value)
{
    if (substr($value['url'], 0, 1) == '/')
    {
        unset($data[$key]);
    }
}

$pager = new Page(10, $count['number'], $page, 10);
$pager_html = $pager->show();

include './templates/results.html';
