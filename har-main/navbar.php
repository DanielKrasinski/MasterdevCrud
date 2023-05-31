<?php
$file = basename($_SERVER['PHP_SELF']);

require_once "config.php";
$query = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
$query->bind_param("i", $_COOKIE["user"]);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_array();
?>

<div class="hero_index">
    <p class="title">TERMINARZ</p>
    <hr>
    <p class="subtitle">Patyna i MinerPL</p>
    <ul class="menu">
        <li onclick="window.location.href = 'index.php'" <?php echo $file === "index.php" ? "class=\"active\"" : "" ?>>🏠 Strona główna</li>
        <li onclick="window.location.href = 'users.php'" <?php echo $file === "users.php" ? "class=\"active\"" : "" ?>>👨🏻 Użytkownicy</li>
        <li onclick="window.location.href = 'tasks.php'" <?php echo $file === "tasks.php" ? "class=\"active\"" : "" ?>>📋 Zadania</li>
        <li onclick="window.location.href = 'plan.php'" <?php echo $file === "plan.php" ? "class=\"active\"" : "" ?>>📅 Plan</li>
        <li onclick="window.location.href = 'logout.php'">🚪 Wyloguj</li>
    </ul>

    <?php
    echo "<div class='logged'>Zalogowano jako " . $user['firstname'] . " " . $user['lastname'] . "!</div>";
    ?>
</div>