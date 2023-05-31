<?php
if (!isset($_COOKIE["user"])) {
    $_SESSION['error'] = "Nie jesteś zalogowany!";
    header("Location: login.php");
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
            <div class="title_block">
                <h1>HAR</h1>
            </div>
            <div class="whats_new">
                <h3>Nowości i zmiany</h3><br>
                <p>31.05.2023</p>
                <p>Koniec projektu</p>
                <br><hr style="margin-left: 11.25vw; width: 30vw;"><br>
                <p>30.05.2023</p>
                <p>Rozpoczęcie projektu</p>
            </div>
        </div>
    </div>
</body>

</html>