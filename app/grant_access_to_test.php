<?php
    require_once('inc/examiner_logged_in_or_kick.php');
    require_once('inc/examiner_owns_test.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['test_id']) && !empty($_POST['user_id'])) {
        // user may already have access
        $testUserQuery = $db->prepare("SELECT user_id FROM test_results WHERE test_id = ? AND user_id = ? LIMIT 1");
        $testUserQuery->execute([$_POST['test_id'], $_POST['user_id']]);
        $testUser = $testUserQuery->fetch();

        // user may not exist or may not be an examinee
        $examineeQuery = $db->prepare("SELECT user_id, email, isexaminee FROM users WHERE user_id = ? LIMIT 1");
        $examineeQuery->execute([$_POST['user_id']]);
        $examinee = $examineeQuery->fetch();

        if (!$examinee) {
            require_once('inc/utils.php');
            redirectToPageWithPost('test_properties.php?test_id=' . htmlspecialchars($_POST['test_id']), 'Wrong operation. User does not exist.', 'alert-danger');
            exit();
        } elseif (!@$examinee['isexaminee']){
            require_once('inc/utils.php');
            redirectToPageWithPost('test_properties.php?test_id=' . htmlspecialchars($_POST['test_id']), 'Wrong operation. User does not have the examinee role.', 'alert-danger');
            exit();
        } elseif ($testUser) {
            require_once('inc/utils.php');
            redirectToPageWithPost('test_properties.php?test_id=' . htmlspecialchars($_POST['test_id']), 'The examinee already has access.', 'alert-warning');
            exit();
        } else {
            $editTestQuery = $db->prepare("INSERT INTO test_results (test_id, user_id) VALUES (?, ?)");
            $editTestQuery->execute([$_POST['test_id'], $_POST['user_id']]);

            require_once('inc/utils.php');
            redirectToPageWithPost('test_properties.php?test_id=' . htmlspecialchars($_POST['test_id']), 'User ' . @$examinee['email'] . ' can now access the test.', 'alert-success');
            exit();
        }
    } else {
        require_once('inc/utils.php');
        redirectToPageWithPost('test_properties.php?test_id=' . htmlspecialchars($_POST['test_id']), 'Wrong operation.', 'alert-danger');
        exit();
    }

