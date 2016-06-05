<?php
session_start();
?>

<fieldset id='postuser'>
  <legend>Add New User</legend>
  <label>First Name:</label> <input type="text" id='fname' name="fname" /><br>
  <label>Last Name:</label> <input type="text" id='lname' name="lname" /><br>
  <label>Username:</label> <input type="text" id='uname' name="uname" /><br>
  <label>Password:</label> <input type="password" id='pw' name="pw" /><br>
  <input type="radio" name="admin" value="false" checked />Patron<br>
  <input type="radio" name="admin" value="true" />Administrator<br>
  <input type="button" value="Submit!" onclick='post_content("add_db.php")' />
</fieldset>

<fieldset>
  <legend>Show Users</legend>
  <input type="button" value="Show" onclick='load_response("show_patrons.php")' />
</fieldset>

