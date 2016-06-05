<?php
require_once 'store.php';
error_reporting(E_ALL);
//ini_set('display_errors', 'On');
session_start();
$insert_book = true;
$fname_array = array();
$lname_array = array();
// get isbn
if (isset($_POST['querynum']) /*&& $_POST['querynum'] != ""*/) {
  $isbn=$_POST['querynum'];
  //line below so you can just copy/paste ISBN's from Amazon
  $isbn=preg_replace('/[^0-9]/', '', $isbn);

  // create url
  $url='http://xisbn.worldcat.org/webservices/xid/isbn/'.$isbn.'?method=getMetadata&format=xml&fl=*';
  // load url into $response
  $response=simplexml_load_file($url);
  if ($response == false) {
    echo "WorldCat service unavailable. You will have to manually enter data until this issue is resolved. ";
    $insert_book = false;
  } else {
    if (!$response->isbn["title"])
    {
      echo "No book with that ISBN";
      $insert_book = false;
    } else {
      $title=$response->isbn["title"];
      $author=$response->isbn["author"]; 
      $year=$response->isbn["year"];
    }
    // code for making author field an fname/lname array
    $a_split=explode(" ", $author);

    if ($a_split[0] == 'by') {
      $throwaway=array_shift($a_split);
    }
    foreach ($a_split as &$val) {
      $size=strlen($val)-1;
      if ($val[$size] == '.' || $val[$size] ==',') {
        $temp=substr($val, 0, $size);
        $val=$temp;
      }
    }
    $count=count($a_split);
    if (($count == 2) || (($count % 2) == 0)) {
      for ($i = 0; $i < $count; $i++) {
        if (($i % 2) == 0) {
          array_push($fname_array, $a_split[$i]);
        } else {
          array_push($lname_array, $a_split[$i]);
        }
      }
    } else if ($count == 3) {
      array_push($fname_array, $a_split[0]);
      array_push($lname_array, $a_split[2]);
    }
    // end WorldCat query code
  }
} else {
  if (isset ($_POST['isbn']) && ($_POST['isbn'] != "")) {
    $isbn=$_POST['isbn'];
    $isbn=preg_replace('/[^0-9]/', '', $isbn);
  } else $insert_book = false;
  if (isset ($_POST['title']) && ($_POST['title'] != "")) {
    $title=strip_tags($_POST['title']);
  } else $insert_book = false;
  if (isset ($_POST['year']) && ($_POST['year'] != "")) {
    $year=strip_tags($_POST['year']);
  } else $insert_book = false;
  if (isset ($_POST['fname']) && ($_POST['fname'] != "")) {
    array_push($fname_array, strip_tags($_POST['fname']));
  } else $insert_book = false;
  if (isset ($_POST['lname']) && ($_POST['lname'] != "")) {
    array_push($lname_array, strip_tags($_POST['lname']));
  } else $insert_book = false;
}
// FIRST, query db to see if isbn is already there
if ($insert_book) {
  $mysqli = new mysqli($db_host, $db_un, $db_pw, $db_db);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (".$mysqli->connect_errno.
      ") ".$mysqli->connect_error;
  }
  //prepare statement
  if (!($stmt=$mysqli->prepare("SELECT isbn FROM book WHERE isbn=?"))) {
    echo "Prepare failed: (".$mysqli->errno.") ".$mysqli->error;
  }
  //bind
  if (!($stmt->bind_param("s", $isbn))) {
    echo "Binding parameters failed: (".$mysqli->errno.") ".$stmt->error;
  }
  //execute
  if (!($stmt->execute())) {
    echo "Execute failed: (".$stmt->errno.") ".$stmt->error;
  }
  //bind result variables
  $stmt->bind_result($intable);
  //fetch value
  $stmt->fetch();
  //close statement
  $stmt->close();
  //close connection
  $mysqli->close();

  if ($intable == $isbn) {
    $book_intable = true;
  } else {
    $book_intable = false;
  }
  // IF NOT IN DB, insert book into SQL database
  if ($book_intable != true) {
    // open connection
    $mysqli = new mysqli($db_host, $db_un, $db_pw, $db_db);
    if ($mysqli->connect_errno) {
      echo "Failed to connect to MySQL: (".$mysqli->connect_errno.
        ") ".$mysqli->connect_error;
    }
    //prepare statement
    if (!($stmt=$mysqli->prepare("INSERT INTO book (isbn, title, pub_year) VALUES(?,?,?)"))) {
      echo "Prepare failed: (".$mysqli->errno.") ".$mysqli->error;
    }
    //bind
    if (!($stmt->bind_param("sss", $isbn, $title, $year))) {
      echo "Binding parameters failed: (".$mysqli->errno.") ".$stmt->error;
    }
    //execute
    if (!($stmt->execute())) {
      echo "Execute failed: (".$stmt->errno.") ".$stmt->error;
    }
    //close statement
    $stmt->close();
    //close connection
    $mysqli->close();

    for ($j = 0; $j < count($lname_array); $j++) {

      // NEXT, check to see if author is already in author table
      $mysqli = new mysqli($db_host, $db_un, $db_pw, $db_db);
      if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (".$mysqli->connect_errno.
          ") ".$mysqli->connect_error;
      }
      //prepare statement
      if (!($stmt=$mysqli->prepare("SELECT author_id FROM author WHERE fname=? AND lname=?"))) {
        echo "Prepare failed: (".$mysqli->errno.") ".$mysqli->error;
      }
      //bind
      if (!($stmt->bind_param("ss", $fname_array[$j], $lname_array[$j]))) {
        echo "Binding parameters failed: (".$mysqli->errno.") ".$stmt->error;
      }
      //execute
      if (!($stmt->execute())) {
        echo "Execute failed: (".$stmt->errno.") ".$stmt->error;
      }
      //bind result variables
      $stmt->bind_result($auth_intable);
      //fetch value
      $stmt->fetch();
      //close statement
      $stmt->close();
      //close connection
      $mysqli->close();

      if ($auth_intable) {
        $auth_id=$auth_intable;
      } else {
        // NOT in table, so insert author into author table
        // open connection
        $mysqli = new mysqli($db_host, $db_un, $db_pw, $db_db);
        if ($mysqli->connect_errno) {
          echo "Failed to connect to MySQL: (".$mysqli->connect_errno.
            ") ".$mysqli->connect_error;
        }
        //prepare statement
        if (!($stmt=$mysqli->prepare("INSERT INTO author (fname, lname) VALUES(?,?)"))) {
          echo "Prepare failed: (".$mysqli->errno.") ".$mysqli->error;
        }
        //bind
        if (!($stmt->bind_param("ss", $fname_array[$j], $lname_array[$j]))) {
          echo "Binding parameters failed: (".$mysqli->errno.") ".$stmt->error;
        }
        //execute
        if (!($stmt->execute())) {
          echo "Execute failed: (".$stmt->errno.") ".$stmt->error;
        }
        //close statement
        $stmt->close();
        //close connection
        $mysqli->close();

        // NOW, add book_author link
        // FIRST, GET author_id of author just added
        // open connection
        $mysqli = new mysqli($db_host, $db_un, $db_pw, $db_db);
        if ($mysqli->connect_errno) {
          echo "Failed to connect to MySQL: (".$mysqli->connect_errno.
            ") ".$mysqli->connect_error;
        }
        //prepare statement
        if (!($stmt=$mysqli->prepare("SELECT author_id FROM author WHERE fname=? AND lname=?"))) {
          echo "Prepare failed: (".$mysqli->errno.") ".$mysqli->error;
        }
        //bind
        if (!($stmt->bind_param("ss", $fname_array[$j], $lname_array[$j]))) {
          echo "Binding parameters failed: (".$mysqli->errno.") ".$stmt->error;
        }
        //execute
        if (!($stmt->execute())) {
          echo "Execute failed: (".$stmt->errno.") ".$stmt->error;
        }
        //bind result variables
        $stmt->bind_result($auth_id);
        //fetch value
        $stmt->fetch();
        //close statement
        $stmt->close();
        //close connection
        $mysqli->close();
      }

      // THEN, ADD book_author entry
      $mysqli = new mysqli($db_host, $db_un, $db_pw, $db_db);
      if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (".$mysqli->connect_errno.
          ") ".$mysqli->connect_error;
      }
      //prepare statement

      if (!($stmt=$mysqli->prepare("INSERT INTO book_author (isbn, author_id) VALUES(?,?)"))) {
        echo "Prepare failed: (".$mysqli->errno.") ".$mysqli->error;
      }
      //bind
      if (!($stmt->bind_param("ss", $isbn, $auth_id))) {
        echo "Binding parameters failed: (".$mysqli->errno.") ".$stmt->error;
      }
      //execute
      if (!($stmt->execute())) {
        echo "Execute failed: (".$stmt->errno.") ".$stmt->error;
      }
      //close statement
      $stmt->close();
      //close connection
      $mysqli->close();
    }
  }
  if (isset($_POST['numcopies']) && ($_POST['numcopies'] > 0)) {
    $count = $_POST['numcopies'];
  } else if (isset($_POST['numcopies2']) && ($_POST['numcopies2'] > 0)) {
    $count = $_POST['numcopies2'];
  } 
  else {
    $count = 0;
  }
  if ($count > 20) {
    echo "Max of 20 items per query. Resubmit to add more copies.<br>";
    $count = 20;
  }
  // LAST, ADD item with this isbn
  $mysqli = new mysqli($db_host, $db_un, $db_pw, $db_db);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (".$mysqli->connect_errno.
      ") ".$mysqli->connect_error;
  }
  for ($i = 0; $i < $count; $i++) {
    //prepare statement
    if (!($stmt=$mysqli->prepare("INSERT INTO item (available, plocation, blocation, isbn) VALUES(?,?,?,?)"))) {
      echo "Prepare failed: (".$mysqli->errno.") ".$mysqli->error;
    }
    //bind

    $avail = true;
    $ploc = null;

    if (isset($_POST['branch']) && ($_POST['branch'] > 0)) {
      $location = $_POST['branch'];

    } else if (isset($_POST['branch2']) && ($_POST['branch2'] > 0)) {
      $location = $_POST['branch2'];
    }
    if (!($stmt->bind_param("iiis", $avail, $ploc, $location, $isbn))) {
      echo "Binding parameters failed: (".$mysqli->errno.") ".$stmt->error;
    }
    //execute
    if (!($stmt->execute())) {
      echo "Execute failed: (".$stmt->errno.") ".$stmt->error;
    }
    //close statement
    $stmt->close();
  }
  //close connection
  $mysqli->close();

  //CODE FOR CHECKING RESULTS
  //print_r($response);    
  //echo $url;
  echo "<br>";
  echo "Title :".$title."<br>";
  echo "Fname :".$fname_array[0]."<br>";
  echo "Lname :".$lname_array[0]."<br>";
  echo "Year  :".$year."<br>";
  echo "ISBN  :".$isbn."<br>";

  echo $count." copies successfully added to branch# ".$location;
} else {
  echo "Not enough data given";
}
?>
