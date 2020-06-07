<?php
    require_once('examiner_logged_in_or_kick.php');
    require_once('examiner_owns_test.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $editTestQuery = $db->prepare("DELETE FROM tests WHERE test_id=?");
        $editTestQuery->execute([$_POST['test_id']]);

        require_once('utils.php');
        redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/examiner_dashboard.php', 'Test was successfully removed.', 'alert-success');
        exit();
    } else {
        require_once('utils.php');
        redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/examiner_dashboard.php', 'Wrong operation. POST method required.', 'alert-danger');
        exit();
    }

