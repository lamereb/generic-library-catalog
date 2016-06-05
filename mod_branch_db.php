<?php
require_once('store.php');
ini_set('display_errors', 'On');
session_start();
$insert_branch=true;

if (isset($_SESSION['admin']) && ($_SESSION['admin'] == true)) {

  if (isset($_POST['name']) && ($_POST['name'] != "")) {
    $b_name=$_POST['name'];
  } else $insert_branch = false;
  if (isset($_POST['city']) && ($_POST['city'] != "")) {
    $b_city=$_POST['city'];
  } else $insert_branch = false;
  if (isset($_POST['zip']) && ($_POST['zip'] != "")) {
    $zip=$_POST['zip'];
  } else $insert_branch = false;

  // maybe query db to see if this branch already exists first?

  if ($insert_branch) {
    $mysqli = new mysqli($db_host, $db_un, $db_pw, $db_db);
    if ($mysqli->connect_errno) {
      echo "Failed to connect to MySQL: (".$mysqli->connect_errno.
        ") ".$mysqli->connect_error;
    }
    //prepare statement
    if (!($stmt=$mysqli->prepare("INSERT INTO branch (name, city, zip) VALUES(?,?,?)"))) {
      echo "Prepare failed: (".$mysqli->errno.") ".$mysqli->error;
    }
    //bind
    if (!($stmt->bind_param("sss", $b_name, $b_city, $zip))) {
      echo "Binding parameters failed: (".$mysqli->errno.") ".$stmt->error;
    }
    //execute
    if (!($stmt->execute())) {
      echo "Execute failed: (".$stmt->errno.") ".$stmt->error;
    }
    //close statement
    $stmt->close();
    //close connection
    $mysqli->close();

    echo "Successfully added ".$b_name." in the city of ".$b_city." and zipcode ".$zip." to Library System";
  } else {
    echo "Not enough info given";
  }

} else {
  echo "You do not have the privilege level to do this";
}

?>
