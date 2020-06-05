<?php
    session_start();

    require_once('inc/db.php');

    if (!empty($_SESSION["user_id"])) {
        include('inc/redirect_user_to_correct_dashboard.php');
    }

    $errors = null;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // check if redirected and carrying a message
        if (empty($_POST['balloonMessage']) || empty($_POST['balloonType'])) {
            // check form's POST data
            if (empty($_POST['email'])) {
                $errors['email'] = 'The Email field cannot be empty.';
            } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Please provide a valid e-mail address.';
            }
            if (empty($_POST['password'])) {
                $errors['password'] = 'The Password field cannot be empty.';
            } elseif (strlen($_POST['password']) < 7) {
                $errors['password'] = 'Please choose a password with 8 or more characters.';
            } elseif ($_POST['password'] != @$_POST['passwordRepeat']) {
                $errors['passwordMatch'] = 'The two passwords did not match each other.';
            }
            if (empty($_POST['passwordRepeat'])) {
                $errors['passwordRepeat'] = 'The Repeat Password field cannot be empty.';
            }
            if (empty($errors)) {
                $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);

                // Check DB if record exists already
                $existingUserQuery = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
                $existingUserQuery->execute([$_POST['email']]);
                $user = $existingUserQuery->fetch(PDO::FETCH_ASSOC);

                if (!$user) {
                    // Insert a new user into the DB
                    require_once('inc/db.php');
                    $newUserQuery = $db->prepare("INSERT INTO `users` (`email`, `password`, `isexaminer`, `isexaminee`) VALUES (?, ?, False, True)");
                    $newUserQuery->execute([$_POST['email'], $passwordHash]);

                    // Log in the user
                    require_once('inc/utils.php');
                    sign_user_in($db, $_POST['email'], $_POST['password']);

                } else {
                    require_once('inc/utils.php');
                    redirectToPageWithPost($_SERVER['PHP_SELF'], 'Provided e-mail is already registered.', 'alert-danger');
                    exit();
                }
            }
        }
    }

    $pageTitle = 'Sign Up';
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
                            Sign Up
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
                            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <div class="form-group">
                                    <label for="email">
                                        Email address
                                    </label>
                                    <input type="email" class="form-control" name="email" id="email"
                                           value="<?php echo(@$errors['email'] ? '' : htmlspecialchars(@$_POST['email'])); ?>"
                                           required>
                                    <?php echo(@$errors['email'] ? '<small class="text-danger">' . $errors['email'] . '</small>' : ''); ?>
                                </div>
                                <div class="form-group">
                                    <label for="password">
                                        Password
                                    </label>
                                    <input type="password" class="form-control" name="password" id="password"
                                           value="<?php echo(@$errors['password'] ? '' : htmlspecialchars(@$_POST['password'])); ?>"
                                           required>
                                    <?php echo(@$errors['password'] ? '<small class="text-danger">' . $errors['password'] . '</small>' : ''); ?>
                                </div>
                                <div class="form-group">
                                    <label for="passwordRepeat">
                                        Repeat password
                                    </label>
                                    <input type="password" class="form-control" name="passwordRepeat"
                                           id="passwordRepeat"
                                           value="<?php echo(@$errors['passwordRepeat'] ? '' : htmlspecialchars(@$_POST['passwordRepeat'])); ?>"
                                           required>
                                    <?php echo(@$errors['passwordRepeat'] ? '<small class="text-danger">' . $errors['passwordRepeat'] . '</small>' : ''); ?>
                                </div>
                                <?php echo(@$errors['passwordMatch'] ? '<div><small class="text-danger">' . $errors['passwordMatch'] . '<br><br></small></div>' : ''); ?>
                                <button type="submit" class="btn btn-primary mt-2">
                                    Sign Up
                                </button>
                            </form>
                            <a href="signin.php" class="btn btn-outline-secondary btn-sm mt-3" >I already have an account</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
            </div>
        </div>
    </div>

<?php include('inc/footer.php'); ?>