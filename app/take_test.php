<?php

    require_once('inc/examinee_logged_in_or_kick.php');
    require_once('inc/get_test_from_db.php');

    $testResultsQuery = $db->prepare("SELECT user_id, email, score, answersheet, submission_time FROM test_results JOIN users USING (user_id) WHERE test_id = ? AND user_id = ? LIMIT 1");
    $testResultsQuery->execute([$_REQUEST['test_id'], $_REQUEST['user_id']]);
    $testResult = $testResultsQuery->fetch();

    if ($testResult){
        if (!empty($testResult['submission_time'])){
            require_once('inc/utils.php');
            redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/dashboard.php', 'You have already taken this test!.', 'alert-danger');
            exit();
        }
    } else {
        require_once('inc/utils.php');
        redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/dashboard.php', 'Unauthorized.', 'alert-danger');
        exit();
    }

    //libxml_use_internal_errors(true);
    $xmlDom = new DOMDocument();
    $xmlDom->loadXML($test['xml']);
    if (!$xmlDom) {
        require_once('inc/utils.php');
        redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/dashboard.php', 'Error. Invalid test contents.', 'alert-danger');
        exit();
    } elseif ($xmlDom->schemaValidate('inc/test_schema.xsd')) {
        // valid xml
        $xml = simplexml_import_dom($xmlDom);
    } else {
        require_once('inc/utils.php');
        redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/dashboard.php', 'Error. Invalid test contents.', 'alert-danger');
        exit();
    }

    $pageTitle = 'Test ' . htmlspecialchars($test['name']);
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
                    <form method="post" action="inc/submit_test.php">
                        <?php foreach ($xml as $question) { ?>
                            <div class="form-group">
                                <div class="jumbotron pt-2 pb-2 mb-2 mt-4 bg-light">
                                    <?php echo(strval($question['id']) . '.  <small>' . $question->description . '</small>'); ?>
                                </div>
                                <?php foreach ($question->answers->answer as $answer) {
                                    $checkboxId = 'question' . strval($question['id']) . '_answer' . $answer['id']; ?>
                                    <div class="ml-5 form-check">
                                        <input class="form-check-input" type="checkbox" name="<?php echo($checkboxId) ?>"
                                               id="<?php echo($checkboxId) ?>">
                                        <label class="form-check-label" for="<?php echo($checkboxId) ?>">
                                            <small><?php echo($answer) ?></small>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <div class="form-group row justify-content-center text-center">
                            <input type='hidden' name='test_id' value='<?php echo($test['test_id']); ?>'>
                            <input type='hidden' name='user_id' value='<?php echo($_SESSION['user_id']); ?>'>
                            <button class="btn btn-primary" type="submit"
                                    onsubmit="return confirm('Are you sure you want to submit your answers and leave?');">
                                Submit test
                            </button>
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




