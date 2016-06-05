<?php
session_start();
require_once('store.php');
ini_set('display_errors', 'On');
// update db by checking out book @ get['id'] to user @ get['pid']
if (isset($_GET['id']) && isset($_GET['pid'])) {
  $item_id=$_GET['id'];
  $patron_id=$_GET['pid'];

  if ($patron_id >= 1000) {

    //open connection
    $mysqli = new mysqli($db_host, $db_un, $db_pw, $db_db);
    if ($mysqli->connect_errno) {
      echo "Failed to connect to MySQL: (".$mysqli->connect_errno.
        ") ".$mysqli->connect_error;
    }
    $prep="UPDATE item SET plocation=?, ";
    $prep.="blocation=NULL, available=FALSE, ";
    $prep.="due_date=(SELECT ADDDATE(CURDATE(), INTERVAL 21 DAY)) ";
    $prep.="WHERE item_id=?";

    //prepare statement
    if (!($stmt=$mysqli->prepare($prep))) {
      echo "Prepare failed: (".$mysqli->errno.") ".$mysqli->error;
    }
    //bind
    if (!($stmt->bind_param("ss", $patron_id, $item_id))) {
      echo "Binding parameters failed: (".$mysqli->errno.") ".$stmt->error;
    }
    //execute
    if (!($stmt->execute())) {
      echo "Execute failed: (".$stmt->errno.") ".$stmt->error;
    }
  } else if ($patron_id < 1000) {
    //open connection
    $mysqli = new mysqli($db_host, $db_un, $db_pw, $db_db);
    if ($mysqli->connect_errno) {
      echo "Failed to connect to MySQL: (".$mysqli->connect_errno.
        ") ".$mysqli->connect_error;
    }
    $prep="UPDATE item SET plocation=NULL, ";
    $prep.="blocation=?, available=TRUE, ";
    $prep.="due_date=NULL ";
    $prep.="WHERE item_id=?";

    //prepare statement
    if (!($stmt=$mysqli->prepare($prep))) {
      echo "Prepare failed: (".$mysqli->errno.") ".$mysqli->error;
    }
    //bind
    if (!($stmt->bind_param("ss", $patron_id, $item_id))) {
      echo "Binding parameters failed: (".$mysqli->errno.") ".$stmt->error;
    }
    //execute
    if (!($stmt->execute())) {
      echo "Execute failed: (".$stmt->errno.") ".$stmt->error;
    } 
  }
  else {
    echo "Nothing to update";
  }
}

require_once('checkout.php');
?>
