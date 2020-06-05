<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo(empty(@$pageTitle) ? 'eTester' : 'eTester | ' . $pageTitle); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
          integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>
<body>
<header class="container-fluid bg-primary">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="container-fluid bg-primary">
                <div class="row">
                    <div class="d-flex w-100 flex-column flex-md-row align-items-center">
                        <h4 class="p-3 text-white font-weight-bolder">eTester</h4>
                        <?php
                            if (!empty(@$_SESSION['user_id'])) {
                                echo('<nav class="p-3 mb-1">');
                                if (@$_SESSION['isexaminee']) {
                                    echo('<a href = "dashboard.php" class="pr-4 text-white" >Dashboard</a>');
                                }
                                if (@$_SESSION['isexaminer']) {
                                    echo('<a href = "examiner_dashboard.php" class="pr-4 text-white" >Examiner Dashboard</a>');
                                }
                                echo('
                        </nav>
                        <a href="signout.php" class="mb-1 ml-auto text-white">Sign out</a>');
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>

</header>