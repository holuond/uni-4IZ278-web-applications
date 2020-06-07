<?php
    require_once('inc/examiner_logged_in_or_kick.php');
    require_once('inc/examiner_owns_test.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['test_id']) && !empty($_POST['user_id'])) {
        // user may not exist or may not be an examinee
        $examineeQuery = $db->prepare("SELECT user_id, email, isexaminee FROM users WHERE user_id = ? LIMIT 1");
        $examineeQuery->execute([$_POST['user_id']]);
        $examinee = $examineeQuery->fetch();

        // test if user has a result in the first place
        $testUserQuery = $db->prepare("SELECT user_id FROM test_results WHERE test_id = ? AND user_id = ? AND submission_time IS NOT NULL LIMIT 1");
        $testUserQuery->execute([$_POST['test_id'], $_POST['user_id']]);
        $testUser = $testUserQuery->fetch();


        if (!$examinee) {
            require_once('inc/utils.php');
            redirectToPageWithPost('test_properties.php?test_id=' . htmlspecialchars($_POST['test_id']), 'Wrong operation. User does not exist.', 'alert-danger');
            exit();
        } elseif ($testUser) {
            $removeResultQuery = $db->prepare("UPDATE test_results SET score = NULL, submission_time = NULL, answersheet = NULL WHERE test_id = ? AND user_id = ?");
            $removeResultQuery->execute([$_POST['test_id'], $_POST['user_id']]);

            require_once('inc/utils.php');
            redirectToPageWithPost('test_properties.php?test_id=' . htmlspecialchars($_POST['test_id']), 'Test submission (result and answersheet) has been removed.  (' . @$examinee['email'] . ')', 'alert-success');
            exit();
        } else {
            require_once('inc/utils.php');
            redirectToPageWithPost('test_properties.php?test_id=' . htmlspecialchars($_POST['test_id']), 'The examinee did not have a submitted result.', 'alert-warning');
            exit();
        }
    } else {
        require_once('inc/utils.php');
        redirectToPageWithPost('test_properties.php?test_id=' . htmlspecialchars($_POST['test_id']), 'Wrong operation.', 'alert-danger');
        exit();
    }

