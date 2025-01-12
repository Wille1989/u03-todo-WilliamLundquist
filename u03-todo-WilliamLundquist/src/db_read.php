<?php

// Upprätthåll connection till databasen
require_once 'db_connection.php';

// Hämta listor
function fetch_id($pdo) {

    try {

        // Hantera frågan till databasen.
        $stmt = $pdo->query("SELECT 
        list_id, 
        titel,
        is_done,
        is_list_favorite 
        FROM list"); // skicka frågan till databasen

        // Returnera resultatet
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
            return [];
    }
}


// KOMBINERA LIST OCH TASK
function combine_list_task($pdo, $list_id) {

    try {

        $stmt = $pdo->prepare("SELECT 
        l.list_id AS list_id,
        l.titel AS list_title,
        l.a_description AS list_description,
        l.is_done AS list_done,
        l.is_list_favorite AS list_favorite,
        l.created_at AS list_created_at,
        t.task_id AS task_id,
        t.titel AS task_title,
        t.is_done AS task_done
    FROM list l
    LEFT JOIN task t ON l.list_id = t.list_id
    WHERE l.list_id = :list_id
    ");

    $list_id = $_GET['list_id'];
    $stmt->bindParam(':list_id', $list_id, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "Failed: " . $e->getMessage();
        return [];
    }
}



// Hämta uppgift från Tabell Uppgift där list_id har ett värde.
function fetch_task_id($pdo) {

    try {
       
        $stmt = $pdo->query("SELECT task_id, titel FROM task WHERE list_id IS NOT NULL");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOexception $e) {

        echo "Failed: " . $e->getMessage();
    }

}



// Hämta uppgift från Tabell Uppgift där list_id inte har ett värde.
function fetch_task_seperate_id($pdo) {

    try {
       
        $stmt = $pdo->query("SELECT 
        task_id, 
        titel,
        is_done,
        is_task_favorite,
        a_description
        FROM task 
        WHERE list_id IS NULL");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOexception $e) {

        echo "Failed: " . $e->getMessage();
    }

}


// HÄMTAR SPECIFIK LISTA
function fetch_specifik_list($pdo, $list_id) {

    try {

        // Hantera frågan till databasen.
        $stmt = $pdo->prepare("SELECT * FROM list WHERE list_id = :list");//Förbered Frågan till databasen
        $stmt->execute(['list' => $list_id]); //Skicka frågan till databasen

        return $stmt->fetch(PDO::FETCH_ASSOC); //Returnera resultatet

    } catch (PDOException $e) {
        echo "Failed: " . $e->getMessage();
        return null;
    }
}


// HÄMTA SPECIFIK UPPGIFT
function fetch_specifik_task($pdo, $task_id) {

    try {
        
        $stmt = $pdo->prepare("SELECT * FROM task WHERE task_id = :task");
        
        $stmt->execute(['task' => (int)$task_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "Failed: " . $e->getMessage();
        return null;
    }
    
}


// HÄMTA LISTA SOM ÄR CHECKAD SOM FAVORIT
function fetch_favorites_list($pdo) {

    try {
       
        $stmt = $pdo->prepare("SELECT * FROM list WHERE is_list_favorite = 1");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOexception $e) {

        echo "Failed: " . $e->getMessage();
    }

}


// HÄMTA UPPGIFT SOM ÄR CHECKAD SOM FAVORIT
function fetch_favorites_task($pdo) {

    try {
       
        $stmt = $pdo->prepare("SELECT * FROM task WHERE is_task_favorite = 1 AND list_id IS NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOexception $e) {

        echo "Failed: " . $e->getMessage();
        return [];
    }

}


// HÄMTAR LISTA OCH UPPGIFT I BÅDA TABELLERNA BASERAT PÅ OM NÅGOT ÄR KLART
function fetch_completed_LT($pdo){

    try {
        $stmt = $pdo->prepare("
        (
        SELECT
            list.list_id AS l_id,
            list.titel AS l_titel,
            task.task_id AS t_id,
            task.titel AS t_titel
        FROM list
        LEFT JOIN task ON list.list_id = task.list_id
        WHERE list.is_done = 1
        ) 
        UNION ALL
        (
        SELECT 
            NULL AS l_id,
            NULL AS l_titel,
            task.task_id AS t_id,
            task.titel AS t_titel
        FROM task
        WHERE task.list_id IS NULL AND task.is_done = 1
        )
        ");


        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "Failed: " . $e->getMessage();
        return[];
    }
}