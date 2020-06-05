<?php
  require('db_details.php');
  $db = new PDO($dsn, $user, $passwd);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);