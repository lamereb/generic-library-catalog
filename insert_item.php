<?php
if (!(isset($_SESSION))) {
  session_start();
}
ini_set('display_errors', 'On');
require_once('store.php');

if (isset($_SESSION['loggedin']) && ($_SESSION['loggedin'] == true))
?>

<fieldset id='postbooks'>
  <legend>Add Book to Collection</legend>
  <label>ISBN:</label> <input type='text' id='isbn' /><br>
  <label>Title:</label> <input type='text' id='title' /><br>
  <label>Author Last Name:</label> <input type='text' id='lname' /><br>
  <label>Author First Name:</label> <input type='text' id='fname' /><br>
  <label>Publication Year:</label> <input type='number' min='1' max='2016' id='year' /><br>
  <label>Number of copies:</label> <input type='number' value='1' min='1' max='20' id='numcopies' /><br>
  <label>Branch To Add To:</label> <select id='branch'>

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
  <br>
  <input type='submit' value='Add' onclick='post_content("insert_db.php")'/>
</fieldset>

<fieldset>
  <legend>Or, Query WorldCat for Data via ISBN</legend>
  <label>ISBN:</label> <input type='text' id='querynum' /><br>
  <label>Number of copies:</label> <input type='number' value='1' min='1' max='20' id='numcopies2' /><br>
  <label>Branch To Add To:</label> <select id='branch2'>
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
  <br>
  <input type='submit' value='Lookup' onclick='post_content("insert_db.php")' />
</fieldset>

<?php
?>

