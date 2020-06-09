<?php
    require_once('inc/examinee_logged_in_or_kick.php');
    require_once('inc/db.php');

    $usersTestsQuery = $db->prepare("SELECT test_id, `name`, test_results.user_id as user_id, score, answersheet, submission_time FROM test_results JOIN tests USING (test_id) WHERE test_results.user_id = ? ORDER BY test_id");
    $usersTestsQuery->execute([$_SESSION['user_id']]);
    $usersTests = $usersTestsQuery->fetchAll();

    #region filter submitted vs. available tests
    function submittedTests($test)
    {
        if ($test['submission_time']) {
            return True;
        }
        return False;
    }

    function availableTests($test)
    {
        if ($test['submission_time']) {
            return False;
        }
        return True;
    }

    $submittedTests = array_filter($usersTests, "submittedTests");
    $availableTests = array_filter($usersTests, "availableTests");
    #endregion filter submitted vs. available tests

    $percentageFormatter = new NumberFormatter('en_US', NumberFormatter::PERCENT);

    $pageTitle = 'Dashboard';
    include('inc/header.php');
?>


    <div class="container-fluid">
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h1 class="text-center">
                            Dashboard
                        </h1>
                    </div>
                </div>
                <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        if (!empty($_POST['balloonMessage']) && !empty($_POST['balloonType'])) {
                            echo('<div class="row mt-4">');
                            echo('<div class="alert ' . $_POST['balloonType'] . '">' . $_POST['balloonMessage'] . '</div>');
                            echo('</div>');
                        }
                    }
                ?>
                <div class="row mt-5">
                    <div class="col-md-6">
                        <div class="text-center"><h5>Submitted tests</h5></div>
                        <div class="jumbotron pt-4 pb-3">
                            <?php if (empty($submittedTests)) {
                                echo('<p class="mt-2 mb-2 text-center"><small>You have not submitted any tests yet.</small></p>');
                            } else {
                                $notFirstFlag = null;
                                foreach ($submittedTests as $submittedTest) {
                                    if ($notFirstFlag) {
                                        echo('<hr>');
                                    } else {
                                        $notFirstFlag = 1;
                                    } ?>
                                    <div class="container-fluid mb-3">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <p class="mt-2 mb-2">
                                                    <small><?php echo(htmlspecialchars($submittedTest['name'])); ?></small>
                                                </p>
                                            </div>
                                            <div class="col-md-7">
                                                <div class="d-flex w-100 flex-column flex-md-row align-items-center">
                                                    <small><span
                                                                class="ml-auto"><?php echo($percentageFormatter->format($submittedTest['score'])); ?></span></small>
                                                    <a class="btn btn-sm ml-auto btn-outline-secondary p-1 m-1 pr-2 pl-2"
                                                       href="view_test.php?test_id=<?php echo($submittedTest['test_id']); ?>&user_id=<?php echo($_SESSION['user_id']); ?>"><small>View</small></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php }
                            } ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-center"><h5>Available tests</h5></div>
                        <div class="jumbotron pt-4 pb-3">
                            <?php if (empty($availableTests)) {
                                echo('<p class="mt-2 mb-2 text-center"><small>No tests are available for you right now.</small></p>');
                            } else {
                                $notFirstFlag = null;
                                foreach ($availableTests as $availableTest) {
                                    if ($notFirstFlag) {
                                        echo('<hr>');
                                    } else {
                                        $notFirstFlag = 1;
                                    } ?>
                                    <div class="container-fluid mb-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mt-2 mb-2">
                                                    <small><?php echo(htmlspecialchars($availableTest['name'])); ?></small>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex w-100 flex-column flex-md-row align-items-center">
                                                    <a class="btn btn-sm ml-auto btn-outline-secondary p-1 m-1 pr-2 pl-2"
                                                       onclick="return confirm('Are you sure you want to start up this test?');"
                                                       href="take_test.php?test_id=<?php echo($availableTest['test_id']); ?>&user_id=<?php echo($_SESSION['user_id']); ?>"><small>Launch</small></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php }
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1"></div>
        </div>
    </div>

<?php include('inc/footer.php'); ?>