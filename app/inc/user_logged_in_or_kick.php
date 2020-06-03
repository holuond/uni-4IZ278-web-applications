<?php
  session_start();

  if(!isset($_SESSION["user_id"])){
    header('Location: signin.php');
    die();
  }

  require_once('db.php');
  $currentUserQuery = $db->prepare("SELECT * FROM users WHERE user_id = ? LIMIT 1");
  $currentUserQuery->execute([$_SESSION["user_id"]]);

  $currentUser = $currentUserQuery->fetch(PDO::FETCH_ASSOC);

  if (!$currentUser){
    session_destroy();
    header('Location: signin.php');
    die();
  }