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
                <h1>Użytkownicy</h1>
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
                        <td width="15%">
                            <p>Imię</p>
                        </td>
                        <td width="15%">
                            <p>Nazwisko</p>
                        </td>
                        <td width="40%">
                            <p>Email</p>
                        </td>
                        <td width="30%">
                            Akcje
                        </td>
                    </thead>
                    <?php
                    $query = $mysqli->prepare("SELECT * FROM users");
                    $query->execute();
                    $result = $query->get_result();
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>";
                        echo "<p>" . $row['firstname'] . "</p>";
                        echo "</td>";
                        echo "<td>";
                        echo "<p>" . $row['lastname'] . "</p>";
                        echo "</td>";
                        echo "<td>";
                        echo "<p>" . $row['email'] . "</p>";
                        echo "</td>";
                        echo "<td>";
                        echo "<button onclick=\"window.location.href = 'add_user.php?id=" . $row['id'] . "'\">✏ Edytuj</button>";
                        echo "<button onclick=\"removeUser(" . $row['id'] . ")\">🗑 Usuń</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
                <button class="add" onclick="window.location.href = 'add_user.php'">Dodaj użytkownika</button>
            </div>
        </div>
    </div>
</body>
<script>
    function removeUser(id) {
        if (confirm("Czy na pewno chcesz usunąć tego użytkownika?")) {
            window.location.href = "remove_user.php?id=" + id;
        }
    }
</script>

</html>