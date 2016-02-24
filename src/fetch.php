<?php
require './common.php';
require './functions.php';

header("Content-Type: text/html;charset=utf-8");
$key = $_GET['word'];
$key = query($key);

if (isset($_GET['page']))
{
    $page = $_GET['page'];
}
else
{
    $page = 1;
}
$start = ($page - 1) * 10;
$cookie_file = dirname(__FILE__) . "/google.txt";

$html = google($key, $start,$cookie_file);
echo $html;
