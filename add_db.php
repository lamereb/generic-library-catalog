<?php
require_once 'store.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$n = '<br>';
$insert_user=true;

// FIRST, I NEED TO QUERY db to see if username is already taken

//check post array
if (isset($_POST['fname']) && ($_POST['fname'] != "")) {
  $fname=strip_tags($_POST['fname']);
} else {
  $fname="Not_given";
  $insert_user=false;
}
if (isset($_POST['lname']) && ($_POST['lname'] != "")) {
  $lname=$_POST['lname'];
} else {
  $lname="Not_given";
  $insert_user=false;
}
if (isset($_POST['admin']) && ($_POST['admin'] == 'true')) {
  $admin=1;
} else {
  $admin=0;
}
if (isset($_POST['uname']) && ($_POST['uname'] != "")) {
  $uname=$_POST['uname'];
} else {
  $uname="Not_given";
  $insert_user=false;
}
if (isset($_POST['pw']) && ($_POST['pw'] != "")) {
  $pw=$_POST['pw'];
} else {
  $pw="Not_given";
  $insert_user=false;
}

// check to see if uname already in patron table, and if so, set insert_user = false
$mysqli = new mysqli($db_host, $db_un, $db_pw, $db_db);
if ($mysqli->connect_errno) {
  echo "Failed to connect to MySQL: (".$mysqli->connect_errno.
    ") ".$mysqli->connect_error;
}
//prepare statement
if (!($stmt=$mysqli->prepare("SELECT patron_id FROM patron WHERE uname=?"))) {
  echo "Prepare failed: (".$mysqli->errno.") ".$mysqli->error;
}
//bind
if (!($stmt->bind_param("s", $uname))) {
  echo "Binding parameters failed: (".$mysqli->errno.") ".$stmt->error;
}
//execute
if (!($stmt->execute())) {
  echo "Execute failed: (".$stmt->errno.") ".$stmt->error;
}
//bind result
$stmt->bind_result($patron_id);
//fetch value
$stmt->fetch();
//close statement
$stmt->close();
//close connection
$mysqli->close();

if ($patron_id) {  // username is in table
  $insert_user = false;
  echo "That username already exists.";
}

if ($insert_user) {
  //open connection
  $mysqli = new mysqli($db_host, $db_un, $db_pw, $db_db);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (".$mysqli->connect_errno.
      ") ".$mysqli->connect_error;
  }
  //prepare statement
  if (!($stmt=$mysqli->prepare("INSERT INTO patron(fname, lname, exp_date, admin, uname, pw) VALUES(?,?,(SELECT ADDDATE(CURDATE(),INTERVAL 365 DAY)),?,?,?)"))) {
    echo "Prepare failed: (".$mysqli->errno.") ".$mysqli->error;
  }
  //bind
  if (!($stmt->bind_param("sssss", $fname, $lname, $admin, $uname, $pw))) {
    echo "Binding parameters failed: (".$mysqli->errno.") ".$stmt->error;
  }
  //execute
  if (!($stmt->execute())) {
    echo "Execute failed: (".$stmt->errno.") ".$stmt->error;
  }
  //test code

  echo "User <b>".$uname."</b> successfully added for Patron <b>".$fname." ".$lname."</b>. ";
  echo "<br>";

} else {
  echo "Not enough info given. No data recorded.";
}
//echo "Click <a href='index.php'>here</a> to return home.";
?>
