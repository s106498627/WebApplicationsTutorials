<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retrieving records to HTML</title>
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
        $qry = "select make, model, price from cars order by make, model;";
        $res = mysqli_query($conn, $qry);

        if (!$res) {
            echo "<p>Something is wrong with the query!</p>";
        }

        echo "<table border=\"1\">\n";
        echo "<tr>"
            ."<th scope=\"col\">Make</th>"
            ."<th scope=\"col\">Model</th>"
            ."<th scope=\"col\">Price</th>"
            ."</tr>";

        while ($row = mysqli_fetch_assoc($res)) {
            echo "<tr>"
                ."<td>".$row["make"]."</td>"
                ."<td>".$row["model"]."</td>"
                ."<td>".$row["price"]."</td>"
                ."</tr>";            
        }
        echo "</table>";

        mysqli_free_result($res);
    }

    mysqli_close($conn);
    ?>
</body>
</html>