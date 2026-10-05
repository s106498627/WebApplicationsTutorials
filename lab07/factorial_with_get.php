<body>
    <?php
        require_once("mathfunctions.php"); 
    ?>
    <h1>Creating Web Applications - Lab 8</h1>
    <?php
        $num = null;
        if (isset($_GET['number'])) {
            $num = $_GET['number'];
        }

        if (isPositiveInteger($num)) {
            $result = factorial($num);
            echo "<p>The factorial of $num is $result.</p>";
        } else {
            echo "<p>Please enter a positive integer.</p>";
        }
        echo "<p><a href='factorial.php'>Return to the entry page</a></p>";
    ?>
</body>