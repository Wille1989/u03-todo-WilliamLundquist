<?php

require_once 'db_read.php';
require_once 'db_create.php';
require_once 'db_init.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U03 - ToDo</title>
    <link rel="stylesheet" href="/style/styles.css">
</head>

<body class="main-content">

    <div class="section-create">
    <?php 
        
        if (isset($_SESSION['error'])) {
            $error = $_SESSION['error'];
            unset($_SESSION['error']);
        }
        ?>
        
        <!-- ATT GÖRA LISTA -->
        <div class="createList">
            <h3>Skapa Ny Lista</h3>

                <form action="db_create.php" method="POST">
                <input type="hidden" name="form_name" value="create_list">

                    <label for ="list_name"></label>
                    <input type="text" name ="list_name" placeholder="Ge ett namn ..."><br>
                        <?php if (!empty($error['list_name'])): ?>
                        <p class="error-message"><?= htmlspecialchars($error['list_name']) ?></p>
                        <?php endif; ?>

                    <label for="list_description"></label>
                    <textarea rows="5" cols="21" name="list_description" placeholder="Beskrivning ..."></textarea><br>
                        <?php if (!empty($error['list_description'])): ?>
                        <p class="error-message"><?= htmlspecialchars($error['list_description']) ?></p>
                        <?php endif; ?>

                    <label for="list_favorite">❤️</label>
                    <input type="radio" id="list_favorite" name="list_favorite">

                    <input type="submit" name="submit_send_data" value="➕"><br>
                </form>
        </div>

        <!-- ATT GÖRA UPPGIFT -->
        <div class="createTask">
                <h3>Skapa Ny Uppgift</h3>

                    <form action ="db_create.php" method="POST">

                        <label display="none" for="task_name"></label>
                            <?php if (!empty($error['task_name'])): ?>
                            <p class="error-message"><?= htmlspecialchars($error['task_name']) ?></p>
                            <?php endif; ?>
                        <input type="text" name ="task_name" placeholder="Skapa Ny Uppgift ..."><br>
                            

                        <label for="task_description"></label>
                            <?php if (!empty($error['task_description'])): ?>
                            <p class="error-message"><?= htmlspecialchars($error['task_description']) ?></p>
                            <?php endif; ?>
                        <textarea rows="5" cols="21" name="task_description" placeholder="Beskrivning ..."></textarea><br>
                            

                        <label for="task_favorite">❤️</label>
                        <input type="radio" name="task_favorite">

                        <input type="submit" name="submit_send_data_task" value="➕">
                    </form>
        </div>
    </div>




    <!-- SEKTION FÖR ATT VISA LISTOR OCH UPPGIFTER -->

    <div class="sectionShowListTask">
    <!-- Loopa hämtad data från list och skriv ut Titel, spara id.-->
        <div class="list">
            <h2>Aktiva Listor</h2>
            <?php
                // Hämta datan från databas
                $lists_by_id = fetch_id($pdo);

                // anger standard värde för synlighet på listan till false
                $has_favorite_lists = false;
                // Kontrollerar att variabeln innehåller data
                    if(!empty($lists_by_id)) :
                        foreach ($lists_by_id as $rows_of_id) :
                            if($rows_of_id['is_done'] == 0 && $rows_of_id['is_list_favorite'] == 0) : ?>
                               
                                <div class="active-item">
                                    <span class="status-circle-active-list"></span>
                                    <a href="index.php?list_id= <?= $rows_of_id['list_id'] ?>" class="item-btn-link">
                                    <?= htmlspecialchars($rows_of_id['titel']) ?>
                                    </a>
                                </div>
            <?php
                            elseif ($rows_of_id['is_list_favorite'] == 1 && $rows_of_id['is_done'] == 0) :
                            $has_favorite_lists = true; ?>
                            <div class="favorite-item">
                                <span class="favorite">🌟</span>
                                <a href="index.php?list_id= <?= $rows_of_id['list_id'] ?>" class="item-btn-link">
                                <?= htmlspecialchars($rows_of_id['titel']) ?>
                                </a>
                            </div>
                        <?php 
                            endif;    
                        endforeach;
                    endif; 
                    
                    if (empty($lists_by_id)) :
                        echo "Inga Aktiva Listor Hittades";
                    endif;
                    ?>

        </div>

        <!-- VISA EN ENSKILD UPPGIFT IHOP MED KNAPPAR -->

        <div class="task">
        <h3>Aktiva Uppgifter</h3><br>

