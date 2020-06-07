<?php
    session_start();

    if (!isset($_SESSION["user_id"])) {
        require_once('utils.php');
        redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/signin.php', 'You are not signed in.', 'alert-danger');
        exit();
    }

