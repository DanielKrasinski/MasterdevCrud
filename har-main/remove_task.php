<?php
session_start();

if (!isset($_COOKIE["user"])) {
    $_SESSION['error'] = "Nie jesteś zalogowany!";
    header("Location: login.php");
    exit();
}

require_once "config.php";

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Nie podano id zadania!";
    header("Location: tasks.php");
    exit();
}

$id = $_GET['id'];

$stmt = $mysqli->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Zadanie nie istnieje!";
    header("Location: tasks.php");
    exit();
}

$stmt = $mysqli->prepare("DELETE FROM tasks WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$_SESSION['success'] = "Zadanie zostało usuniętę!";
header("Location: tasks.php");
