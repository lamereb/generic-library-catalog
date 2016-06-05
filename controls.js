// This function fills the content div with whatever php page is sent to it
function load_content(url) {
  var content = document.getElementById('content');

  //clear response div
  var response = document.getElementById('response');
  // http://stackoverflow.com/questions/5057759/how-do-i-clear-the-contents-of-a-div-without-innerhtml
  while (response.hasChildNodes()) {
    response.removeChild(response.firstChild);
  }
  if (XMLHttpRequest) var req = new XMLHttpRequest();
  else var req = new ActiveXObject('Microsoft.XMLHTTP');
  req.open('GET', url, true);
  req.send();
  req.onreadystatechange = function() {
    if (req.readyState === 4) {
      if (req.status == 200) {
        content.innerHTML = req.responseText;
      } else {
        content.innerHTML = 'Error loading doc';
      }
    }
  }
}

function get_login() {
  // get elements by id value (in the text boxes)
  var user = document.getElementById('user').value;
  var pass = document.getElementById('pass').value;
  var content = document.getElementById('content');

  // post those values to log_check.php
  if (XMLHttpRequest) { var req = new XMLHttpRequest(); }
  else { var req = new ActiveXObject('Microsoft.XMLHTTP'); }
  req.open('POST', 'log_check.php', true);
  req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  req.send('user='+user+'&pass='+pass);

  req.onreadystatechange = function() {
    if (req.readyState === 4) {
      if (req.status == 200) {
        content.innerHTML = req.responseText;
        update_topbar();
      }
    }
  }
}

// called by get_login to update topbar header with logged-in status
function update_topbar() {
  var topbar = document.getElementById('topbar');

  if (XMLHttpRequest) { var req = new XMLHttpRequest(); }
  else { var req = new ActiveXObject('Microsoft.XMLHTTP'); }

  req.open('GET', 'headers.php', true);
  req.send();

  req.onreadystatechange = function() {
    if (req.readyState === 4) {
      if (req.status == 200) {
        topbar.innerHTML = req.responseText;
      }
    }
  }
}

function post_content(url) {
  var post_query = get_querystring();
  var response = document.getElementById('response');

  if (XMLHttpRequest) var req = new XMLHttpRequest();
  else var req = new ActiveXObject('Microsoft.XMLHTTP');
  req.open('POST', url, true);
  req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  req.send(post_query);

  req.onreadystatechange = function() {
    if (req.readyState === 4) {
      if (req.status == 200) {
        response.innerHTML = req.responseText;
      } else {
        response.innerHTML = 'Error loading post doc';
      }
    }
  }
}

function post_content_not_response(url) {
  var post_query = get_querystring();
  var response = document.getElementById('content');

  if (XMLHttpRequest) var req = new XMLHttpRequest();
  else var req = new ActiveXObject('Microsoft.XMLHTTP');
  req.open('POST', url, true);
  req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  req.send(post_query);

  req.onreadystatechange = function() {
    if (req.readyState === 4) {
      if (req.status == 200) {
        response.innerHTML = req.responseText;
      } else {
        response.innerHTML = 'Error loading post doc';
      }
    }
  }
}

function get_querystring() {
  var fields = document.getElementsByTagName('fieldset');
  var id = fields[0].getAttribute('id');
  var querystring;

  if (id == 'postbooks') {
    var isbn = document.getElementById('isbn').value;
    var title = document.getElementById('title').value;
    var lname = document.getElementById('lname').value;
    var fname = document.getElementById('fname').value;
    var year = document.getElementById('year').value;
    var numcopies = document.getElementById('numcopies').value;
    var branch = document.getElementById('branch').value;
    var querynum = document.getElementById('querynum').value;
    var numcopies2 = document.getElementById('numcopies2').value;
    var branch2 = document.getElementById('branch2').value;

    if (querynum) {
      querystring='querynum='+querynum+'&numcopies='+numcopies2+'&branch='+branch2;
    } else {
      querystring='isbn='+isbn+'&title='+title+'&lname='+lname+'&fname='+fname+'&year='+year+'&numcopies='+numcopies+'&branch='+branch;
    }
  } else if (id == 'postuser') {
    var fname = document.getElementById('fname').value;
    var lname = document.getElementById('lname').value;
    var uname = document.getElementById('uname').value;
    var pw = document.getElementById('pw').value;
    var admins = document.getElementsByName('admin');
    //http://stackoverflow.com/questions/9561625/checking-value-of-radio-button-group-via-javascript
    for (var i = 0; i < admins.length; i++) {
      if (admins[i].checked == true) {
        var admin = admins[i].value;
      }
    }
    querystring='fname='+fname+'&lname='+lname+'&uname='+uname+'&pw='+pw+'&admin='+admin;

  } else if (id == 'post-newuser') {
    var fname = document.getElementById('fname').value;
    var lname = document.getElementById('lname').value;
    var uname = document.getElementById('uname').value;
    var pw = document.getElementById('pw').value;
    
    querystring='fname='+fname+'&lname='+lname+'&uname='+uname+'&pw='+pw+'&admin='+admin;
  }
  
  
  
  else if (id == 'postbranch') {
    var name = document.getElementById('name').value;
    var city = document.getElementById('city').value;
    var zip = document.getElementById('zip').value;

    querystring='name='+name+'&city='+city+'&zip='+zip;
  } else if (id == 'branch-filter') {
    var filter = document.getElementById('filter').value;
    querystring='filter='+filter;
    if (document.getElementById('patron-id')) {
      var patron_id = document.getElementById('patron-id').value;
    }
    if (patron_id) {
      querystring+='&patron-id='+patron_id;
    }
    console.log(querystring);
  } else if (id == 'checkedout') {
    var patron_id = document.getElementById('patron-id').value;
    if (patron_id) {
      querystring='patron-id='+patron_id;
    }
  }

  return querystring;
}
function load_response(url) {
  var response = document.getElementById('response');

  if (XMLHttpRequest) { var req = new XMLHttpRequest(); }
  else { var req = new ActiveXObject('Microsoft.XMLHTTP'); }
  req.open('POST', url, true);
  req.send();

  req.onreadystatechange = function() {
    if (req.readyState === 4) {
      if (req.status == 200) {
        response.innerHTML = req.responseText;
      } else {
        response.innerHTML = 'Error loading doc';
      }
    }
  }
}

