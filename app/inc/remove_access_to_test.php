<?php
    require_once('examiner_logged_in_or_kick.php');
    require_once('examiner_owns_test.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['test_id']) && !empty($_POST['user_id'])) {
        // user may not exist or may not be an examinee
        $examineeQuery = $db->prepare("SELECT user_id, email, isexaminee FROM users WHERE user_id = ? LIMIT 1");
        $examineeQuery->execute([$_POST['user_id']]);
        $examinee = $examineeQuery->fetch();

        // user may not have access already
        $testUserQuery = $db->prepare("SELECT user_id FROM test_results WHERE test_id = ? AND user_id = ? LIMIT 1");
        $testUserQuery->execute([$_POST['test_id'], $_POST['user_id']]);
        $testUser = $testUserQuery->fetch();


        if (!$examinee) {
            require_once('utils.php');
            redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/test_properties.php?test_id=' . htmlspecialchars($_POST['test_id']), 'Wrong operation. User does not exist.', 'alert-danger');
            exit();
        } elseif ($testUser) {
            $editTestQuery = $db->prepare("DELETE FROM test_results WHERE test_id = ? AND user_id = ?");
            $editTestQuery->execute([$_POST['test_id'], $_POST['user_id']]);

            require_once('utils.php');
            redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/test_properties.php?test_id=' . htmlspecialchars($_POST['test_id']), 'User access to the test has been revoked.  (' . @$examinee['email'] . ')', 'alert-success');
            exit();
        } else {
            require_once('utils.php');
            redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/test_properties.php?test_id=' . htmlspecialchars($_POST['test_id']), 'The examinee already did not have access.', 'alert-warning');
            exit();
        }
    } else {
        require_once('utils.php');
        redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/test_properties.php?test_id=' . htmlspecialchars($_POST['test_id']), 'Wrong operation.', 'alert-danger');
        exit();
    }

