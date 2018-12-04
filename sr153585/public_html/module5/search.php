<!DOCTYPE html>
<html>
<head>
<link rel="icon" type="image/ico" sizes="16x16" href="./images/favicon.ico">
</head>
<body>
<link rel="stylesheet" type="text/css" href="../css/site.css">
<script src="script.js" type="text/javascript"></script>
<?php
session_start();
processPageRequest();

function displaySearchForm(){
  echo '<title>Search Form</title>
        Welcome, ';
  Echo $_SESSION["displayName"];
  Echo ' <small><a href="#" onclick="confirmLogout()">(logout)</a></small>
        <br><br><img src="./images/xpress.jpg" alt="logo" width=25%, height=25%><br><br>
  <form action="./search.php" method="post">
    <input type="text" class="input" required name="keyword" placeholder="Search for movies with a keyword" id="keyword"><br><br>
    <a href="#" onclick="confirmCancel(\'search\')"><button type="button" formnovalidate class="button">Cancel</button></a>
    <button type="reset" value="Reset" class="button">Clear</button>
    <button type="submit" value="Submit" class="button">Search</button>
  </form>';
}

function displaySearchResults($searchString){
  $results = file_get_contents('http://www.omdbapi.com/?apikey=b3a0f702&s='.urlencode($searchString).'&type=movie&r=json');
  $array = json_decode($results, true)["Search"];
  echo '<title>Search Results</title>
        Welcome, ';
  echo $_SESSION["displayName"];
  echo ' <small><a href="#" onclick="confirmLogout()">(logout)</a></small>
        <br><br><img src="./images/xpress.jpg" alt="logo" width=25%, height=25%><br><br>';
  echo '<table class="c"><tr class="c"><td colspan="3" class="c">';
  echo count($array);
  echo ' movies found</td></tr>';
  if(count($array) >= 1){
    echo '<tr class="c">
              <th class="c">Poster</th>
              <th class="c">Title <small>(year)</small></th>
              <th class="c">Add</th>
            </tr>';
    foreach ($array as $key => $value){
      if($value["Poster"] != "N/A"){
        echo '<tr class="c"><td class="c"><img src="'.$value["Poster"].'" alt="poster" height=100px>';
      }else{
        echo '<tr class="c"><td class="c">Poster unavailable';
      }
      echo '</td><td class="c"> <a href="https://www.imdb.com/title/';
      echo $value["imdbID"];
      echo '/" target="_blank">';
      echo $value["Title"];
      echo ' <small>(';
      echo $value["Year"];
      echo ')</small></a></td>';
      echo '<td class="c"><a href="#" onclick="addMovie(\''.$value["imdbID"].'\')">+</a></td></tr>';
    }
  }
  echo '</table>';
  echo '<a href="#" onclick="confirmCancel(\'search\')"><button type="button" formnovalidate class="button">Cancel</button></a>';
}

function processPageRequest(){
  if(!isset($_SESSION["displayName"])){
    header("location:./logon.php");
  }
  if(empty($_POST)){
    displaySearchForm();
  }else{
    displaySearchResults($_POST["keyword"]);
  }
}

?>
</body>
</html>
