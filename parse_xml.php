<?php

require_once 'Database.php';
$worker = new Database();

$xml = simplexml_load_file($_POST['feed']) or die("Ошибка!");
//$xml = simplexml_load_file("http://www.spbren.ru/upload/yandexFeedKurort.xml") or die("Ошибка!");

unset($xml->{'generation-date'}); // Удаляем пустой элемент

$worker->db_connect();

foreach ($xml as $object['offer'] => $offer) {
    $worker->setOfferDb($offer);
}

$worker->db_close();


?>