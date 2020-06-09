<?php

    require_once('examinee_logged_in_or_kick.php');
    require_once('get_test_from_db.php');

    $testResultsQuery = $db->prepare("SELECT user_id, email, score, answersheet, submission_time FROM test_results JOIN users USING (user_id) WHERE test_id = ? AND user_id = ? LIMIT 1");
    $testResultsQuery->execute([$_REQUEST['test_id'], $_REQUEST['user_id']]);
    $testResult = $testResultsQuery->fetch();

    if ($testResult && $_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($testResult['submission_time'])) {
            require_once('utils.php');
            redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/dashboard.php', 'You have already taken this test!.', 'alert-danger');
            exit();
        }
    } else {
        require_once('utils.php');
        redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/dashboard.php', 'Unauthorized.', 'alert-danger');
        exit();
    }

    #region check xml validity
    //libxml_use_internal_errors(true);
    $xmlDom = new DOMDocument();
    $xmlDom->loadXML($test['xml']);
    if (!$xmlDom) {
        require_once('utils.php');
        redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/dashboard.php', 'Error. Invalid test contents.', 'alert-danger');
        exit();
    } elseif ($xmlDom->schemaValidate('test_schema.xsd')) {
        // valid xml
        $xml = simplexml_import_dom($xmlDom);
    } else {
        require_once('utils.php');
        redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/dashboard.php', 'Error. Invalid test contents.', 'alert-danger');
        exit();
    }
    #endregion check xml validity

    #region transform incoming post variables to an answersheet
    $matches = [];
    foreach ($_POST as $paramName => $paramValue) {
        $match = [];
        preg_match('/^question\d{1,10}_answer\d{1,10}$/', $paramName, $match);
        if (!empty($match)) {
            array_push($matches, $match[0]);
        }
    }
    $answersheet = implode(',', $matches);
    #endregion transform incoming post variables to an answersheet

    $questionCounter = 0;
    $questionScoreSum = 0;
    #region calculate score
    foreach ($xml as $question) {
        $questionAnswerCount = sizeof($question->answers->children());
        $questionCounter = ++$questionCounter;
        $correctAnswersCounter = 0;
        foreach ($question->answers->answer as $answer) {
            $answerId = 'question' . strval($question['id']) . '_answer' . $answer['id'];
            if (in_array($answerId, $matches)) {
                if ($answer['correct'] == 'true') {
                    //ticked answer and correct answer (yay)
                    $correctAnswersCounter = ++$correctAnswersCounter;
                }
            } else {
                if ($answer['correct'] != 'true') {
                    //unticked answer and correct answer (yay)
                    $correctAnswersCounter = ++$correctAnswersCounter;
                }
            }
        }
        $questionScore = $correctAnswersCounter / $questionAnswerCount;
        $questionScoreSum = $questionScoreSum + $questionScore;
    }
    $totalScore = $questionScoreSum / $questionCounter;
    #endregion calculate score

    $saveTestResultQuery = $db->prepare("UPDATE `test_results` SET `answersheet`=?, `score`=?, `submission_time`=? WHERE test_id=? AND user_id=?");
    if ($saveTestResultQuery->execute([$answersheet, $totalScore, time(), $test['test_id'], $_SESSION['user_id']])) {
        require_once('utils.php');
        redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/dashboard.php', 'Test successfully submitted.', 'alert-success');
    } else {
        require_once('utils.php');
        redirectToPageWithPost('https://eso.vse.cz/~holo00/etester/dashboard.php', 'Fatal error during test submission.', 'alert-danger');
    }