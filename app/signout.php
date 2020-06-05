<?php
    require_once 'inc/user_logged_in_or_kick.php';

    if (!empty($_SESSION['user_id'])){
        session_destroy();
    }

    require_once('inc/utils.php');
    redirectToPageWithPost('signin.php', 'You have successfully signed out.', 'alert-success');
    exit();