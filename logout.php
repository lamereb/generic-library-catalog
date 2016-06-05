<?php

session_start();
//redirect to index.php
$filepath = explode('/', $_SERVER['PHP_SELF'], -1);
$filepath = implode('/', $filepath);
$redirect = "http://".$_SERVER['HTTP_HOST'].$filepath;
header("Location: {$redirect}/index.php", true);
$_SESSION = array();
session_destroy();



?>
