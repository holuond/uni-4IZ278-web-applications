<?php
    require_once('inc/examiner_logged_in_or_kick.php');
    require_once('inc/examiner_owns_test.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($test['activation_time']) {
            require_once('inc/utils.php');
            redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/examiner_dashboard.php', 'This test is already active.', 'alert-warning');
            exit();
        } else {
            $editTestQuery = $db->prepare("UPDATE tests SET activation_time=? WHERE test_id=?");
            $editTestQuery->execute([time(), $_POST['test_id']]);

            require_once('inc/utils.php');
            redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/examiner_dashboard.php', 'Test was successfully activated.', 'alert-success');
            exit();
        }
    } else {
        require_once('inc/utils.php');
        redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/examiner_dashboard.php', 'Wrong operation. POST method required.', 'alert-danger');
        exit();
    }

