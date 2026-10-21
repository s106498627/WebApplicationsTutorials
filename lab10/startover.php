<?php
session_start();
$_SESSION["startover"] = true;
header("location:guessinggame.php")
?>