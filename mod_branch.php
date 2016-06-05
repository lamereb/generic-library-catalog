<?php
session_start();

if (isset($_SESSION['loggedin']) && ($_SESSION['loggedin'] == true)) {
?>
  
<fieldset id='postbranch'>
  <legend>Add Library Branch:</legend>
  <label>Name:</label> <input type='text' id='name' /><br>
  <label>City:</label> <input type='text' id='city' /><br>
  <label>Zip:</label> <input type='number' id='zip' min='0' max='99999' /><br>
  <input type='button' value='Enter' onclick='post_content("mod_branch_db.php")' />
</fieldset>

<?php
} else {
  echo "Please login before accessing this feature.";
}
?>
