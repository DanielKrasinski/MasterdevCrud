<?php
session_start();
require_once "config.php";
if (!isset($_COOKIE["user"])) {
    $_SESSION['error'] = "Nie jesteś zalogowany!";
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = $mysqli->prepare("SELECT * FROM tasks WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Nie znaleziono zadania!";
        header("Location: tasks.php");
        exit();
    }
    $editTask = $result->fetch_array();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $name = $_POST['name'];
    $descirption = $_POST['description'];
    $from_to = $_POST['date_from'];
    $due_to = $_POST['date'];
    $assigned_to = "";

    foreach ($_POST as $key => $value) {
        $checkboxeArray = explode('_', $key);
        if (count($checkboxeArray) === 2 && $checkboxeArray[0] === "checkboxe" && $value === "on") {
            $assigned_to .= $checkboxeArray[1] . ",";
        }
    }

    $id = $_GET['id'];
    if (isset($id)) {
        $query = $mysqli->prepare("SELECT * FROM tasks WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $result = $query->get_result();
        if ($result->num_rows === 0) {
            $_SESSION['error'] = "Nie znaleziono zadania!";
            header("Location: tasks.php");
            exit();
        }

        $sql = "UPDATE tasks SET name = ?, description = ?, from_to = ?, due_to = ?, assigned_to = ? WHERE id = ?";
    } else {
        $sql = "INSERT INTO tasks SET name = ?, description = ?, from_to = ?, due_to = ?, assigned_to = ?";
    }

    $stmt = $mysqli->prepare($sql);

    if (isset($id)) {
        $stmt->bind_param("sssssi", $name, $descirption, $from_to, $due_to, $assigned_to, $id);
    } else {
        $stmt->bind_param("sssss", $name, $descirption, $from_to, $due_to, $assigned_to);
    }
    $stmt->execute();

    if (isset($id)) {
        $_SESSION['success'] = "Zadanie zostało zaktualizowane!";
    } else {
        $_SESSION['success'] = "Zadanie zostało stworzone!";
    }
    header("Location: tasks.php");
    exit();
}

?>

<html>

<head>
    <title>Terminarz</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <?php
        require "navbar.php";
        ?>
        <div class="main_index">
            <div>
                <?php
                if (isset($editTask)) {
                    echo "<h1>Edytuj zadanie</h1>";
                    $action = "add_task.php?id=" . $editTask['id'];
                } else {
                    echo "<h1>Dodaj zadanie</h1>";
                    $action = "add_task.php";
                }

                if (isset($_SESSION['error'])) {
                    echo "<p class=\"error\">" . $_SESSION['error'] . "</p>";
                    unset($_SESSION['error']);
                } else if (isset($_SESSION['success'])) {
                    echo "<p class=\"success\">" . $_SESSION['success'] . "</p>";
                    unset($_SESSION['success']);
                }

                echo "<form method=\"POST\" action=\"" . $action . "\">";
                if (isset($editTask)) {
                    echo "<div class=\"editor\">";
                    echo "<input name=\"name\" class=\"editori\" placeholder=\"Nazwa zadania\" value=\"" . $editTask['name'] . "\">";
                    echo "<input name=\"description\" class=\"editori\" placeholder=\"Opis zadania\" value=\"" . $editTask['description'] . "\">";
                    echo "<input type=\"datetime-local\" name=\"date_from\" class=\"editori\" placeholder=\"Czas rozpoczęcia\" value=\"" . $editTask['from_to'] . "\">";
                    echo "<input type=\"datetime-local\" name=\"date\" class=\"editori\" placeholder=\"Czas na wykonanie\" value=\"" . $editTask['due_to'] . "\">";
                    echo "<div id='min'><p>Przypisane do: </p><span id='mind'></span></div>";
                    $usersQuery = $mysqli->prepare("SELECT * FROM users");
                    $usersQuery->execute();
                    $usersResult = $usersQuery->get_result();
                    $users_ids = explode(",", $editTask['assigned_to']);
                    echo "<div class='ilist_container'>";
                    while ($user = $usersResult->fetch_assoc()) {
                        if (in_array($user['id'], $users_ids)) {
                            echo "<div class='ilist'><input checked name=\"checkboxe_" . $user['id'] . "\" type='checkbox'>" . $user['firstname'] . " " . $user['lastname'] . "</option></div><br>";
                        } else {
                            echo "<div class='ilist'><input name=\"checkboxe_" . $user['id'] . "\" type='checkbox'>" . $user['firstname'] . " " . $user['lastname'] . "</option></div><br>";
                        }
                    }
                    echo "</div>";
                    echo "<button class=\"add\">Zapisz</button>";
                    echo "</div>";
                } else {
                    echo "<div class=\"editor\">";
                    echo "<input name=\"name\" class=\"editori\" placeholder=\"Nazwa Zadania\">";
                    echo "<input name=\"description\" class=\"editori\" placeholder=\"Opis Zadania\">";
                    echo "<input type=\"datetime-local\" name=\"date_from\" class=\"editori\" placeholder=\"Czas rozpoczęcia\">";
                    echo "<input type=\"datetime-local\" name=\"date\" class=\"editori\" placeholder=\"Czas na wykonanie\">";
                    echo "<div id='min'><p>Przypisane do: </p><span id='mind'></span></div>";
                    $usersQuery = $mysqli->prepare("SELECT * FROM users");
                    $usersQuery->execute();
                    $usersResult = $usersQuery->get_result();
                    echo "<div class='ilist_container'>";
                    while ($user = $usersResult->fetch_assoc()) {
                        echo "<div class='ilist'><input name=\"checkboxe_" . $user['id'] . "\" type='checkbox'>" . $user['firstname'] . " " . $user['lastname'] . "</option></div><br>";
                    }
                    echo "</div>";
                    echo "<button class=\"add\">Dodaj</button>";
                }
                echo "</form>";
                ?>
            </div>
        </div>
</body>

</html>