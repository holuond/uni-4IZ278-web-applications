<?php
    require_once('inc/examiner_logged_in_or_kick.php');
    require_once('inc/db.php');

    $testQuery = $db->prepare("SELECT * FROM tests WHERE user_id = ? ORDER BY test_id");
    $testQuery->execute([$_SESSION['user_id']]);
    $tests = $testQuery->fetchAll();

    $pageTitle = 'Examiner Dashboard';
    include('inc/header.php');


    #region filter active vs. draft tests
    function activeTests($test)
    {
        if ($test['activation_time']) {
            return True;
        }
        return False;
    }

    function draftTests($test)
    {
        if ($test['activation_time']) {
            return False;
        }
        return True;
    }

    $activeTests = array_filter($tests, "activeTests");
    $draftTests = array_filter($tests, "draftTests");
    #endregion filter active vs. draft tests
?>


    <div class="container-fluid">
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h1 class="text-center">
                            Examiner Dashboard
                        </h1>
                    </div>
                </div>
                <div class="row mt-4">
                    <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            if (!empty($_POST['balloonMessage']) && !empty($_POST['balloonType'])) {
                                echo '<div class="alert ' . $_POST['balloonType'] . '">' . $_POST['balloonMessage'] . '</div>';
                            }
                        }
                    ?>
                    <div class="col-md-12 text-center">
                        <a href="edit_test.php" class="btn btn-primary">Add new test</a>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-md-6">
                        <div class="text-center"><h5>Draft tests</h5></div>
                        <div class="jumbotron pt-4 pb-3">
                            <?php if (empty($draftTests)) {
                                echo('<p class="mt-2 mb-2 text-center"><small>You do not have any draft tests.</small></p>');
                            } else {
                                foreach ($draftTests as $draftTest) { ?>
                                    <div class="container-fluid mb-3">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <p class="mt-2 mb-2">
                                                    <small><?php echo(htmlspecialchars($draftTest['name'])); ?></small>
                                                </p>
                                            </div>
                                            <div class="col-md-7">
                                                <div class="d-flex w-100 flex-column flex-md-row align-items-center">
                                                    <a class="btn btn-sm ml-auto btn-outline-secondary p-1 m-1 pr-2 pl-2"
                                                       href="edit_test.php?test_id=<?php echo($draftTest['test_id']); ?>"><small>Edit</small></a>
                                                    <form action="activate_test.php" method="POST"
                                                          onsubmit="return confirm('Are you sure you want to activate this test? This cannot be undone.');">
                                                        <button type="submit" name="activateDraft" id="activateDraft"
                                                                value="<?php echo($draftTest['test_id']); ?>"
                                                                class="btn btn-sm btn-outline-secondary p-1 m-1 pr-2 pl-2">
                                                            <small>Activate</small></button>
                                                    </form>
                                                    <form action="remove_test.php" method="POST"
                                                          onsubmit="return confirm('Are you sure you want to remove this test? This cannot be undone.');">
                                                        <button type="submit" name="removeDraft" id="removeDraft"
                                                                value="<?php echo($draftTest['test_id']); ?>"
                                                                class="btn btn-sm btn-outline-secondary p-1 m-1 pr-2 pl-2">
                                                            <small>Remove</small></button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                <?php }
                            } ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-center"><h5>Active tests</h5></div>
                        <div class="jumbotron pt-4 pb-3">
                            <?php if (empty($activeTests)) {
                                echo('<p class="mt-2 mb-2 text-center"><small>You do not have any active tests.</small></p>');
                            } else {
                                foreach ($activeTests as $activeTest) { ?>
                                    <div class="container-fluid mb-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mt-2 mb-2">
                                                    <small><?php echo(htmlspecialchars($activeTest['name'])); ?></small>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex w-100 flex-column flex-md-row align-items-center">
                                                    <a class="btn btn-sm ml-auto btn-outline-secondary p-1 m-1 pr-2 pl-2"
                                                       href="test_properties.php?test_id=<?php echo($activeTest['test_id']); ?>"><small>Properties</small></a>
                                                    <form action="remove_test.php" method="POST"
                                                          onsubmit="return confirm('Are you sure you want to remove this test? This cannot be undone.');">
                                                        <button type="submit" name="removeActive" id="removeActive"
                                                                value="<?php echo($activeTest['test_id']); ?>"
                                                                class="btn btn-sm btn-outline-secondary p-1 m-1 pr-2 pl-2">
                                                            <small>Remove</small></button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
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