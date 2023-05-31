<?php
if (!isset($_COOKIE["user"])) {
    $_SESSION['error'] = "Nie jesteś zalogowany!";
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
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
            <h1>Tygodniowy Harmonogram
                <div class="weekly">
                    <table id="table" class="crud">
                        <thead>
                            <td>godziny</td>
                            <td>poniedziałek</td>
                            <td>wtorek</td>
                            <td>środa</td>
                            <td>czwartek</td>
                            <td>piątek</td>
                            <td>sobota</td>
                        </thead>
                        <?php
                        require_once 'config.php';

                        $current_date = new DateTime();
                        $current_date->modify('monday this week');
                        $current_date->setTime(0, 0, 0);

                        $date_object = new stdClass();
                        $date_object->monday = $current_date->format('d');
                        $date_object->tuesday = $current_date->modify('+1 day')->format('d');
                        $date_object->wednesday = $current_date->modify('+1 day')->format('d');
                        $date_object->thursday = $current_date->modify('+1 day')->format('d');
                        $date_object->friday = $current_date->modify('+1 day')->format('d');
                        $date_object->saturday = $current_date->modify('+1 day')->format('d');

                        $hours = array_map(function ($hour) {
                            return $hour . ':00';
                        }, range(0, 23));

                        foreach ($hours as $hour) {
                            echo '<tr>';
                            echo '<td>' . $hour . '</td>';
                            echo '<td id="' . $date_object->monday . '_' . $hour . '"></td>';
                            echo '<td id="' . $date_object->tuesday . '_' . $hour . '"></td>';
                            echo '<td id="' . $date_object->wednesday . '_' . $hour . '"></td>';
                            echo '<td id="' . $date_object->thursday . '_' . $hour . '"></td>';
                            echo '<td id="' . $date_object->friday . '_' . $hour . '"></td>';
                            echo '<td id="' . $date_object->saturday . '_' . $hour . '"></td>';
                            echo '</tr>';
                        }
                        ?>
                    </table>
                </div>
        </div>
    </div>
</body>

</html>

<?php
$query = $mysqli->prepare('SELECT * FROM tasks');
$query->execute();
$result = $query->get_result();

$tasks = array_filter($result->fetch_all(MYSQLI_ASSOC), function ($task) {
    $assigned_users = explode(',', $task['assigned_to']);
    return in_array($_COOKIE['user'], $assigned_users);
});

$tasks = array_filter($tasks, function ($task) use ($date_object) {
    $from_day = explode(' ', $task['from_to'])[0];
    $day = explode('-', $from_day)[2];

    return (int)$day >= (int)$date_object->saturday;
});


$tasks = array_filter($tasks, function ($task) use ($date_object) {
    $from_day = explode(' ', $task['from_to'])[0];
    $day = explode('-', $from_day)[2];

    return (int)$day >= (int)$date_object->monday;
});

foreach ($tasks as $task) {
    $from_to = explode(' ', $task['from_to']);
    $due_to = explode(' ', $task['due_to']);

    $startDay = (int)(explode('-', $from_to[0])[2]);
    $endDay = (int)(explode('-', $due_to[0])[2]);

    $startHour = (int)(explode(':', $from_to[1])[0]);
    $endHour = (int)(explode(':', $due_to[1])[0]);

    if ($startDay == $endDay) {
        for ($i = $startHour; $i <= $endHour; $i++) {
            echo '<script>';
            echo 'document.getElementById("' . $startDay . '_' . $i . ':00").innerHTML += "' . $task['name'] . '<br>";';
            echo '</script>';
        }
    } else {

        for ($i = $startHour; $i <= 23; $i++) {
            echo '<script>';
            echo 'document.getElementById("' . $startDay . '_' . $i . ':00").innerHTML += "' . $task['name'] . '<br>";';
            echo '</script>';
        }

        for ($i = 0; $i <= $endHour; $i++) {
            echo '<script>';
            echo 'document.getElementById("' . $endDay . '_' . $i . ':00").innerHTML += "' . $task['name'] . '<br>";';
            echo '</script>';
        }

        for ($i = $startDay + 1; $i < $endDay; $i++) {
            for ($j = 0; $j <= 23; $j++) {
                echo '<script>';
                echo 'document.getElementById("' . $i . '_' . $j . ':00").innerHTML += "' . $task['name'] . '<br>";';
                echo '</script>';
            }
        }
    }
}
