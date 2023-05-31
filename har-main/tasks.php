<?php
session_start();
if (!isset($_COOKIE["user"])) {
    $_SESSION['error'] = "Nie jesteś zalogowany!";
    header("Location: login.php");
    exit();
}

require_once "config.php";
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
                <h1>Zadania</h1>
                <?php
                if (isset($_SESSION['error'])) {
                    echo "<p class=\"error\">" . $_SESSION['error'] . "</p>";
                    unset($_SESSION['error']);
                } else if (isset($_SESSION['success'])) {
                    echo "<p class=\"success\">" . $_SESSION['success'] . "</p>";
                    unset($_SESSION['success']);
                }
                ?>
                <table class="crud">
                    <thead>
                        <td width="20%">
                            <p>Nazwa i opis</p>
                        </td>
                        <td width="20%">
                            <p>Od kiedy?</p>
                        </td>
                        <td width="20%">
                            <p>Do kiedy?</p>
                        </td>
                        <td width="20%">
                            <p>Użytkownik przypisany</p>
                        </td>
                        <td width="25%">
                            Akcje
                        </td>
                    </thead>
                    <?php
                    $query = $mysqli->prepare("SELECT * FROM tasks");
                    $query->execute();
                    $result = $query->get_result();
                    while ($row = $result->fetch_assoc()) {
                        $users_ids = explode(",", $row['assigned_to']);
                        $assigned = "";

                        foreach ($users_ids as $user_id) {
                            $queryUsers = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
                            $queryUsers->bind_param("i", $user_id);
                            $queryUsers->execute();
                            $resultUsers = $queryUsers->get_result();
                            $user = $resultUsers->fetch_array();
                            if($user) {
                                $assigned .= $user['firstname'] . " " . $user['lastname'] . "<br>";
                            }
                        }

                        echo "<tr>";
                        echo "<td>";
                        echo "<p>" . $row['name'] . "<hr>" . $row['description'] . "</p>";
                        echo "</td>";
                        echo "<td>";
                        echo "<p>" . $row['from_to'] . "</p>";
                        echo "</td>";
                        echo "<td>";
                        echo "<p>" . $row['due_to'] . "</p>";
                        echo "</td>";
                        echo "<td>";
                        echo "<p>" . $assigned . "</p>";
                        echo "</td>";
                        echo "<td>";
                        echo "<button onclick=\"window.location.href = 'add_task.php?id=" . $row['id'] . "'\">✏ Edytuj</button>";
                        echo "<button onclick=\"removeTask(" . $row['id'] . ")\">🗑 Usuń</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
                <button class="add" onclick="window.location.href = 'add_task.php'">Dodaj zadanie</button>
            </div>
        </div>
    </div>
</body>
<script>
    function removeTask(id) {
        if (confirm("Czy na pewno chcesz usunąć to wydarzenie z terminarza?")) {
            window.location.href = "remove_task.php?id=" + id;
        }
    }
</script>

</html>