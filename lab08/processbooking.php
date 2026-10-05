<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        require_once("helpers.php");
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
</head>
<body>
    <h1>Rohirrim Tour Booking Confirmation</h1>
    <?php
    $formItems = ["firstname" => "", "lastname" => "", "species" => "", "age" => "", "food" => "", "partySize" => ""];
    $tripVals = ["1day", "4day", "10day"];

    foreach ($formItems as $item => $val) {
        if (!isset($_POST[$item]) || $_POST[$item] == "") {

            echo "<p>Error: missing $item in the <a href='register.html'>form</a></p>";
            exit();
        }
        // input ok, sanitize and store in variable
        $formItems[$item] = sanitize($_POST[$item]);
    }

    $selections = [];
    foreach ($tripVals as $trip) {
        if (isset($_POST[$trip])) {
            $selections[] = sanitize($_POST[$trip]);
        }
    }

    if (empty($selections)) {
        echo "<p>Error: no tour selected in the <a href='register.html'>form</a></p>";
        exit();
    }

    $dayString = buildDayString($selections);

    echo "<p>
    Welcome, $formItems[firstname] $formItems[lastname]!<br>
    you are booked on the $dayString<br>
    Species: $formItems[species]<br>
    Age: $formItems[age]<br>
    Meal preference: $formItems[food]<br>
    Number of travellers: $formItems[partySize]
    </p>"

    ?>
</body>
</html>