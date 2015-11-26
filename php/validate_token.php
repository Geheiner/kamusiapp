<?php
function validate_token($token) {
    session_start();

    if($token == $_SESSION['token']) {
        return true;
    }
    return false;
}
?>
