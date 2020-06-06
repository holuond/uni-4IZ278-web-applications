<?php

    require_once('examiner_logged_in_or_kick.php');
    require_once('get_test_from_db.php');

    if (empty($test['user_id']) || @$test['user_id'] != $_SESSION['user_id']) {
        header('HTTP/1.0 401 Unauthorized');
        exit("401: Unauthorized");
    }