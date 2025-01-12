<?php

$servername = "mariadb";
$username = "root";
$password = "mariadb";
$dbname = "mariadb";

try {

    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION]);

    return $pdo;

} catch (PDOException $e) {

    die("Kunde inte ansluta till databasen: " . $e->getMessage());

}
