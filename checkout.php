<?php

if (!($_SESSION)) {
  session_start();
}
ini_set('display_errors', 'On');
require_once('store.php');

if (isset($_SESSION['admin']) && ($_SESSION['admin'])) {
  echo "<fieldset id='checkedout'>";
  echo "<div id='patron-checkout'>";
  echo "Select Patron ID to See Checked Out Items: ";
  echo "<input type='number' min='1000' max='9999' id='patron-id' />";

  echo "<input type='button' value='Enter' onclick='post_content_not_response(\"checkout.php\")'/>";
  if ((isset($_POST['patron-id'])) && ($_POST['patron-id'] >= 1000)) {
    $patron_check=0;
    $mysqli = new mysqli($db_host, $db_un, $db_pw, $db_db);
    if ($mysqli->connect_errno) {
      echo "Failed to connect to MySQL: (".$mysqli->connect_errno.
        ") ".$mysqli->connect_error;
    }
    $prepare = "SELECT patron_id FROM patron WHERE patron_id=(?)";
    //prepare statement
    if (!($stmt=$mysqli->prepare($prepare))) {
      echo "Prepare failed: (".$mysqli->errno.") ".$mysqli->error;
    }
    //bind param
    if(!($stmt->bind_param("s", $_POST['patron-id']))) {
      echo "Bind params failed: (".$mysqli->errno.") ".$mysqli->error;
    }
    //execute
    if (!($stmt->execute())) {
      echo "Execute failed: (".$stmt->errno.") ".$stmt->error;
    }
    //bind result
    $stmt->bind_result($patron_check);
    //fetch value
    $stmt->fetch();
    if ($patron_check) {
      $_SESSION['patron-check'] = $_POST['patron-id'];
    } else {
      echo " | Invalid Patron ID | ";
    }
    //close statement
    $stmt->close();
    //close connection
  }

  if (isset($_SESSION['patron-check'])) {
    echo " | Currently selected = ".$_SESSION['patron-check'];
  } else if (isset($_SESSION['pid'])) {
    echo " | Currently selected = ".$_SESSION['pid'];
  }
  echo "</div>";
  echo "</fieldset>";
}

$patron_to_check=1001;

if (isset($_SESSION['admin']) && ($_SESSION['admin']) && (isset($_SESSION['patron-check'])) && ($_SESSION['patron-check'] > 999)) {
  $patron_to_check = $_SESSION['patron-check'];
} else {
  $patron_to_check = $_SESSION['pid'];
}

if ($patron_to_check) {
  if (isset($_SESSION['pid']) && ($_SESSION['pid'] == $patron_to_check)) {
    echo "<p>Items checked out to ".$_SESSION['fname']." ".$_SESSION['lname'].", Patron ID: ".$_SESSION['pid']."</p>";
  } else {
    echo "<p>Items checked out to Patron ID: ".$patron_to_check."</p>";
  }
}


// query database to show books checked out to this patron
$mysqli = new mysqli($db_host, $db_un, $db_pw, $db_db);
if ($mysqli->connect_errno) {
  echo "Failed to connect to MySQL: (".$mysqli->connect_errno.
    ") ".$mysqli->connect_error;
}

//prepare statement
$prepare = "SELECT i.item_id, b.title, count(*), a.lname, a.fname, i.blocation, i.plocation, i.due_date ";
$prepare .= " FROM item i ";
$prepare .= "INNER JOIN book b ON i.isbn=b.isbn ";
$prepare .= "INNER JOIN book_author ba ON b.isbn=ba.isbn ";
$prepare .= "INNER JOIN author a ON ba.author_id=a.author_id ";
//$prepare .= "INNER JOIN branch l ON i.location=l.branch_id ";
$prepare .= "WHERE i.plocation=? ";
$prepare .= "GROUP BY i.item_id";
//prepare
if (!($stmt=$mysqli->prepare($prepare))) {
  echo "Prepare failed: (".$mysqli->errno.") ".$mysqli->error;
}

//bind param
if (!($stmt->bind_param("s", $patron_to_check))) {
  echo "Bind params failed: (".$mysqli->errno.") ".$stmt->error;
}

//execute
if (!($stmt->execute())) {
  echo "Execute failed: (".$stmt->errno.") ".$stmt->error;
}
//bind result variables
$stmt->bind_result($id, $title, $count, $a_lname, $a_fname, $bloc, $ploc, $due);
// display books in collection

if (!($stmt->fetch())) {
  echo "No books checked out";
} else {
  echo "<table id='books'>\n";
  echo "<thead>\n";
  echo "<th>id<th>title<th>#auths<th>a_last<th>a_first<th>location<th>date due<th>action";
  echo "</thead><tbody>";
  do { // fetch values
    echo "<tr><td>".$id;
    echo "<td>".$title;
    echo "<td>".$count;
    echo "<td>".$a_lname;
    echo "<td>".$a_fname;
    echo "<td>".$ploc;

    if ($due) {
      echo "<td>".$due;
    } else {
      echo "<td>Available";
    }
    // AND a button to check item out or return it if it's already checked out
    echo "<td>";
    if (!($ploc)) {
      echo "<input type='button' value='checkout' id='check_" .$id. "' onclick='load_content(\"update_checkout.php?id=" .$id. "&pid=" .$_SESSION['pid']. "\")' />";
    } else if (!($bloc)) {
      echo "<input type='button' value='return' id='check_" .$id. "' onclick='load_content(\"update_checkout.php?id=" .$id. "&pid=1\") '/>";
    }
  } while ($stmt->fetch());

}
echo "</tbody></table>\n";
//close statement
$stmt->close();
//close connection
$mysqli->close();
?>
