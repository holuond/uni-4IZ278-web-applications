<?php
    session_start();

    if (!empty($_SESSION["user_id"])) {
        include('inc/redirect_user_to_correct_dashboard.php');
    }

    $errors = null;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST['balloonMessage']) || empty($_POST['balloonType'])) {
            if (empty($_POST['email'])) {
                $errors['email'] = 'The Email field cannot be empty.';
            }
            if (empty($_POST['password'])) {
                $errors['password'] = 'The Password field cannot be empty.';
            }
            if (empty($errors)) {
                //login
                require_once('inc/db.php');
                require_once('inc/utils.php');
                sign_user_in($db, $_POST['email'], $_POST['password']);

            } else {
                $errors['login'] = 'Incorrect username/password combination. Please try again.';
            }
        }
    }

    $pageTitle = 'Sign In';
    include('inc/header.php');
?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
            </div>
            <div class="col-md-4">
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h1 class="text-center">
                            Sign In
                        </h1>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <?php
                            if (!empty($_POST['balloonMessage']) && !empty($_POST['balloonType'])) {
                                echo '<div class="alert ' . $_POST['balloonType'] . '">' . $_POST['balloonMessage'] . '</div>';
                            }
                        ?>
                        <div class="jumbotron pt-5 pb-5">
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
                                <button type="submit" class="btn btn-primary mt-2">
                                    Sign In
                                </button>
                            </form>
                            <?php #TODO Forgotten password ?>
                            <button type="button" class="btn btn-outline-secondary btn-sm mt-3">
                                I've forgotten my password
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
            </div>
        </div>
    </div>

<?php include('inc/footer.php'); ?>