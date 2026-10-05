<?php 
function sanitize($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

function buildDayString($inputs) {
    if (empty($inputs)) {
        return "no days selected";
    } else if (count($inputs) == 1) {
        return substr($inputs[0], 0, -3) . " day tour.";
    } else  if (count($inputs) == 2) {
        return substr($inputs[0], 0, -3) . " day and " . substr($inputs[1], 0, -3) .  " day tours.";
    } else  if (count($inputs) == 3) {
        return substr($inputs[0], 0, -3) . " day, " . substr($inputs[1], 0, -3) .  " day and " . substr($inputs[2], 0, -3) . " day tours.";
    } else {
        return "error: too many days selected";
    }
}
?>