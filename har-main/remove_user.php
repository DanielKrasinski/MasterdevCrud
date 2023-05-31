<?php
session_start();

if (!isset($_COOKIE["user"])) {
    $_SESSION['error'] = "Nie jesteś zalogowany!";
    header("Location: login.php");
    exit();
}

require_once "config.php";

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Nie podano id użytkownika!";
    header("Location: users.php");
    exit();
}

$id = $_GET['id'];

$stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($_COOKIE['user'] == $id) {
    $_SESSION['error'] = "Nie możesz usunąć samego siebie!";
    header("Location: users.php");
    exit();
}

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Użytkownik nie istnieje!";
    header("Location: users.php");
    exit();
}

$stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$_SESSION['success'] = "Użytkownik został usunięty!";
header("Location: users.php");
