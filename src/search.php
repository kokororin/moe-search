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

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.kotori.love/google/search.php?word=' . $word . '&page=' . $page);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$contents = curl_exec($ch);
curl_close($ch);
$data = json_decode($contents, true);
if ($data['status'] == 'failure') {
    $num = 0;
} else {
    $num = $data['count'];
}

include './includes/page.class.php';
$pager      = new Page(10, $num, $page, 10);
$pager_html = $pager->show();

include './tpl/results.html';
