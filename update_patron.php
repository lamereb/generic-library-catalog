<?php
require_once('store.php');
ini_set('display_errors', 'On');
if(!(isset($_SESSION))) {
  session_start();
}

if (isset($_SESSION['admin']) && ($_SESSION['admin'])) {

  if (isset($_GET['id']) && ($_GET['id'] > 999)) {
    $prepare="DELETE FROM patron WHERE patron_id=(?)";

    $mysqli=new mysqli($db_host, $db_un, $db_pw, $db_db);
    if ($mysqli->connect_errno) {
      echo "Failed to connect to MySQL: (".$mysqli->connect_errno.
        ") ".$mysqli->connect_error;
    }
    //prepare
    if (!($stmt=$mysqli->prepare($prepare))) {
      echo "Prepare failed: (".$mysqli->errno.") ".$mysqli->error;
    }
    //bind param
    if (!($stmt->bind_param("s", $_GET['id']))) {
      echo "Bind params failed: (".$stmt->errno.") ".$stmt->error;
    }
    //execute
    if (!($stmt->execute())) {
      echo "Execute failed: (".$stmt->errno.") ".$stmt->error;
    }
    //bind result variables
    // display books in collection
    //close statement
    $stmt->close();
    //close connection
    $mysqli->close();

    require_once('show_patrons.php');
  } else {
    echo "Invalid Patron ID.";
  }
}

?>
