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
<header class="container bg-primary">
    <h1 class="text-white">eTester</h1>
    <?php
        if (!empty(@$_SESSION['email'])) {
            echo('<p class="text-right"><small>' . htmlspecialchars($_SESSION['email']) . '<br><a href="signout.php">Sign out</a></small></p>');
        } else {
            echo('<p><small>&nbsp;<br></small></p>');
        }
    ?>
</header>