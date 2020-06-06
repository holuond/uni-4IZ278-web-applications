<?php
    require_once('inc/user_logged_in_or_kick.php');

    $pageTitle = 'Help';
    include('inc/header.php');
?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
            </div>
            <div class="col-md-8">
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h1 class="text-center">
                            Help
                        </h1>
                    </div>
                </div>
                <?php if ($_SESSION['isexaminer']) { ?>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="mt-1 mb-4">
                                <h4>Examiner Tips</h4>
                            </div>
                            <div class="jumbotron pt-2 pb-2 mb-3">
                                <small><span>Editing active test's contents is not possible, make sure to do all necessary edits in the draft stage.</span></small>
                            </div>
                            <div class="jumbotron pt-2 pb-2 mb-3">
                                <small><span>Example valid test XML:</span><br><br>
                                    <span><pre><?php echo(htmlspecialchars('
<?xml version="1.0" encoding="UTF-8"?>
<test xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="https://eso.vse.cz/~holo00/etester/inc/test_schema.xsd">
	<question id="1">
		<description>Question 1</description>
		<answers>
			<answer correct="false">WRONG</answer>
			<answer correct="true">CORRECT</answer>
		</answers>
	</question>
	<question id="2">
		<description>Question 2</description>
		<answers>
			<answer correct="false">WRONG</answer>
			<answer correct="true">CORRECT</answer>
			<answer correct="false">WRONG2</answer>
		</answers>
	</question>
</test>')); ?></pre></span></small>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($_SESSION['isexaminee']) { ?>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="mt-1 mb-4">
                                <h4>Examinee Tips</h4>
                            </div>
                            <div class="jumbotron pt-2 pb-2 mb-3">
                                <small><span>Lorem ipsum</span></small>
                            </div>
                            <div class="jumbotron pt-2 pb-2 mb-3">
                                <small><span>dolor sit amet</span></small>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="col-md-2">
            </div>
        </div>
    </div>

<?php include('inc/footer.php'); ?>