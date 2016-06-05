<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// http://stackoverflow.com/questions/6249707/check-if-php-session-has-already-started
// Thanks to link above for finding this info updating bar when logging in
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

/*
//if (isset($_SESSION['username'])) {
if (isset($_SESSION['loggedin']) && ($_SESSION['loggedin'] == true)) {
  // $fname=$_SESSION['fname'];
  // $uname=$_SESSION['username'];
  // $admin=$_SESSION['admin'];
} else {
  // $fname = false;
  // $admin = false;
  // $_SESSION['loggedin'] = false;
}
?>

<?php
 */

echo "<div id='home'><a href='index.php'>Home</a></div>\n";
if ((isset($_SESSION['loggedin'])) && ($_SESSION['loggedin'] == true)) {
echo "Welcome, ".$_SESSION['fname'].". | ";
} else {
  echo "Welcome. Please <a href='#' onclick='load_content(\"login.php\")'>login</a> or ";
  // echo "<a href='add_acct.php'>create new account</a> | ";
  echo "<a href='#' onclick='load_content(\"add_acct.php\")'>create new account</a> | ";
}
echo "<a href='#' onclick='load_content(\"browse.php\")'>Browse</a> | ";
if ((isset($_SESSION['loggedin'])) && ($_SESSION['loggedin'])) {
  echo "<a href='#' onclick='load_content(\"checkout.php\")'>Checked-Out</a> | ";
}
if ((isset($_SESSION['admin'])) && ($_SESSION['admin'] == true)) {
  echo "<a href='#' onclick='load_content(\"mod_acct.php\")'>Edit Patron(s)</a> | ";
  echo "<a href='#' onclick='load_content(\"insert_item.php\")'>Edit Catalog</a> | ";
  echo "<a href='#' onclick='load_content(\"mod_branch.php\")'>Edit Branch(es)</a> | ";
}

echo "<a href='logout.php'>Logout</a>";

?>