<?php
                $tasks = fetch_task_seperate_id($pdo);
                $has_favorite_tasks = false;
    
                if(!empty($tasks)) :

                    foreach($tasks as $tasks_of_id) :

                        if($tasks_of_id['is_done'] == 0 && $tasks_of_id['is_task_favorite'] == 0) :
                            $has_active_tasks = true; ?>
                            <div class="active-task-item">
                                <span class="status-circle-active"></span>

                                <!-- Formulär för redigeringsknapp -->
                                <form action="db_edit.php" method="POST" class="inline-form">
                                   <span class="item-btn-link-nonHref"> <?= htmlspecialchars($tasks_of_id['titel']); ?></span>
                                   <span class="item-description"> <?= htmlspecialchars($tasks_of_id['a_description']); ?></span>
                                   
                                   <!-- Dold data för hantering -->
                                    <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($tasks_of_id['task_id']); ?>">
                                    <input type="hidden" name="edit_seperate_task" value="1">
                                    <input type="hidden" name="in_list" value="0">

                                    <!-- Knappen i formuläret -->
                                    <input type="submit" name="access_edit_task" value="✏️">
                                </form>
                            
                            
                                <!-- Checkboxar för markering klar/ej klar & klar/ej klar -->
                                <form action="db_edit.php" method="POST" class="inline-form">
                                    <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($tasks_of_id['task_id']); ?>">

                                    <!-- Checkbox för favorit -->
                                    <label>🌟</label>
                                    <input type="checkbox" name="is_task_favorite" value="1" <?php echo $tasks_of_id['is_task_favorite'] == 1 ? 'checked' : ''; ?>><br>

                                    <!-- Checkbox för klar -->
                                    <label>✔️</label>
                                    <input type="checkbox" name="is_task_done" value="1" <?php echo $tasks_of_id['is_done'] == 1 ? 'checked' : ''; ?>><br>

                                    <!-- Knappen i formuläret -->
                                    <input type="submit" name="taskStatus" value="💾">
                                </form>
                            </div><!-- STÄNGER: active-task-item -->
                        
            <?php
                        elseif ($tasks_of_id['is_task_favorite'] == 1 && $tasks_of_id['is_done'] == 0 ) :
                            $has_favorite_tasks = true; ?>
                                <div class="favorite-task-item">
                                    <span class="favorite">🌟</span>
                                        <!-- Formulär för redigeringsknapp -->
                                        <form action="db_edit.php" method="POST" class="inline-form-favorite">
                                        <span class="item-btn-link-nonHref"><?= htmlspecialchars($tasks_of_id['titel']); ?></span>
                                        <span class="item-description"> <?= htmlspecialchars($tasks_of_id['a_description']); ?></span>
                                                    
                                        
                                            <!-- Dold data för hantering -->
                                            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($tasks_of_id['task_id']); ?>">
                                            <input type="hidden" name="edit_seperate_task" value="1">
                                            <input type="hidden" name="in_list" value="0">

                                            <!-- Knappen i formuläret -->
                                            <input type="submit" name="access_edit_task" value="✏️">
                                        </form>
                                    
                                    
                                        <!-- Checkboxar för markering klar/ej klar & klar/ej klar -->
                                        <form action="db_edit.php" method="POST" class="inline-form-favorite">
                                            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($tasks_of_id['task_id']); ?>">

                                            <!-- Checkbox för favorit -->
                                            <label><span class="status-circle-active"></span></label>
                                            <input type="checkbox" name="is_task_favorite" value="0" <?php echo $tasks_of_id['is_task_favorite'] == 1 ? 'checked' : ''; ?>><br>

                                            <!-- Checkbox för klar -->
                                            <label>✔️</label>
                                            <input type="checkbox" name="is_task_done" value="1" <?php echo $tasks_of_id['is_done'] == 1 ? 'checked' : ''; ?>><br>

                                            <!-- Knappen i formuläret -->
                                            <input type="submit" name="taskStatus" value="💾">
                                        </form>
                                </div><!-- STÄNGER: favorite-task-item -->
                                
                            </a>
                            <?php
                        endif;
                    endforeach;
                    endif;
                        if (empty($tasks)) :
                            echo "Inga Aktiva Uppgifter Hittades";
                endif;
                ?>
        </div>
    </div>



    <!-- LIST VYN -->

    <div class="listView">
        <?php
            if (isset($_GET['list_id']) && is_numeric($_GET['list_id'])) {

                // Hämta list_id från URL:en
                $list_id = (int) $_GET['list_id'];

                // Hämta task_id från POST
                $task_id = isset($_POST['task_id']) ? (int)$_POST['task_id'] : (isset($_GET['task_id']) ? (int)$_GET['task_id'] : null);

                // Hämta detaljerad information
                $showListInTask = fetch_specifik_task($pdo, $task_id);
                $combined_results = combine_list_task($pdo, $list_id);
                $show_entire_list = fetch_specifik_list($pdo, $list_id);

                require_once 'db_edit.php';
                require_once 'db_init.php';

                    // Utskrift: Visar listans titel och beskrivning
                    if ($show_entire_list) : ?>

                    <div class="title-list-item">
                        <span class="status-circle-active-list"></span>
                            <div class="title-text">
                    <?php
                        echo "<br><h3><strong>" . htmlspecialchars($show_entire_list['titel']) . "</strong></h3>";
                    ?>

                                <!-- Formulär: Visar knapp för att redigera lista -->
                                <form action="db_edit.php" method="POST">
                                    <input type="hidden" name="list_id" value="<?php echo htmlspecialchars($show_entire_list['list_id']); ?>">
                                    <input type="hidden" name="edit_list" value="1">

                                    <input type="submit" name="access_edit_list" value="✏️"><br>
                                </form>
                            

                        <!-- Formulär för checkbox klar/ej klar, favorit/ej favorit -->
                        <form action="db_edit.php" method='POST'>
                            <input type="hidden" name="list_id" value="<?php echo htmlspecialchars($list_id); ?>">

                            <input type="hidden" name="list_favorite" value="0">
                            <input type="hidden" name="is_list_done" value="0">

                            <label>❤️</label>
                            <input type="checkbox" name="list_favorite" value="1" <?php echo $show_entire_list['is_list_favorite'] == 1 ? 'checked' : ''; ?>><br>

                            <label>✔️</label>
                            <input type="checkbox" name="is_list_done" value="1" <?php echo $show_entire_list['is_done'] == 1 ? 'checked' : ''; ?>><br>

                            <input type="submit" name="check_favNdone" value="&#x1F4BE;">
                        </form>

                            </div> <!-- Stänger "Title-text" -->
                    </div><!-- Stänger "title-list-item" -->

                    <!-- BESKRIVNING FÖR LISTAN -->
                    <div class="descriptionOfList">
                    <?php echo "<p>" . htmlspecialchars($show_entire_list['a_description']) . "</p>"; ?>
                    </div><!-- Stänger "descriptionOfList" -->
                    
                    
                    <!--SKAPA EN UPPGIFT I LISTAN-->
                <form action ="db_create.php" method="POST" class="inline-form-add_task">
                    <input type="hidden" name="list_id" value="<?= htmlspecialchars($list_id); ?>">

                    <div class="input-container">
                    <!-- Input för att skriva uppgiftens namn -->
                    <?php if (!empty($error['task_name'])): ?>
                    <p class="error-message"><?= htmlspecialchars($error['task_name']) ?></p>
                    <?php endif; ?>
                    <label for ="task_name"></label>
                    <input type="text" name ="task_name" placeholder="Uppgift i listan ...">

                    <!-- Knapp för att lägga till uppgift i lista -->
                    <input type="submit" name="add_task" value="➕">
                    </div>
                    <a href="index.php" class="regret-button">&#x21A9;</a>
                </form>
            <?php
            endif;

                // Formulär: Aktiera vy för REDIGERING AV LISTAN
                $list_id = isset($_GET['list_id']) ? (int)$_GET['list_id'] : null;
                $edit_mode = isset($_GET['edit_list']) && $_GET['edit_list'] == 1;

                    if ($edit_mode && $list_id) :
                ?>
                        <form action="db_edit.php" method="POST">
                            <input type="hidden" name="list_id" value="<?php echo htmlspecialchars($list_id); ?>">

                            <label><br>Titel</label><br>
                            <input type="text" name="list_name" value="<?php echo htmlspecialchars($show_entire_list['titel']); ?>"><br>

                            <label>Beskrivning</label><br>
                            <textarea name="list_description"><?php echo htmlspecialchars($show_entire_list['a_description']); ?></textarea><br>

                            <input type="submit" name="submit_edit_data" value="Uppdatera Listan">

                        </form>
                        <a href="index.php?list_id=<?php echo htmlspecialchars($list_id); ?>" class="regret-button">&#x21A9;</a>
                <?php
                    endif;

                // Kontrollera att variabeln inte är tom
                if(!empty($combined_results)) {

                    // Loopa igenom resultatet av hämtad data.
                    foreach ($combined_results as $results) {
                        if (!empty($results['task_id'])) {
                            if(isset($results['task_title']) && $results['task_title'] !== null) :
                ?>
                        <div class="inline-form_task">
                        <!-- Formulär: Markera klar/ej klar -->
                        <form action="db_edit.php" method="post">
                            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($results['task_id']); ?>">
                            <input type="hidden" name="list_id" value="<?php echo htmlspecialchars($results['list_id']); ?>">

                            <span><?php echo isset($results['task_title']) ? htmlspecialchars($results['task_title']) : 'Ingen titel'; ?></span>

                            <label>
                                <input type="hidden" name="is_task_done" value="0">
                                <label>✔️</label>
                                <input type="checkbox" name="is_task_done" value="1"
                                <?php echo $results['task_done'] ? 'checked' : ''; ?>>
                            </label>

                            <input type="submit" name="completed_task" value="💾">
                        </form>
                        <?php endif; ?>

                    <!-- Formulär: Ta bort uppgift -->
                    <form action="db_edit.php" method="post">
                        <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($results['task_id']); ?>">
                        <input type="hidden" name="list_id" value="<?php echo htmlspecialchars($results['list_id']); ?>">
                        <input type="submit" name="delete_task" value="&#x1F5D1;">
                    </form>

                    <!-- Formulär: Redigera uppgift -->
                    <form action="db_edit.php" method="POST">
                        <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($results['task_id']); ?>">
                        <input type="hidden" name="list_id" value="<?php echo htmlspecialchars($results['list_id']); ?>">
                        <input type="hidden" name="edit_task" value="1">
                        <input type="hidden" name="in_list" value="1">
                        <input type="submit" name="access_edit_task" value="✏️">
                    </form>
                    </div> <!-- STÄNGER inline-form_task -->
            <?php


                // Formulär: Aktiera vy för REDIGERING AV EN UPPGIFT SOM FINNS I LISTAN

                $task_id = isset($_GET['task_id']) ? (int)$_GET['task_id'] : null;
                $edit_mode = isset($_GET['edit_task']) && $_GET['edit_task'] == 1;
                $in_list = isset($_GET['in_list']) && $_GET['in_list'] == 1;

                    if ($edit_mode && $task_id && $in_list) {
                 
                            if ($results['task_id'] == $task_id) {
                    ?>
                        <div class="inline-form-visible">
                        <form action="db_edit.php" method="POST">
                            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task_id); ?>">
                            <input type="hidden" name="list_id" value="<?php echo htmlspecialchars($list_id); ?>">

                            <label><br>Uppgiftens namn:</label><br>
                            <input type="text" name="task_name" value="<?php echo htmlspecialchars($results['task_title'])  ? htmlspecialchars($results['task_title']) : ''; ?>"><br>

                            <input type="submit" name="submit_task_edit_data_in_list" value="Spara">
                        </form>
                        <a href="index.php?list_id=<?php echo htmlspecialchars($list_id); ?>" class="regret-button">&#x21A9;</a>
                        </div> <!-- Stänger "inline-form-visible" -->
                    
            <?php
                            }
                        }
                    }
                        }
                }
                    }

            ?>

              <!-- VY FÖR ENSKILDA UPPGIFTER  I EN LISTA -->
              <?php
                if (isset($_GET['task_id']) && (!isset($_GET['in_list']) || $_GET['in_list'] == 0)) {
                    $task_id = trim($_GET['task_id']);
                    $edit_mode = isset($_GET['edit_seperate_task']) && $_GET['edit_seperate_task'] == 1;
                    $show_entire_task = fetch_specifik_task($pdo, $task_id); ?>
                    <div class="show_task">

                    <?php 
                    require_once 'db_edit.php';

                        if($show_entire_task){
                            echo "<h3><span class='task-title'>" . htmlspecialchars($show_entire_task['titel']) . "</span></h3>";
                            echo "<p><span class='task-description'>" . htmlspecialchars($show_entire_task['a_description']) . "</span></p>";
                        } else {
                            echo "<p>Uppgiften kunde inte hittas.</p>";
                        }
            ?>  
                    <div class="inline-form-show_task">
                    <form action="db_edit.php" method='POST'>
                        <!-- Dolt ID för Uppgift -->
                        <input type="hidden" name="task_id"
                                value="<?php echo htmlspecialchars($show_entire_task['task_id']); ?>">
                

                        <!-- Ändra titeln på uppgiften -->
                        <label>Ändra Uppgiften:</label>
                        <input type="text" name="task_name"
                                value="<?php echo htmlspecialchars($show_entire_task['titel']); ?>">

                        <label>Ändra Beskrivningen:</label>
                        <input type="text" name="task_description"
                        value="<?php echo htmlspecialchars($show_entire_task['a_description']); ?>">

                        <!-- Knappar i formuläret -->
                         
                        <input type="submit" name="submit_task_edit_data" value="&#x1F501;">
                        <input type="submit" name="delete_task" value="&#x1F5D1;">
                    </form>
                        <a href="index.php?list_id=$list_id<?php echo htmlspecialchars($task_id); ?>" id="regret-button">&#x21A9;</a>
                    </div><!-- Stänger "show_task" -->

                    
         <?php
                }
            ?>
        </div><!-- Stänger "listView" -->





              
    </div>
    



    <!-- FÄRDIGA LISTOR OCH UPPGIFTER -->

    <!-- VISAR UPPGIFTER & LISTOR MARKERADE SOM KLAR-->
    <!-- Renderar en knapp för att ta bort lista & Uppgift -->
    <div class="completedForms">
        <div class="completedList">
    <h3>FÄRDIGT</h3>
        <?php
            // Hämtar datan från databasen
            $completed = fetch_completed_LT($pdo);
            $displayed_lists = [];
            // Kontroll att variabel innehåller data
            if(!empty($completed)) {
                foreach($completed as $complete) {
                    
                    // // Skriver ut titeln för lista
                    if(!empty($complete['l_titel']) && !in_array($complete['l_id'], $displayed_lists)) {
                        $displayed_lists[] = $complete['l_id'];


                        echo '<div class="completed-item">';
                        echo '<span class="status-circle-completed"></span>';
                        echo htmlspecialchars($complete['l_titel']) . "<br>";

        ?>
                        <!-- Visar knapp för att ta bort en lista som är klar -->
                        <form action="db_edit.php" method="POST" class="inline-form">
                            <input type="hidden" name="list_id" value="<?php echo htmlspecialchars($complete['l_id'])?>">
                            <input type="submit" name="submit_remove_list" value="&#x1F5D1;"><br>
                        </form>
                        </div> <!-- Stänger div för formulär -->
        </div>
        <?php
                    }
                }
        ?>
        
    

        <div class="completedTask">
        <?php
                    // Enskild uppgift som inte ligger i en lista
                    // Kontroll att variabel innehåller data
                    // Skriver ut titeln för uppgift
                    foreach($completed as $complete) {
                    if (empty($complete['l_id']) && !empty($complete['t_titel'])){
                        echo '<div class="completed-item">';
                        echo '<span class="status-circle-completed"></span>';
                        echo htmlspecialchars($complete['t_titel']) . "<br>";

        ?>
                    <!-- Visar knapp för att ta bort en uppgift som är klar -->
                    <form action="db_edit.php" method="POST" class="inline-form">
                        <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($complete['t_id'])?>">
                        <input type="submit" name="submit_remove_task" value="&#x1F5D1;"><br>
                    </form>
                    
        <?php
                    }
                }
        ?>
                    </div><!-- Stänger div för formulär -->
                </div><!-- Stänger div för completed-item -->

        <div class="nothingCompleted">
        <?php
            // Om inga listor eller uppgifter är klara
            } else {
                echo "<em>Inga uppgifter eller listor är klara</em>";
            }
        ?>
        </div><!-- Stänger div för Inga uppgifter eller listor klara -->

    </div>
</div>

</body>
</html>