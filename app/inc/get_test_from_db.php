<?php
    require_once('db.php');
    $testQuery = $db->prepare("SELECT * FROM tests WHERE test_id = ? LIMIT 1");
    $testQuery->execute([$_REQUEST['test_id']]);
    $test = $testQuery->fetch(PDO::FETCH_ASSOC);

    if (!$test){
        if (@$_SESSION['isexaminer']) {
            require_once('utils.php');
            redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/examiner_dashboard.php', 'Error: Test not found.', 'alert-danger');
            exit();
        } elseif (@$_SESSION['isexaminee']) {
            require_once('utils.php');
            redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/dashboard.php', 'Error: Test not found.', 'alert-danger');
            exit();
        } else {
            header('HTTP/1.0 401 Unauthorized');
            exit("401: Unauthorized");
        }
    }