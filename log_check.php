<?php

require_once('store.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (isset($_POST['user']) && ($_POST['user'] != "")) {
  $user=$_POST['user'];
}
if (isset($_POST['pass']) && ($_POST['pass'] != "")) {
  $pass=$_POST['pass'];
}

//query database for user & pass from patron table
$mysqli = new mysqli($db_host, $db_un, $db_pw, $db_db);
if ($mysqli->connect_errno) {
  echo "Failed to connect to MySQL: (".$mysqli->connect_errno.
    ") ".$mysqli->connect_error;
}
//prepare statement
if (!($stmt=$mysqli->prepare("SELECT patron_id, fname, lname, admin FROM patron WHERE uname=? AND pw=?"))) {
  echo "Prepare failed: (".$mysqli->errno.") ".$mysqli->error;
}
//bind
if (!($stmt->bind_param("ss", $user, $pass))) {
  echo "Binding parameters failed: (".$mysqli->errno.") ".$stmt->error;
}
//execute
if (!($stmt->execute())) {
  echo "Execute failed: (".$stmt->errno.") ".$stmt->error;
}
//bind result
$stmt->bind_result($valid_user, $fname, $lname, $admin);
//fetch value
$stmt->fetch();
//close statement
$stmt->close();
//close connection
$mysqli->close();

if ($valid_user >= 1000) {
  $_SESSION['pid'] = $valid_user;
  $_SESSION['username'] = $user;
  $_SESSION['fname'] = $fname;
  $_SESSION['lname'] = $lname;
  $_SESSION['admin'] = $admin;
  $_SESSION['loggedin'] = true;

  echo 'Welcome, '.$_SESSION['fname'].'. Please use bar above to navigate.';
  //redirect to index.php
  /*
  $filepath = explode('/', $_SERVER['PHP_SELF'], -1);
  $filepath = implode('/', $filepath);
  $redirect = "http://".$_SERVER['HTTP_HOST'].$filepath;
  header("Location: {$redirect}/index.php", true);
   */

} else {
  echo "Login failed.<br>";
  //echo "Click <a href='index.php'>here</a> to return to home.";
}
?>
