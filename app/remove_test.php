<?php
    require_once('inc/examiner_logged_in_or_kick.php');
    require_once('inc/examiner_owns_test.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $editTestQuery = $db->prepare("DELETE FROM tests WHERE test_id=?");
        $editTestQuery->execute([$_POST['test_id']]);

        require_once('inc/utils.php');
        redirectToPageWithPost('examiner_dashboard.php', 'Test was successfully removed.', 'alert-success');
        exit();
    } else {
        require_once('inc/utils.php');
        redirectToPageWithPost('examiner_dashboard.php', 'Wrong operation. POST method required.', 'alert-danger');
        exit();
    }

