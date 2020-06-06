<?php
    require_once('inc/examiner_owns_test.php');

    if (!$test['activation_time']) {
        require_once('inc/utils.php');
        redirectToPageWithPost('examiner_dashboard.php', 'Invalid operation. This test is not active.', 'alert-danger');
        exit();
    }

    $testResultQuery = $db->prepare("SELECT test_results.*, users.email as email FROM test_results JOIN users USING (user_id) WHERE test_results.test_id = ? ORDER BY test_results.submission_time DESC");
    $testResultQuery->execute([$_REQUEST['test_id']]);
    $testResults = $testResultQuery->fetchAll();

    #region submitted results vs. not yet submitted
    function submittedResults($testResult)
    {
        if ($testResult['submission_time']) {
            return True;
        }
        return False;
    }

    function notYetSubmitted($testResult)
    {
        if ($testResult['submission_time']) {
            return False;
        }
        return True;
    }

    $submittedResults = array_filter($testResults, "submittedResults");
    $awaitingSubmissionArray = array_filter($testResults, "notYetSubmitted");
    #endregion submitted results vs. not yet submitted

    #region get those not invited - without access to this test
    $testUsersQuery = $db->prepare("SELECT user_id FROM test_results WHERE test_results.test_id = ? ORDER BY user_id ASC");
    $testUsersQuery->execute([$_REQUEST['test_id']]);
    $testUsers = $testUsersQuery->fetchAll();

    $allExamineesQuery = $db->query("SELECT user_id FROM users WHERE isexaminee = 1 ORDER BY user_id ASC");
    $allExaminees = $allExamineesQuery->fetchAll();

    function getOnlyUserIds($arrayMember)
    {
        return $arrayMember['user_id'];
    }

    $examineeIdsWithoutAccess = array_diff(
        array_map('getOnlyUserIds', $allExaminees),
        array_map('getOnlyUserIds', $testUsers));

    $examineesWithoutAccessQuery = $db->query("SELECT user_id, email 
          FROM `users` 
         WHERE `user_id` IN (" . implode(',', array_map('intval', $examineeIdsWithoutAccess)) . ")");
    $notInvitedArray = $examineesWithoutAccessQuery->fetchAll();
    #endregion get those not invited - without access to this test


    $percentageFormatter = new NumberFormatter('en_US', NumberFormatter::PERCENT);

    $pageTitle = 'Test Properties - ' . htmlspecialchars($test['name']);
    include('inc/header.php');
?>


    <div class="container-fluid">
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h1 class="text-center">
                            <?php echo($pageTitle); ?>
                        </h1>
                    </div>
                </div>
                <div class="row mt-2">
                    <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            if (!empty($_POST['balloonMessage']) && !empty($_POST['balloonType'])) {
                                echo '<div class="alert ' . $_POST['balloonType'] . '">' . $_POST['balloonMessage'] . '</div>';
                            }
                        }
                    ?>
                    <div class="col-md-12 text-center">
                        <small>Active since <?php echo(date('Y-m-d H:i:s', $test['activation_time'])); ?></small>
                    </div>
                </div>
                <div class="row mt-4 justify-content-center">
                    <h3>Examinees</h3>
                </div>

                <div class="row mt-3 <?php echo (empty($awaitingSubmissionArray) && empty($submittedResults)) ? 'justify-content-center' : '' ?>">
                    <?php if (empty($awaitingSubmissionArray) && empty($submittedResults)) {
                        echo '<div class="alert alert-info"><small>No students have access to this test.</small></div>';
                    } else { ?>
                        <div class="col-md-7">
                            <div class="text-center"><h5>Results</h5></div>
                            <div class="jumbotron pt-4 pb-3">
                                <?php if (empty($submittedResults)) {
                                    // customize the "no results yet" message based on whether there are any examinees on the list or not
                                    $messageIfNone = empty($awaitingSubmissionArray) ? '' : 'Nobody has finished the test so far.';
                                    echo('<p class="mt-2 mb-2 text-center"><small>' . $messageIfNone . '</small></p>');
                                } else {
                                    $notFirstFlag = null;
                                    foreach ($submittedResults as $submittedResult) {
                                        if ($notFirstFlag) {
                                            echo('<hr>');
                                        } else {
                                            $notFirstFlag = 1;
                                        } ?>
                                        <div class="container-fluid mb-3">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <p class="mt-2 mb-2">
                                                        <small><?php echo(htmlspecialchars($submittedResult['email'])); ?></small>
                                                    </p>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="d-flex w-100 flex-column flex-md-row align-items-center">
                                                        <small><span
                                                                    class="ml-auto"><?php echo($percentageFormatter->format($submittedResult['score'])); ?></span></small>
                                                        <a class="btn btn-sm ml-auto btn-outline-secondary p-1 m-1 pr-2 pl-2"
                                                           href="view_test.php?test_id=<?php echo($test['test_id']); ?>&user_id=<?php echo($submittedResult['user_id']); ?>"><small>View</small></a>
                                                        <form action="remove_test_result.php" method="POST"
                                                              onsubmit="return confirm('Are you sure you want to remove this test result? This cannot be undone.');">
                                                            <input type='hidden' name='test_id'
                                                                   value='<?php echo($test['test_id']); ?>'>
                                                            <button type="submit" name="user_id" id="removeTestResult"
                                                                    value="<?php echo($submittedResult['user_id']); ?>"
                                                                    class="btn btn-sm btn-outline-secondary p-1 m-1 pr-2 pl-2">
                                                                <small>Remove</small></button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }
                                } ?>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="text-center"><h5>Awaiting submission</h5></div>
                            <div class="jumbotron pt-4 pb-3">
                                <?php if (empty($awaitingSubmissionArray)) {
                                    // customize the "not yet submitted is empty" message based on whether there are any examinees on the list or not
                                    $messageIfNone = empty($submittedResults) ? '' : 'All invited examinees have already submitted their work.';
                                    echo('<p class="mt-2 mb-2 text-center"><small>' . $messageIfNone . '</small></p>');
                                } else {
                                    $notFirstFlag = null;
                                    foreach ($awaitingSubmissionArray as $awaitingSubmission) {
                                        if ($notFirstFlag) {
                                            echo('<hr>');
                                        } else {
                                            $notFirstFlag = 1;
                                        } ?>
                                        <div class="container-fluid mb-3">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mt-2 mb-2">
                                                        <small><?php echo(htmlspecialchars($awaitingSubmission['email'])); ?></small>
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex w-100 flex-column flex-md-row align-items-center">
                                                        <form action="remove_access_to_test.php" method="POST"
                                                              onsubmit="return confirm('Are you sure you want to remove this examinee\'s access to the test? If the user is already in progress, test submission will not be allowed.');">
                                                            <input type='hidden' name='test_id'
                                                                   value='<?php echo($test['test_id']); ?>'>
                                                            <button type="submit" name="user_id" id="removeAccessToTest"
                                                                    value="<?php echo($awaitingSubmission['user_id']); ?>"
                                                                    class="btn btn-sm btn-outline-secondary p-1 m-1 pr-2 pl-2">
                                                                <small>Remove access</small></button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }
                                } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="row mt-4 justify-content-center">
                    <h3>Not invited</h3>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <div class="jumbotron pt-4 pb-3">
                            <?php if (empty($notInvitedArray)) {
                                $messageIfNone = 'All students are already on the examinee list.';
                                echo('<p class="mt-2 mb-2 text-center"><small>' . $messageIfNone . '</small></p>');
                            } else {
                                $notFirstFlag = null;
                                foreach ($notInvitedArray as $notInvited) {
                                    if ($notFirstFlag) {
                                        echo('<hr>');
                                    } else {
                                        $notFirstFlag = 1;
                                    } ?>
                                    <div class="container-fluid mb-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mt-2 mb-2">
                                                    <small><?php echo(htmlspecialchars($notInvited['email'])); ?></small>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex w-100 flex-column flex-md-row justify-content-end">
                                                    <form action="grant_access_to_test.php" method="POST"
                                                          onsubmit="return confirm('Are you sure you want to grant this examinee access to the test?');">
                                                        <input type='hidden' name='test_id'
                                                               value='<?php echo($test['test_id']); ?>'>
                                                        <button type="submit" name="user_id" id="grantAccessToTest"
                                                                value="<?php echo($notInvited['user_id']); ?>"
                                                                class="btn btn-sm btn-outline-secondary p-1 m-1 pr-2 pl-2">
                                                            <small>Grant access</small></button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php }
                            } ?>
                        </div>
                    </div>
                    <div class="col-md-3"></div>
                </div>
            </div>
            <div class="col-md-1"></div>
        </div>
    </div>

<?php include('inc/footer.php'); ?>