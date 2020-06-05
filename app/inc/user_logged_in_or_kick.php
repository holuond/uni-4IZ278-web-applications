<?php
    session_start();

    if (!isset($_SESSION["user_id"])) {
        require_once('utils.php');
        redirectToPageWithPost('signin.php', 'You are not signed in.', 'alert-danger');
        exit();
    }

