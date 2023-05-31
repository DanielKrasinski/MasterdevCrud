<?php
session_start();
?>

<html>

<head>
    <title>Terminarz</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <div class="hero">
            <p class="title">TERMINARZ</p>
            <hr>
            <p class="subtitle">Mikołaj Patynowski i Dawid Jaworski</p>
        </div>
        <div class="main">
            <?php
            if (isset($_SESSION['error'])) {
                echo "<p class=\"error\">" . $_SESSION['error'] . "</p>";
                unset($_SESSION['error']);
            } else if (isset($_SESSION['success'])) {
                echo "<p class=\"success\">" . $_SESSION['success'] . "</p>";
                unset($_SESSION['success']);
            }
            ?>
            <form action="login.php" method="post" class="login">
                <p>Podaj poniższe dane ażeby się zalogować.</p>
                <input type="text" name="login" placeholder="Login">
                <input type="password" name="password" placeholder="Password">
                <input type="submit" value="Login">
            </form>
        </div>
    </div>
</body>

</html>

<?php
require_once "config.php";

if (isset($_COOKIE["user"])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $login = $_POST['login'];
    $password = $_POST['password'];

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Nieprawidłowe dane logowania!";
        header("Location: login.php");
        exit();
    }

    $user = $result->fetch_array();

    if (password_verify($password, $user['password'])) {
        setcookie("user", $user['id'], time() + 3600);
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['error'] = "Nieprawidłowe dane logowania!";
        header("Location: login.php");
        exit();
    }
}
