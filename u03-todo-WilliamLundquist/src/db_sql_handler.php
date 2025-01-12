<?php

// Upprätt anslutning till databasen
require_once 'db_connection.php'; 

// variabel för sql. filen
$sqlfile = 'seed/mysql.sql';

// Kontrollera att sqlfilen finns, samt att filen inte är tom
try {
    if (file_exists($sqlfile)) {
        $sql = file_get_contents($sqlfile);
            
        if (!empty($sql)) {
            $pdo->exec($sql);
        } else {
            echo $sqlfile . " är tom";
        }
    }
} catch (PDOException $e) {
    echo "Failed: " . $e->getMessage();
}
