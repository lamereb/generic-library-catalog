<?php
require_once('store.php');

$mysqli=new mysqli($db_host, $db_un, $db_pw, $db_db);
if ($mysqli->connect_errno) {
  echo "Failed to connect to MySQL: (".$mysqli->connect_errno.
    ") ".$mysqli->connect_error;
}
$prepare="SELECT patron_id, fname, lname, exp_date, admin, uname FROM patron";
//prepare
if (!($stmt=$mysqli->prepare($prepare))) {
  echo "Prepare failed: (".$mysqli->errno.") ".$mysqli->error;
}
//execute
if (!($stmt->execute())) {
  echo "Execute failed: (".$stmt->errno.") ".$stmt->error;
}
//bind result variables
$stmt->bind_result($id, $fname, $lname, $exp, $admin, $uname);
// display books in collection
echo "<table id='books'>\n";
echo "<thead>\n";
echo "<th>id<th>first<th>last<th>username<th>status<th>exp_date<th>action\n";
echo "</thead><tbody>\n";
while ($stmt->fetch()) {  //fetch value(s)
  echo "<tr><td>".$id;
  echo "<td>".$fname;
  echo "<td>".$lname;
  echo "<td>".$uname;
  if ($admin) { 
    echo "<td>Administrator";
  } else {
    echo "<td>Patron";
  }
  echo "<td>".$exp;

  echo "<td>";

  // add a DELETE button here, but not for first 4 accounts
  if ($id > 1003) {
    echo "<input type='button' value='Delete' id='delete_".$id."' onclick='load_content(\"update_patron.php?id=".$id."\")' />";
  }
}
echo "</tbody><table>\n";
//close statement
$stmt->close();
//close connection
$mysqli->close();

?>
