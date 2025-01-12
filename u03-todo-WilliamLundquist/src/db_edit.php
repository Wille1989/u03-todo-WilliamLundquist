<?php

require_once 'db_init.php';


// REDIGERA LISTA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_edit_data'])){

    $list_name = trim($_POST['list_name']);
    $list_description = trim($_POST['list_description']);
    $list_favorite = isset($_POST['list_favorite']) ? 1 : 0;
    $list_done = isset($_POST['is_list_done']) ? 1 : 0;
    $list_id = $_POST['list_id'];

    require_once 'db_connection.php';
    require_once 'db_sql_handler.php';

        try {
       
            $stmt = $pdo->prepare(
                "UPDATE list 
                 SET titel = :titel, 
                    a_description = :a_description, 
                    is_list_favorite = :is_list_favorite,
                    created_at = NOW()
                 WHERE list_id = :list_id
                "); 

            $stmt->execute([
                ':titel' => $list_name,
                ':a_description' => $list_description,
                ':is_list_favorite' => $list_favorite,
                ':list_id' => $list_id
            ]);

            header("Location: index.php?list_id=$list_id");

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

    
        } catch (PDOException $e) {
            echo "Update Failed: " . $e->getMessage();
        }
}



// KONTROLLERA STATUS FÖR OM EN UPPGIFT ÄR KLAR I EN LISTA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['completed_task'])){

    $task_done = isset($_POST['is_task_done']) ? (int)$_POST['is_task_done'] : 0;
    $task_id = isset($_POST['task_id']) ? (is_array($_POST['task_id']) ? $_POST['task_id'][0] : $_POST['task_id']) : null;
    $list_id = isset($_POST['list_id']) ? (is_array($_POST['list_id']) ? $_POST['list_id'][0] : $_POST['list_id']) : null;

    require_once 'db_connection.php';
    require_once 'db_sql_handler.php';

        try {
    
            $stmt = $pdo->prepare("UPDATE task 
                                SET is_done = :is_done 
                                WHERE task_id = :task_id"); 

            $stmt->execute([':is_done' => $task_done, ':task_id' => $task_id]);
                
            header("Location: index.php?list_id=" . urlencode($list_id));
            
        } catch (PDOException $e) {
            echo "Update Failed: " . $e->getMessage();
        }
}



// KONTROLLERAR STATUS FÖR OM EN ENSKILD UPPGIFT ÄR KLAR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['taskStatus'])){

    $task_id = trim($_POST['task_id']);
    $is_task_done = isset($_POST['is_task_done']) ? 1 : 0;
    $is_task_favorite = isset($_POST['is_task_favorite']) ? 1 : 0;

    require_once 'db_connection.php';
    require_once 'db_sql_handler.php';

        try {
    
        $stmt = $pdo->prepare("UPDATE 
                                task 
                                SET 
                                is_done = :is_done, 
                                is_task_favorite = :is_task_favorite 
                                WHERE 
                                task_id = :task_id"); 


            $stmt->execute([':task_id' => $task_id, 
                            ':is_done' => $is_task_done, 
                            ':is_task_favorite' => $is_task_favorite]);
                                
            
            header("Location: index.php?=task_id=$task_id");
        
        } catch (PDOException $e) {
        echo "Update Failed: " . $e->getMessage();
        }
}



// REDIGERA ENSKILD UPPGIFT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_task_edit_data'])){

    $task_name = trim($_POST['task_name']);
    $task_description = trim($_POST['task_description']);
    $task_favorite = isset($_POST['task_favorite']) ? 1 : 0;
    $task_done = isset($_POST['is_task_done']) ? 1 : 0;
    $task_id = $_POST['task_id'];

    require_once 'db_connection.php';
    require_once 'db_sql_handler.php';

    try {
   
        $stmt = $pdo->prepare(
            "UPDATE task
            SET titel = :titel, 
                a_description = :a_description, 
                is_task_favorite = :is_task_favorite,
                is_done = :is_done,
                created_at = NOW()
            WHERE task_id = :task_id
            "); 

        $stmt->execute([
            ':titel' => $task_name,
            ':a_description' => $task_description,
            ':is_task_favorite' => $task_favorite,
            ':is_done' => $task_done,
            ':task_id' => $task_id
        ]);

        header("Location: index.php");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

        

    } catch (PDOException $e) {
        echo "Update Failed: " . $e->getMessage();
}
}




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_task_edit_data_in_list'])){

    $task_name = trim($_POST['task_name']);
    $task_id = $_POST['task_id'];
    $list_id = $_POST['list_id'];

    require_once 'db_connection.php';
    require_once 'db_sql_handler.php';

        try {
    
            $stmt = $pdo->prepare(
                                "UPDATE task
                                SET titel = :titel, 
                                created_at = NOW()
                                WHERE task_id = :task_id
                                "); 

            $stmt->execute([
                ':titel' => $task_name,
                ':task_id' => $task_id
            ]);

            header("Location: index.php?list_id=$list_id");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        

        } catch (PDOException $e) {
            echo "Update Failed: " . $e->getMessage();
        }
}



