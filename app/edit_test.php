<?php
    require_once('inc/examiner_logged_in_or_kick.php');

    $editMode = null;
    if (empty($_REQUEST['test_id'])) {
        $editMode = False;
        $pageTitle = 'Add new test';
    } else {
        // confirm ownership
        require_once('inc/examiner_owns_test.php');

        // owner can only edit his draft tests, not active ones
        if($test['activation_time']){
            require_once('inc/utils.php');
            redirectToPageWithPost('examiner_dashboard.php', 'You cannot edit an active test.', 'alert-danger');
            exit();
        } else {
            $editMode = True;
            $pageTitle = 'Edit test ' . htmlspecialchars($test['name']);
        }
    }

    $errors = null;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST['balloonMessage']) || empty($_POST['balloonType'])) {
            if (empty($_POST['name'])) {
                $errors['name'] = 'The Name field cannot be empty.';
            }
            if (empty($_POST['testcontents'])) {
                $errors['testcontents'] = 'The Test contents field cannot be empty.';
            }
            if (empty($errors)) {
                #region handle xml and actions
                libxml_use_internal_errors(true);
                $xml = new DOMDocument();
                $xml->loadXML($_POST['testcontents']);
                if ($xml->schemaValidate('inc/test_schema.xsd')) {
                    // XML well-formed and valid
                    if (@$_POST['previewAction']) {
                        // TODO make sure redirect link is ok after php file added
                        ?>
                        <form name='viewTest' action='view_test.php' method='POST'>
                            <input type='hidden' name='name' value='<?php echo($_POST['name']); ?>'>
                            <input type='hidden' name='xml' value='<?php echo($_POST['testcontents']); ?>'>
                        </form>
                        <script type='text/javascript'>
                            document.viewTest.submit();
                        </script>
                        <?php
                    } elseif (@$_POST['addAction']) {
                        require_once('inc/db.php');
                        $newTestQuery = $db->prepare("INSERT INTO `tests` (`user_id`, `name`, `xml`) VALUES (?, ?, ?)");
                        $newTestQuery->execute([$_SESSION['user_id'], $_POST['name'], $_POST['testcontents']]);

                        require_once('inc/utils.php');
                        redirectToPageWithPost('examiner_dashboard.php', 'Test successfully added.', 'alert-success');
                        exit();
                    } elseif (@$_POST['editAction']) {
                        require_once('inc/db.php');
                        require_once('inc/examiner_owns_test.php');

                        $editTestQuery = $db->prepare("UPDATE tests SET name=?, xml=? WHERE test_id=?");
                        $editTestQuery->execute([$_POST['name'], $_POST['testcontents'], $_GET['test_id']]);

                        require_once('inc/utils.php');
                        redirectToPageWithPost('examiner_dashboard.php', 'Test successfully changed.', 'alert-success');
                        exit();
                    }

                } else {
                    $errors['testcontents'] = 'The XML document provided does not match the mandatory schema. Please refer to the documentation.';
                }
                #endregion handle xml and actions
            }
        }
    }

    $name = @$_POST['name'];
    $testContents = @$_POST['testcontents'];

    if ($editMode) {
        // POST variables (potentially changed) have priority over values from DB
        if (empty($_POST['name'])){
            $name = $test['name'];
        }
        if (empty($_POST['testcontents'])){
            $testContents = $test['xml'];
        }
    }

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
                    <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            if (!empty($_POST['balloonMessage']) && !empty($_POST['balloonType'])) {
                                echo '<div class="alert ' . $_POST['balloonType'] . '">' . $_POST['balloonMessage'] . '</div>';
                            }
                        }
                    ?>
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <form method="post">
                            <label for="name">Name</label>
                            <br>
                            <input type="text" id="name" name="name"
                                   value="<?php echo(@$errors['name'] ? '' : htmlspecialchars(@$name)); ?>">
                            <?php echo(@$errors['name'] ? '<div><small class="text-danger">' . $errors['name'] . '</small></div>' : ''); ?>
                            <div class="mt-4">
                                <label for="testcontents">Test contents (XML)</label>
                                <p style="font-size: 65%;" class="text-secondary mb-1"><i>You can edit this in the
                                        external visual editor module <b>vEditor</b> (integration coming soon - in
                                        v2.0).</i>
                                </p>                                <textarea class="w-100" id="testcontents"
                                                                              name="testcontents"
                                                                              rows="6"><?php echo(@$testContents); ?></textarea>
                            </div>
                            <?php echo(@$errors['testcontents'] ? '<div><small class="text-danger">' . $errors['testcontents'] . '</small></div>' : ''); ?>
                            <div class="mt-2">
                                <button type="submit" name="previewAction" id="previewAction"
                                        value="1"
                                        class="btn mt-2 mb-2 btn-sm btn-outline-secondary mr-1">
                                    <small>Preview</small></button>
                                <button type="submit" name="editwysiwyg" id="editwysiwyg"
                                        value="1"
                                        class="btn mt-2 mb-2 btn-sm btn-outline-secondary" disabled>
                                    <small>Visual Edit (coming soon)</small></button>
                            </div>
                            <button type="submit" name="<?php echo($editMode ? 'editAction' : 'addAction'); ?>"
                                    id="<?php echo($editMode ? 'editAction' : 'addAction'); ?>"
                                    value="1"
                                    class="btn mt-4 btn-primary">
                                <small><?php echo($editMode ? 'Save changes' : 'Add test'); ?></small></button>

                        </form>
                    </div>
                    <div class="col-md-3"></div>
                </div>
            </div>
            <div class="col-md-1"></div>
        </div>
    </div>

<?php include('inc/footer.php'); ?>