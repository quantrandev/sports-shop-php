<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sport_shop";

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
// set the PDO error mode to exception
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$conn->exec("SET NAMES 'utf8';");

?>