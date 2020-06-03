<?php
    session_start();

    if (!empty($_SESSION["user_id"])) {
        # TODO add correct link
        header('Location: index.php');
        die();
    }

    $errors = null;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST['email'])) {
            $errors['email'] = 'The Email field cannot be empty.';
        }
        if (empty($_POST['password'])) {
            $errors['password'] = 'The Password field cannot be empty.';
        }
        if (empty($errors)) {
            require_once('inc/db.php');
            $userQuery = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
            $userQuery->execute([$_POST['email']]);
            $user = $userQuery->fetch(PDO::FETCH_ASSOC);

            if (@$user AND password_verify($_POST['password'], $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['email'] = $user['email'];

                # TODO add correct link
                header('Location: index.php');
                exit();
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
            <div class="col-md-4">
            </div>
        </div>
    </div>

<?php include('inc/footer.php'); ?>