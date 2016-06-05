<?php
if (!(isset($_SESSION))) { 
  session_start(); 
}
ini_set('display_errors', 'On');
require_once('store.php');

?>

<fieldset id='branch-filter'>
  Filter by: <select id='filter' onchange='post_content_not_response("browse.php")'>
    <option value='0'>--</option>
    <option value='0'>All</option>

<?php
$mysqli = new mysqli($db_host, $db_un, $db_pw, $db_db);
if ($mysqli->connect_errno) {
  echo "Failed to connect to MySQL: (".$mysqli->connect_errno.
    ") ".$mysqli->connect_error;
}
$prepare = "SELECT branch_id, name FROM branch";
//prepare statement
if (!($stmt=$mysqli->prepare($prepare))) {
  echo "Prepare failed: (".$mysqli->errno.") ".$mysqli->error;
}
//execute
if (!($stmt->execute())) {
  echo "Execute failed: (".$stmt->errno.") ".$stmt->error;
}
//bind result
$stmt->bind_result($branch_id, $branch_name);
//fetch value
while ($stmt->fetch()) {
  echo "<option value='".$branch_id."'>".$branch_name."</option>";
}
//close statement
$stmt->close();
//close connection
$mysqli->close();
?>

  </select>

<?php
// code for selecting patron to check-out to should go here
if (isset($_SESSION['admin']) && ($_SESSION['admin'])) {
  echo " | ";
  echo "<div id='patron-select'>";
  echo "Select Patron ID to Check Books Out To: ";

  // NOTE NEED to change this to a drop-down for selecting patrons
  echo "<input type='number' min='1000' max='9999' id='patron-id' />";

  echo "<input type='button' value='Enter' onclick='post_content_not_response(\"browse.php\")'/>";

  // NOTE NEED TO QUERY DB HERE TO MAKE SURE PATRON ID IS VALID
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
}
?>

</fieldset>

<?php
$branch_filter=0;
if (isset($_POST['filter'])) {
  $branch_filter = $_POST['filter'];
}
$mysqli = new mysqli($db_host, $db_un, $db_pw, $db_db);
if ($mysqli->connect_errno) {
  echo "Failed to connect to MySQL: (".$mysqli->connect_errno.
    ") ".$mysqli->connect_error;
}
//prepare statement
$prepare = "SELECT i.item_id, b.title, count(*), a.lname, a.fname, i.blocation, i.plocation, i.due_date, br.name ";
$prepare .= " FROM item i ";
$prepare .= "INNER JOIN book b ON i.isbn=b.isbn ";
$prepare .= "INNER JOIN book_author ba ON b.isbn=ba.isbn ";
$prepare .= "INNER JOIN author a ON ba.author_id=a.author_id ";
$prepare .= "LEFT JOIN branch br ON br.branch_id=i.blocation ";
$prepare .= "GROUP BY i.item_id";
//prepare
if (!($stmt=$mysqli->prepare($prepare))) {
  echo "Prepare failed: (".$mysqli->errno.") ".$mysqli->error;
}
//execute
if (!($stmt->execute())) {
  echo "Execute failed: (".$stmt->errno.") ".$stmt->error;
}
//bind result variables
$stmt->bind_result($id, $title, $count, $a_lname, $a_fname, $bloc, $ploc, $due, $br_name);
// display books in collection
echo "<table id='books'>\n";
echo "<thead>";
echo "<th>id<th>title<th>#auths<th>a_last<th>a_first<th>branch<th>patron<th>status";

// PEOPLE NOT LOGGED in can browse collection, but cannot perform actions on it
// so only logged-in users get ACTION row
if (isset($_SESSION['loggedin']) && ($_SESSION['loggedin'])) {
  echo "<th>action";
}
echo "\n";

echo "</thead><tbody>";
while ($stmt->fetch()) {  //fetch value(s)
  // if branch-filter is zero, print every row
  // if branch-filter is not zero, only print when branch-filter=bloc
  $print_row = true;
  if ($branch_filter) {
    if ($bloc != $branch_filter) {
      $print_row = false;
    }
  }
  if ($print_row) {
    echo "<tr><td>".$id;
    echo "<td>".$title;
    echo "<td>".$count;
    echo "<td>".$a_lname;
    echo "<td>".$a_fname;
    if ($bloc) {
      echo "<td>".$br_name;
    } else {
      echo "<td>---";
    }

    if ((isset($_SESSION['admin'])) && ($_SESSION['admin'])) {
      if ($ploc) {
        echo "<td>".$ploc;
      } else {
        echo "<td>---";
      }
    } else {
      if ($ploc) {
        if (isset($_SESSION['pid']) && ($ploc == $_SESSION['pid'])) {
          echo "<td>Out to You";
        } else {
          echo "<td>Out";
        } 
      }
      else {
        echo "<td>---";
      }
    }
    if ($due) {
      echo "<td>".$due;
    } else {
      echo "<td>Available";
    }
    // AND buttons to check out/return items if already checked out
    if (isset($_SESSION['pid']) && ($_SESSION['pid'])) {
      echo "<td>";

      if (!($ploc)) {

        if (isset($_SESSION['patron-check'])) {
          echo "<input type='button' value='checkout' id='check_" .$id. "' onclick='load_content(\"update_stat.php?id=" .$id. "&pid=" .$_SESSION['patron-check']. "\")' />";
        } else {
          echo "<input type='button' value='checkout' id='check_" .$id. "' onclick='load_content(\"update_stat.php?id=" .$id. "&pid=" .$_SESSION['pid']. "\")' />";
        }

        //echo "<input type='button' value='checkout' id='check_" .$id. "' onclick='load_content(\"update_stat.php?id=" .$id. "&pid=" .$_SESSION['pid']. "\")' />";
      } else if (!($bloc)) {

        if (isset($_SESSION['admin']) && ($_SESSION['admin'])) {
          echo "<input type='button' value='return' id='check_" .$id. "' onclick='load_content(\"update_stat.php?id=" .$id. "&pid=1\") '/>";
        } else if ($ploc == $_SESSION['pid']) {
          echo "<input type='button' value='return' id='check_" .$id. "' onclick='load_content(\"update_stat.php?id=" .$id. "&pid=1\") '/>";
        }
      }
    }
  }
}
echo "</tbody></table>";
//close statement
$stmt->close();
//close connection
$mysqli->close();

?>
