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
    $query = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Nie znaleziono użytkownika!";
        header("Location: users.php");
        exit();
    }
    $editUser = $result->fetch_array();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $id = $_GET['id'];
    if (isset($id)) {
        $query = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $result = $query->get_result();
        if ($result->num_rows === 0) {
            $_SESSION['error'] = "Nie znaleziono użytkownika!";
            header("Location: users.php");
            exit();
        }
        $editUser = $result->fetch_array();

        $sql = "UPDATE users SET firstname = ?, lastname = ?, email = ?, password = ? WHERE id = ?";
    } else {
        $sql = "INSERT INTO users SET firstname = ?, lastname = ?, email = ?, password = ?";
    }

    $stmt = $mysqli->prepare($sql);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    if ($password === "") {
        $hashedPassword = $editUser['password'];
    }

    if (isset($id)) {
        $stmt->bind_param("ssssi", $firstname, $lastname, $email, $hashedPassword, $id);
    } else {
        $stmt->bind_param("ssss", $firstname, $lastname, $email, $hashedPassword);
    }
    $stmt->execute();

    if (isset($id)) {
        $_SESSION['success'] = "Użytkownik został zaktualizowany!";
    } else {
        $_SESSION['success'] = "Użytkownik został stworzony!";
    }
    header("Location: users.php");
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
                if (isset($editUser)) {
                    echo "<h1>Edytuj użytkownika</h1>";
                    $action = "add_user.php?id=" . $editUser['id'];
                } else {
                    echo "<h1>Dodaj użytkownika</h1>";
                    $action = "add_user.php";
                }

                if (isset($_SESSION['error'])) {
                    echo "<p class=\"error\">" . $_SESSION['error'] . "</p>";
                    unset($_SESSION['error']);
                } else if (isset($_SESSION['success'])) {
                    echo "<p class=\"success\">" . $_SESSION['success'] . "</p>";
                    unset($_SESSION['success']);
                }

                echo "<form method=\"POST\" action=\"" . $action . "\">";
                if (isset($editUser)) {
                    echo "<div class=\"editor\">";
                    echo "<input name=\"email\" class=\"editori\" placeholder=\"Mail\" value=\"" . $editUser['email'] . "\">";
                    echo "<input name=\"firstname\" class=\"editori\" placeholder=\"Imie\" value=\"" . $editUser['firstname'] . "\">";
                    echo "<input name=\"lastname\" class=\"editori\" placeholder=\"Nazwisko\" value=\"" . $editUser['lastname'] . "\">";
                    echo "<input name=\"password\" class=\"editori\" placeholder=\"Hasło\" >";
                    echo "<button class=\"add\">Zapisz</button>";
                    echo "</div>";
                } else {
                    echo "<div class=\"editor\">";
                    echo "<input name=\"email\" class=\"editori\" placeholder=\"Mail\">";
                    echo "<input name=\"firstname\" class=\"editori\" placeholder=\"Imie\">";
                    echo "<input name=\"lastname\" class=\"editori\" placeholder=\"Nazwisko\">";
                    echo "<input name=\"password\" class=\"editori\" placeholder=\"Hasło\">";
                    echo "<button class=\"add\">Dodaj</button>";
                }
                echo "</form>";
                ?>
            </div>
        </div>
</body>

</html>