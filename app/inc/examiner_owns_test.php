<?php

    require_once('examiner_logged_in_or_kick.php');
    require_once('db.php');
    $testOwnerQuery = $db->prepare("SELECT * FROM tests WHERE user_id = ? AND test_id = ? LIMIT 1");
    $testOwnerQuery->execute([$_SESSION['user_id'], $_REQUEST['test_id']]);
    $test = $testOwnerQuery->fetch(PDO::FETCH_ASSOC);

    if (!$test) {
        header('HTTP/1.0 401 Unauthorized');
        exit("401: Unauthorized");
    }