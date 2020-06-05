<?php
    if (@$_SESSION['isexaminer']) {
        require_once('utils.php');
        redirectToPageWithPost('examiner_dashboard.php', 'You are already logged in.', 'alert-info');
        exit();
    } elseif (@$_SESSION['isexaminee']) {
        require_once('utils.php');
        redirectToPageWithPost('dashboard.php', 'You are already logged in.', 'alert-info');
        exit();
    } else {
        header('HTTP/1.0 401 Unauthorized');
        exit("401: Unauthorized");
    }