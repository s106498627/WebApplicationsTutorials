<?php
    session_start();

    $show_answer = false;
    $giveup_message = false;
    
    function check_answer($guess, $ans) {
        if (!isset($guess)) {
            return false;
        }

        if ($guess == $ans) {
            return true;
        }

        return false;
    }
    
    if (!isset($_SESSION["answer"])) {
        $_SESSION["answer"] = rand(1, 100);
    }
    $num_to_guess = $_SESSION["answer"];

    if (!isset($_SESSION["guesses"])) {
        $_SESSION["guesses"] = 0;
    }
    $num_guesses = $_SESSION["guesses"];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $show_answer = check_answer($_POST["guessnum"], $num_to_guess);
        $num_guesses++;
        $_SESSION["guesses"] = $num_guesses;
    }

    if (isset($_SESSION["giveup"])) {
        $giveup_message = true;
    }

    if (isset($_SESSION["startover"])) {
        session_reset();
        session_destroy();
        header("location:guessinggame.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guessing Game</title>
</head>
<body>
    <p>(DBG) num is <?= $num_to_guess ?></p>

    <form method="post" action="guessinggame.php" novalidate>
        <p><input type="number" name="guessnum" id="guessnum"></p>
        <p><input type="submit" value="Guess"/></p>
    </form>

    <?php if($show_answer): ?>
        <p>Congratulations! You guessed the hidden number.</p>
    <?php endif; ?>

    <p>Number of guesses: <?= $num_guesses ?></p>

    <?php if ($giveup_message): ?>
        <p>Too bad, the answer was: <?= $num_to_guess ?></p>
    <?php endif; ?>

    <a href="giveup.php">Give Up</a>
    <a href="startover.php">Start Over</a>
</body>
</html>