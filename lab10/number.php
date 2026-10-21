<?php
    session_start();
    if (!isset($_SESSION["number"])) {
        $_SESSION["number"] = 0;
    }
    $num = $_SESSION["number"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 11</title>
</head>
<body>
    <h1>Lab 11</h1>
    <p>The number is <?= $num ?></p>
    <p><a href="numberup.php">Up &#x2191;</a></p>
    <p><a href="numberdown.php">Down &#x2193;</a></p>
    <p><a href="numberreset.php">Reset</a></p>
</body>
</html>