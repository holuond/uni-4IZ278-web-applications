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
                        <div class="jumbotron">
                            <form role="form" method="post">
                                <div class="form-group">
                                    <label for="email">
                                        Email address
                                    </label>
                                    <input type="email" class="form-control" name="email" id="email"
                                           value="<?php echo(@$errors['email'] ? '' : htmlspecialchars(@$_POST['email'])); ?>"
                                           required/>
                                    <?php echo(@$errors['email'] ? '<small class="text-danger">' . $errors['email'] . '</small>' : ''); ?>
                                </div>
                                <div class="form-group">
                                    <label for="password">
                                        Password
                                    </label>
                                    <input type="password" class="form-control" name="password" id="password"
                                           value="<?php echo(@$errors['password'] ? '' : htmlspecialchars(@$_POST['password'])); ?>"
                                           required/>
                                    <?php echo(@$errors['password'] ? '<small class="text-danger">' . $errors['password'] . '</small>' : ''); ?>
                                </div>
                                <?php echo(@$errors['login'] ? '<div><small class="text-danger">' . $errors['login'] . '</small><br><br></div>' : ''); ?>
                                <button type="submit" class="btn btn-primary">
                                    Sign In
                                </button>
                            </form>
                            <br>
                            <?php #TODO Forgotten password ?>
                            <button type="button" class="btn btn-outline-secondary btn-sm">
                                I've forgotten my password
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1">
            </div>
        </div>
    </div>

<?php include('inc/footer.php'); ?>