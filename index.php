<?php
session_start();
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Welcome to Public Library</title>
    <link href="style.css" rel="stylesheet">
    <script src="controls.js"></script>
  </head>
  <body>

    <div id="topbar">
<?php
require_once('headers.php');
?>
    </div>
    <div id="content"></div>
    <div id="response"></div>

  </body>
</html>
