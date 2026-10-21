<?php
session_start();
$_SESSION["giveup"] = true;
header("location:guessinggame.php")
?>