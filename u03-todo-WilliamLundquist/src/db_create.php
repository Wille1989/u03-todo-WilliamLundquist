<?php

require_once 'db_init.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

    // SKAPA EN NY LISTA

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_send_data']) && $_POST['form_name'] === 'create_list') {
 

        $list_name = trim($_POST['list_name']);
        $list_description = trim($_POST['list_description']);
        $list_favorite = isset($_POST['list_favorite']) ? 1 : 0;
       
            
            if (empty($list_name)){
                $error['list_name'] = "Namn på Listan är ett krav";}

            if  (empty($list_description)){       
                $error['list_description'] = "Beskrivning är ett krav";}

                if (!empty($error)) {
                    $_SESSION['error'] = $error;
                    header("Location: index.php");
                    exit;
                }


                require_once 'functions/crud-functions.php';

                $list_name = validate($list_name);
                $list_description = validate($list_description);

                require_once 'db_connection.php';
                require_once 'db_sql_handler.php';
                
                    try {
            
                        $sql = "INSERT INTO list (titel, a_description, is_list_favorite, created_at)
                        VALUES (:titel, :a_description, :is_list_favorite, NOW())";

                        $stmt = $pdo->prepare($sql);
      
                        $stmt->bindParam(':titel', $list_name);
                        $stmt->bindParam(':a_description', $list_description);
                        $stmt->bindParam(':is_list_favorite', $list_favorite, PDO::PARAM_INT);

                        $stmt->execute();

                        header("Location: index.php");
                    
                    } catch (PDOException $e) {

                        echo "SQL-Fel: " . $e->getMessage();
                        
                    }
            }


// SKAPA NY UPPGIFT I EN LISTA


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {

    $list_id = isset($_POST['list_id']) ? (int) $_POST['list_id'] : null;
    $task_name = trim($_POST['task_name']);
    $task_favorite = isset($_POST['task_favorite']) ? 1 : 0;

   
        if(empty($task_name)){
            $error['task_name'] = "Ange Uppgift";}

           

        }

        if (!empty($error)) {
            $_SESSION['error'] = $error;
            header("Location: index.php?list_id=$list_id");
            exit;
        }
            
        if (!empty($task_name)){
                        
            require_once 'functions/crud-functions.php';
    
            $task_name = validate($task_name);
    
            require_once 'db_connection.php';
            require_once 'db_sql_handler.php';

            if(isset($_POST['add_task'])){
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM list WHERE list_id = :list_id");
                $stmt->bindParam(':list_id', $list_id, PDO::PARAM_INT);
                $stmt->execute();
                $listExists = $stmt->fetchColumn();
            
                    try {
            
                    $stmt = $pdo->prepare("INSERT INTO task (list_id, titel, is_task_favorite, created_at)
                    VALUES (:list_id, :titel, :is_task_favorite, NOW())");
                        
                        
                    $stmt->bindParam(':list_id', $list_id, PDO::PARAM_INT);    
                    $stmt->bindParam(':titel', $task_name, PDO::PARAM_STR);
                    $stmt->bindParam(':is_task_favorite', $task_favorite, PDO::PARAM_INT);
                    
                    $stmt->execute();

                    header("Location: index.php?list_id=$list_id");

                } catch (PDOException $e) {
    
                    echo "SQL-Fel: " . $e->getMessage();
                            
                }
            }
        }


// SKAPA NY ENSKILD UPPGIFT


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_send_data_task'])) {

    $list_id = isset($_POST['list_id']) ? (int) $_POST['list_id'] : null;
    $task_name = trim($_POST['task_name']);
    $task_description = trim($_POST['task_description']);
    $task_favorite = isset($_POST['task_favorite']) ? 1 : 0;

    if (empty($task_name)){
        $error['task_name'] = "Namn på uppgift är ett krav";}

    if  (empty($task_description)){       
        $error['task_description'] = "Beskrivning är ett krav";}

        if (!empty($error)) {
            $_SESSION['error'] = $error;
            header("Location: index.php");
            exit;
        }
    
            
        if (!empty($task_name) && !empty($task_description)){
                        
            require_once 'functions/crud-functions.php';
    
            $task_name = validate($task_name);
            $task_description = validate($task_description);
    
            require_once 'db_connection.php';
            require_once 'db_sql_handler.php';

                        try {
                
                        $stmt = $pdo->prepare("INSERT INTO task (list_id, titel, a_description, is_task_favorite, created_at)
                        VALUES (:list_id, :titel,:a_description, :is_task_favorite, NOW())");
                            
                            
                        $stmt->bindParam(':list_id', $list_id, PDO::PARAM_INT);    
                        $stmt->bindParam(':titel', $task_name, PDO::PARAM_STR);
                        $stmt->bindParam(':a_description', $task_description, PDO::PARAM_STR);
                        $stmt->bindParam(':is_task_favorite', $task_favorite, PDO::PARAM_INT);
                        
                        $stmt->execute();

                        header("Location: index.php");

                    } catch (PDOException $e) {
        
                        echo "SQL-Fel: " . $e->getMessage();
                                
                    }
                }   
    }

   