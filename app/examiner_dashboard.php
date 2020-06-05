<?php
    require_once('inc/examiner_logged_in_or_kick.php');

    $pageTitle = 'Examiner Dashboard';
    include('inc/header.php');
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
                            <div class="container-fluid mb-3">
                                <div class="row">
                                    <div class="col-md-5">
                                        <p class="mt-2 mb-2"><small>English verbs</small></p>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="d-flex w-100 flex-column flex-md-row align-items-center">
                                            <a class="btn btn-sm ml-auto btn-outline-secondary p-1 m-1 pr-2 pl-2"><small>Activate</small></a>
                                            <a class="btn btn-sm ml-auto btn-outline-secondary p-1 m-1 pr-2 pl-2"><small>Edit</small></a>
                                            <a class="btn btn-sm ml-auto btn-outline-secondary p-1 m-1 pr-2 pl-2"><small>Remove</small></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-center"><h5>Active tests</h5></div>
                        <div class="jumbotron pt-4 pb-3">
                            <div class="container-fluid mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mt-2 mb-2"><small>English verbs</small></p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex w-100 flex-column flex-md-row align-items-center">
                                            <a class="btn btn-sm ml-auto btn-outline-secondary p-1 m-1 pr-2 pl-2"><small>Properties</small></a>
                                            <a class="btn btn-sm ml-auto btn-outline-secondary p-1 m-1 pr-2 pl-2"><small>Remove</small></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1"></div>
        </div>
    </div>

<?php include('inc/footer.php'); ?>