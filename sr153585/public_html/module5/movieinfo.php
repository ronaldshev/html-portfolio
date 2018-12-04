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
require_once '/home/dbInterface.php';
processPageRequest();

function createMessage($movieId){
  $gotten = getMovieData($movieId);
  if($gotten == null){
    $plot = "Invalid Movie Id!";
  }else{
    $plot = $gotten["Plot"];
  }
  $message = "<div class='modal-header'>
                <span class='close'>[Close]</span>
                <h2>".$gotten['Title']." (".$gotten['Year'].") Rated ".$gotten['Rating']." ".$gotten['Runtime']."<br />".$gotten['Genre']."</h2>
              </div>
              <div class='modal-body'>
                <p>Actors: ".$gotten['Actors']."<br />Directed By: ".$gotten['Director']."<br />Written By: ".$gotten['Writer']."</p>
              </div>
              <div class='modal-footer'>
                <p>".$plot."</p>
              </div>";
  echo $message;
}

function processPageRequest(){
  if(!isset($_SESSION["displayName"])){
    header("location:./logon.php");
  }
  if(empty($_GET)){
    createMessage(0);
  }else{
    if(isset($_GET['movie_id'])){
      createMessage($_GET['movie_id']);
    }
  }
}

?>
</body>
</html>
