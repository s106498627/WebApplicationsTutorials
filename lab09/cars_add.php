<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adding records to HTML</title>
</head>
<body>
    <h1>
        Creating Web Applications - Lab 10
    </h1>

    <?php
    require_once("settings.php");

    $conn = @mysqli_connect($host, $user, $pwd, $sql_db);

    if (!$conn) {
        echo "<p>Database connection error</p>";
    } else {
        $sql_table = "cars";
        $make = trim(mysqli_escape_string($conn, $_POST["carmake"]));
        $model = trim(mysqli_escape_string($conn, $_POST["carmodel"]));
        $price = trim(mysqli_escape_string($conn, $_POST["price"]));
        $yom = trim(mysqli_escape_string($conn, $_POST["yom"]));

        $qry = "insert into $sql_table (make, model, price, yom) values ('$make', '$model', '$price', '$yom');";
        $res = mysqli_query($conn, $qry);

        if (!$res) {
            echo "<p>Something is wrong with the query!</p>";
        } else {
            echo "<p class=\"ok\">Successfully added new car record!</p>";
        }
    }

    mysqli_close($conn);
    ?>
</body>
</html>