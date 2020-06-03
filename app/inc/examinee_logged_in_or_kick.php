<?php

  require_once('user_logged_in_or_kick.php');

  if (!$currentUser['isexaminee']){
    header('HTTP/1.0 401 Unauthorized');
    die ("401: Unauthorized");
  }