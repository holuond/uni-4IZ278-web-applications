<?php

    /*
     *  5 modes
     *  (all show correct answers)
     *
     *  examiner previewing a test while adding a new one
     *  --> return to "edit_test.php - add mode"
     *
     *  examiner previewing his draft test
     *  --> return to "edit_test.php - edit mode"
     *
     *  examiner looking at his active test
     *  --> return to "test_properties.php"
     *
     *  examiner looking at examinee's result
     *  --- shows examinee answers
     *  --> return to "test_properties.php"
     *
     *  examinee looking at his results
     *  --- shows examinee answers
     *  --> return to "dashboard.php"
     *
     *
     *  $test variable ALWAYS carries test info, doesn't matter which mode is used
     *
     *
     */

    require_once('inc/user_logged_in_or_kick.php');

    #region determine page mode and filter out wrong operations
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        if (empty($_GET['test_id'])) {
            // wrong operation
            require_once('inc/utils.php');
            redirectToCorrectDashboardWithPost('Wrong operation. Test not properly specified.', 'alert-danger');
            exit();
        } elseif (empty($_GET['user_id'])) {
            require_once('inc/examiner_owns_test.php');
            // mode: examiner viewing his active test
            $returnHref = 'test_properties.php?test_id=' . $test['test_id'];
            $fillInResultsMode = False;

        } elseif ($_SESSION['user_id'] == $_GET['user_id']) {
            require_once('inc/examinee_logged_in_or_kick.php');
            // mode: examinee looking at his own test results
            // --- shows examinee answers
            require_once('inc/get_test_from_db.php');
            $returnHref = 'dashboard.php';
            $fillInResultsMode = True;

        } else {
            require_once('inc/examiner_owns_test.php');
            // mode: examiner viewing examinee's result from a test he owns
            // --- shows examinee answers
            $returnHref = 'test_properties.php?test_id=' . $test['test_id'];
            $fillInResultsMode = True;
        }
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['name']) && !empty($_POST['xml'])) {
        if (empty($_POST['test_id'])) {
            // mode: examiner previewing a test while adding a new one
            $returnHref = 'edit_test.php'; // has to return via POST and carry name + xml
            $fillInResultsMode = False;
            $test = null;
            $test['test_id'] = null;
            $test['name'] = $_POST['name'];
            $test['xml'] = $_POST['xml'];
        } else {
            // mode: examiner previewing his draft test
            $returnHref = 'edit_test.php';
            $fillInResultsMode = False;
            $test = null;
            $test['test_id'] = $_POST['test_id'];
            $test['name'] = $_POST['name'];
            $test['xml'] = $_POST['xml'];
        }

    } else {
        require_once('inc/utils.php');
        redirectToCorrectDashboardWithPost('Wrong operation.', 'alert-danger');
        exit();
    }
    #endregion determine page mode and filter out wrong operations

    #region get results from db if $fillInResultsMode = True;
    if ($fillInResultsMode) {
        $testResultsQuery = $db->prepare("SELECT user_id, email, score, answersheet, submission_time FROM test_results JOIN users USING (user_id) WHERE test_id = ? AND user_id = ? LIMIT 1");
        $testResultsQuery->execute([$_GET['test_id'], $_GET['user_id']]);
        $testResult = $testResultsQuery->fetch();

        $percentageFormatter = new NumberFormatter('en_US', NumberFormatter::PERCENT);

        $tickedAnswers = explode(',', $testResult['answersheet']);
    }
    #endregion get results from db if $fillInResultsMode = True;

    //libxml_use_internal_errors(true);
    $xmlDom = new DOMDocument();
    $xmlDom->loadXML($test['xml']);
    if (!$xmlDom) {
        require_once('inc/utils.php');
        redirectToCorrectDashboardWithPost('Error. Invalid test contents.', 'alert-danger');
        exit();
    } elseif ($xmlDom->schemaValidate('inc/test_schema.xsd')) {
        // valid xml
        $xml = simplexml_import_dom($xmlDom);
    } else {
        require_once('inc/utils.php');
        redirectToCorrectDashboardWithPost('Error. Invalid test contents.', 'alert-danger');
        exit();
    }

    $pageTitle = 'View test ' . $test['name'];
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
            <div class="row mt-4">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <?php if ($fillInResultsMode) { ?>
                        <div class="row mt-1 mb-4">
                            <div class="col-md-12 text-center">
                                <?php if ($testResult['user_id'] != $_SESSION['user_id']) { ?>
                                    <small>Examinee: <?php echo($testResult['email']); ?></small>
                                    <br>
                                <?php } ?>
                                <small>Submission
                                    time: <?php echo(date('Y-m-d H:i:s', $testResult['submission_time'])); ?></small>
                                <br>
                                <small>Total
                                    score: <span
                                            class="badge badge-secondary"><b><?php echo($percentageFormatter->format($testResult['score'])); ?></b></span></small>
                            </div>
                        </div>
                    <?php } ?>
                    <form method="post" <?php echo($_SERVER["REQUEST_METHOD"] == "POST" ? 'action=' . $returnHref : ''); ?>>
                        <?php foreach ($xml as $question) {
                            $questionAnswerCount = sizeof($question->answers->children());
                            $correctAnswersCounter = 0;
                            ?>
                            <div class="form-group">
                                <div class="jumbotron pt-2 pb-2 mb-2 mt-4 bg-light">
                                    <?php echo(strval($question['id']) . '.  <small>' . $question->description . '</small>'); ?>
                                </div>
                                <?php foreach ($question->answers->answer as $answer) {
                                    $checkboxId = 'question' . strval($question['id']) . '_answer' . $answer['id'];
                                    if ($fillInResultsMode) {
                                        if (in_array($checkboxId, $tickedAnswers)) {
                                            $checkboxState = 'disabled checked';
                                            if ($answer['correct'] == 'true') {
                                                //ticked answer and correct answer (yay)
                                                $checkboxColor = 'green';
                                                $checkboxCorrectOrNotSymbol = ('✔');
                                                $correctAnswersCounter = ++$correctAnswersCounter;
                                            } else {
                                                //ticked answer but incorrect answer
                                                $checkboxColor = 'red';
                                                $checkboxCorrectOrNotSymbol = ('✘');
                                            }
                                        } else {
                                            $checkboxState = 'disabled';
                                            if ($answer['correct'] == 'true') {
                                                //unticked answer but correct answer
                                                $checkboxColor = 'red';
                                                $checkboxCorrectOrNotSymbol = ('✘');
                                            } else {
                                                //unticked answer and correct answer (yay)
                                                $correctAnswersCounter = ++$correctAnswersCounter;
                                                $checkboxColor = 'green';
                                                $checkboxCorrectOrNotSymbol = ('✔');
                                            }
                                        }
                                    } else {
                                        if ($answer['correct'] == 'true') {
                                            $checkboxState = 'disabled checked';
                                        } else {
                                            $checkboxState = 'disabled';
                                        }
                                    }
                                    ?>
                                    <div class="ml-5 form-check">
                                        <input class="form-check-input" type="checkbox"
                                               id="<?php echo($checkboxId) ?>" <?php echo($checkboxState) ?>>
                                        <label class="form-check-label" for="<?php echo($checkboxId) ?>">
                                            <small><?php echo($answer) ?></small>
                                            <?php if ($fillInResultsMode) { ?>
                                                <span style="color: <?php echo($checkboxColor); ?>;"> <?php echo($checkboxCorrectOrNotSymbol); ?></span>
                                            <?php } ?>
                                        </label>
                                    </div>
                                <?php } ?>
                                <?php if ($fillInResultsMode) { ?>
                                    <div class="text-right">
                                        <small><?php echo($percentageFormatter->format($correctAnswersCounter / $questionAnswerCount)) ?></small>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <div class="form-group row justify-content-center text-center">
                            <?php
                                if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['name']) && !empty($_POST['xml'])) {
                                    if (empty($test['test_id'])) {
                                        // (mode: examiner previewing a test while adding a new one)
                                        ?>
                                        <input type='hidden' name='name' value='<?php echo($test['name']); ?>'>
                                        <input type='hidden' name='testcontents' value='<?php echo($test['xml']); ?>'>
                                        <button class="btn btn-primary" type="submit">Go
                                            back
                                        </button>
                                        <?php
                                    } else {
                                        // (mode: examiner previewing his draft test)
                                        ?>
                                        <input type='hidden' name='name' value='<?php echo($test['name']); ?>'>
                                        <input type='hidden' name='testcontents' value='<?php echo($test['xml']); ?>'>
                                        <input type='hidden' name='test_id' value='<?php echo($test['test_id']); ?>'>
                                        <button class="btn btn-primary" type="submit">Go
                                            back
                                        </button>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <a href="<?php echo($returnHref); ?>" class="btn btn-primary" type="submit">Go
                                        back</a>
                                <?php } ?>
                        </div>
                    </form>
                </div>
                <div class="col-md-2"></div>
            </div>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>

<?php include('inc/footer.php'); ?>




