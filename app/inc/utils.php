<?php
    function redirectToPageWithPost($pageRelPath, $balloonMessage, $balloonType)
    {
        ?>
        <form name='del' action='<?php echo($pageRelPath) ?>' method='POST'>
            <input type='hidden' name='balloonMessage' value='<?php echo($balloonMessage) ?>'>
            <input type='hidden' name='balloonType' value='<?php echo($balloonType) ?>'>
        </form>
        <script type='text/javascript'>
            document.del.submit();
        </script>
        <?php
    }

    function sign_user_in($db, $email, $password)
    {
        $userQuery = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $userQuery->execute([$email]);
        $user = $userQuery->fetch(PDO::FETCH_ASSOC);

        if (@$user AND password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['isexaminer'] = $user['isexaminer'];
            $_SESSION['isexaminee'] = $user['isexaminee'];

            if ($user['isexaminer']) {
                header('Location: examiner_dashboard.php');
                exit();
            } elseif ($user['isexaminee']) {
                header('Location: dashboard.php');
                exit();
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit("401: Unauthorized");
            }
        }
    }

?>