// TA BORT LISTA
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_list'])){

    require_once 'db_connection.php';
    require_once 'db_sql_handler.php';

    $list_id = $_POST['list_id'] ?? null;

    if(!empty($list_id)) {

        try {
            
            $stmt = $pdo->prepare("DELETE from list WHERE list_id = :list_id");

            $stmt->execute([':list_id'=> $list_id]);

            header("Location: index.php");

        } catch (PDOException $e) {
            echo "Delete Failed: " . $e->getMessage();
        }
    } else {
    Echo "Listan kan inte tas bort";
    }
}




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_remove_list'])){

    require_once 'db_connection.php';
    require_once 'db_sql_handler.php';

    $list_id = $_POST['list_id'] ?? null;

    if(!empty($list_id)){

    try {
        
        $stmt = $pdo->prepare("DELETE FROM list WHERE list_id = :list_id");
        $stmt->execute([':list_id' => $list_id]);

        
        header("Location: index.php");
        


    } catch (PDOException $e) {
        echo "Delete Failed: " . $e->getMessage();
    }
} else {

    Echo "Listan kan inte tas bort från Färdiga sektionen";
}
}



// TA BORT ENSKILD UPPGIFT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_remove_task'])){

    require_once 'db_connection.php';
    require_once 'db_sql_handler.php';

    $task_id = $_POST['task_id'] ?? null;

    if(!empty($task_id)){

        try {
            
            $stmt = $pdo->prepare("DELETE FROM task WHERE task_id = :task_id");
            $stmt->execute([':task_id' => $task_id]);

            
            header("Location: index.php");
            

        } catch (PDOException $e) {
            echo "Delete Failed: " . $e->getMessage();
        }
    } else {
        Echo "Listan kan inte tas bort från Färdiga sektionen";
    }
}


// TA BORT UPPGIFT I EN LISTA
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_task'])){

    require_once 'db_connection.php';
    require_once 'db_sql_handler.php';

    $list_id = $_POST['list_id'] ?? null;
    $task_id = $_POST['task_id'] ?? null;

    if($task_id){
        
        try {
            $stmt = $pdo->prepare("DELETE from task WHERE task_id = :task_id");

            $stmt->execute([':task_id'=> $task_id]);

            header("Location: index.php?list_id=$list_id");

        } catch (PDOException $e) {
            echo "Delete Failed: " . $e->getMessage();
        }

}
}




// KONTROLLERA VILLKOR FÖR OM EN REDIGERINGSVY FÖR LISTA SKA VISAS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_list']) && $_POST['edit_list'] == 1) {
    $list_id = $_POST['list_id'];
    header("Location: index.php?list_id=$list_id&edit_list=1");
    exit;
}



// KONTROLLERA VILLKOR FÖR OM EN REDIGERINGSVY SKA VISAS FÖR UPPGIFT I LISTA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_task']) && $_POST['edit_task'] == 1) {
    $task_id = $_POST['task_id']; 
    $in_list = isset($_POST['in_list']) && $_POST['in_list'] == 1 ? 1 : 0;
    $list_id = isset($_POST['list_id']) ? (int)trim($_POST['list_id']) : null;

    
    
    header("Location: index.php?list_id=$list_id&task_id=$task_id&edit_task=1&in_list=$in_list"); // Omdirigera till GET
    exit;
}

// ÖPPNA REDIGSERINGSVY FÖR ENSKILD UPPGIFT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['access_edit_task']) && isset($_POST['edit_seperate_task']) && $_POST['edit_seperate_task'] == 1) {

    $task_id = $_POST['task_id'];
    $in_list = isset($_POST['in_list']) ? (int)$_POST['in_list'] : 0;
   
   header("Location: index.php?task_id=$task_id&in_list=$in_list");
   exit;

}


// UPPDATERAR EN LISTAS VÄRDE OM FAVORIT ELLER EJ, SAMT KLAR/EJ KLAR
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['check_favNdone'])){

    $list_favorite = $_POST['list_favorite'] ? 1 : 0;
    $list_done = $_POST['is_list_done'] ? 1 : 0;
    $list_id = $_POST['list_id'];

    require_once 'db_connection.php';
    require_once 'db_sql_handler.php';

    try {

        $stmt = $pdo->prepare("UPDATE 
                                list 
                                SET 
                                is_done = :is_done, 
                                is_list_favorite = :is_list_favorite 
                                WHERE 
                                list_id = :list_id");
        
        
        $stmt->execute([':list_id' => $list_id,
                        ':is_done' => $list_done,
                        ':is_list_favorite' => $list_favorite]);

        header("Location: index.php?=list_id=$list_id");
        
    } catch (PDOException $e) {
        echo "error: Kunde inte uppdatera favorit eller klar" . $e->getMessage();
        return[];
    }
}