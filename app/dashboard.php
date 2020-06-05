<?php
    require_once('inc/examinee_logged_in_or_kick.php');

    $pageTitle = 'Dashboard';
    include('inc/header.php');
?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-1">
            </div>
            <div class="col-md-10">
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h1 class="text-center">
                            Dashboard
                        </h1>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <?php
                            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                if (!empty($_POST['balloonMessage']) && !empty($_POST['balloonType'])) {
                                    echo '<div class="alert ' . $_POST['balloonType'] . '">' . $_POST['balloonMessage'] . '</div>';
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-1">
            </div>
        </div>
    </div>

<?php include('inc/footer.php'); ?>