<?php
    require_once('db.php');
    $testQuery = $db->prepare("SELECT * FROM tests WHERE test_id = ? LIMIT 1");
    $testQuery->execute([$_REQUEST['test_id']]);
    $test = $testQuery->fetch(PDO::FETCH_ASSOC);