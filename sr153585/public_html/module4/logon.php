<!DOCTYPE html>
<html>
<head>
<link rel="icon" type="image/ico" sizes="16x16" href="./images/favicon.ico">
</head>
<body>
<script src="script.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="../css/site.css">
<?php
processPageRequest();

function processPageRequest(){
  session_unset();
  if(empty($_POST)){
    displayLoginForm();
  }else{
    authenticateUser($_POST["username"], $_POST["password"]);
  }
}

function authenticateUser($username, $password){
  $myfile = file_get_contents("./data/credentials.db");
  $array = explode(",", $myfile);
  if($array[0] == $username && $array[1] == $password){
    session_start();
    $_SESSION["displayName"] = $array[2];
    $_SESSION["email"] = $array[3];
    header("location:index.php");
  }
  else{
      displayLoginForm("Invalid login credentials.");
  }
}

function displayLoginForm($message=""){
  echo "<title>Login to myMovies Xpress!</title>";
  echo '<img src="./images/xpress.jpg" alt="logo" width=25%, height=25%><br>';
  if($message != '')
    echo '<br><b>'.$message.'</b>';
  echo "<br>Please type in your username and password<br>";
  echo '<br><form action="./logon.php" method="post">
    Username:<br><input type="text" class="input" required name="username" id="username"><br>
    Password:<br><input type="password" class="input" required name="password" id="password">
    <br><br><button type="reset" value="Reset" class="button">Reset</button>
    <button type="submit" value="Submit" class="button">Login</button>
    </form>
    <br><a href="../index.html" class="eport">ePortfolio</a>';
}

?>
</body>
</html>
