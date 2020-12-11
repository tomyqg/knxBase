<?php
header("Content-Type: text/plain");

$number1 = $_GET["number1"]+0;
$number2 = $_GET["number2"]+0;

$sum = $number1 + $number2;

//echo $sum;

echo utf8_decode('<?xml version="1.0" encoding="UTF-8"?><number>'.$sum.'</number>');

?>