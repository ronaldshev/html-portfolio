<!DOCTYPE html>
<html>
<head>
<link rel="icon" type="image/ico" sizes="16x16" href="./images/favicon.ico">
</head>
<body>
<script src="script.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="../css/site.css">
<?php
require_once '/home/mail.php';
require_once '/home/dbInterface.php';
processPageRequest();

function authenticateUser($username, $password){
  $validated = validateUser($username, $password);
  if(is_array($validated)){
    session_start();
    $_SESSION["user_id"] = $validated[0];
    $_SESSION["displayName"] = $validated[1];
    $_SESSION["email"] = $validated[2];
    header("location:index.php");
  }else{
    displayLoginForm("Invalid login credentials.");
  }
}

function createAccount($username, $password, $displayName, $email){
  $id = addUser($username, $password, $displayName, $email);
  if($id > 0){
    sendValidationEmail($id, $displayName, $email);
  }else if($id == 0){
    displayLoginForm("The provided username already exists.");
  }
}

function displayCreateAccountForm(){
  echo "<title>Create Account</title>";
  echo '<img src="./images/xpress.jpg" alt="logo" width=25%, height=25%><br>';
  echo "<br>Create your account with myMovies Xpress!<br><br>";
  echo '<form action="./logon.php" onsubmit="return validateCreateACcountForm();" method="post">
          <b>Display Name:</b><br><input type="text" class="input" required name="displayName" id="displayName"><br>
          <b>Email Address:</b><br><input type="text" class="input" required name="email" id="email"><br>
          <b>Confirm Email Address:</b><br><input type="text" class="input" required name="confirmEmail" id="confirmEmail"><br>
          <b>Username:</b><br><input type="text" class="input" required name="username" id="username"><br>
          <b>Password:</b><br><input type="password" class="input" required name="password" id="password"><br>
          <b>Confirm Password:</b><br><input type="password" class="input" required name="confirmPassword" id="confirmPassword"><br>

          <br><a href="#" onclick="confirmCancel(\'create\')"><button type="button" formnovalidate class="button">Cancel</button></a>
          <button type="reset" value="Reset" class="button">Clear</button>
          <button type="submit" value="Submit" class="button">Create Account</button>

          <input type="hidden" name="action" value="create">

          </form>';
}

function displayForgotPasswordForm(){
  echo "<title>Forgot Password</title>";
  echo '<img src="./images/xpress.jpg" alt="logo" width=25%, height=25%><br>';
  echo "<br>Forgot your password? Enter your username and myMovies Xpress! can help.<br><br>";
  echo '<form action="./logon.php" method="post">
          <b>Username:</b><br><input type="text" class="input" required name="username" id="username"><br>

          <br><a href="#" onclick="confirmCancel(\'forgot\')"><button type="button" formnovalidate class="button">Cancel</button></a>
          <button type="reset" value="Reset" class="button">Clear</button>
          <button type="submit" value="Submit" class="button">Submit</button>

          <input type="hidden" name="action" value="forgot">

          </form>';
}

function displayLoginForm($message=""){
  echo "<title>Login to myMovies Xpress!</title>";
  echo '<img src="./images/xpress.jpg" alt="logo" width=25%, height=25%><br>';
  if($message != '')
    echo '<br><b>'.$message.'</b>';
  echo "<br>Please type in your username and password to login.<br>";
  echo '<br><form action="./logon.php" method="post">
    Username:<br><input type="text" class="input" required name="username" id="username"><br>
    Password:<br><input type="password" class="input" required name="password" id="password">

    <br><br><button type="reset" value="Reset" class="button">Reset</button>
    <button type="submit" value="Submit" class="button">Login</button>

    <input type="hidden" name="action" value="login">

    <br><br><a href="./logon.php?form=create">Create Account</a>
    <a href="./logon.php?form=forgot">Forgot Password</a>

    </form>
    <br><a href="../index.html" class="eport">ePortfolio</a>';
}

function displayResetPasswordForm($user_id){
  echo "<title>Forgot Password</title>";
  echo '<img src="./images/xpress.jpg" alt="logo" width=25%, height=25%><br>';
  echo "<br>Type in your new password below.<br><br>";
  echo '<form action="./logon.php" onsubmit="return validateResetPasswordForm();" method="post">
          Password:<br><input type="password" class="input" required name="password" id="password"><br>
          Confirm Password:<br><input type="password" class="input" required name="confirmPassword" id="confirmPassword"><br>

          <br><a href="#" onclick="confirmCancel(\'reset\')"><button type="button" formnovalidate class="button">Cancel</button></a>
          <button type="reset" value="Reset" class="button">Clear</button>
          <button type="submit" value="Submit" class="button">Reset Password</button>

          <input type="hidden" name="action" value="reset">
          <input type="hidden" name="user_id" value="'.$user_id.'">

          </form>';
}

function processPageRequest(){
  session_unset();

  if(isset($_POST)){
    if(isset($_POST['action'])){
      if($_POST['action'] == "create"){
        createAccount($_POST["username"], $_POST["password"], $_POST["displayName"], $_POST["email"]);
      }else if($_POST['action'] == "forgot"){
        sendForgotPasswordEmail($_POST["username"]);
      }else if($_POST['action'] == "login"){
        authenticateUser($_POST["username"], $_POST["password"]);
      }else if($_POST['action'] == "reset"){
        resetPassword($_POST["user_id"], $_POST["password"]);
      }
    }
  }
  if(isset($_GET)){
    if(isset($_GET['action'])){
      if($_GET['action'] == "validate"){
        validateAccount($_GET["user_id"]);
      }else if($_GET['action'] == "logoff"){
        displayLoginForm("Successfully logged out.");
      }
    }else if(isset($_GET['form'])){
      if($_GET['form'] == "create"){
        displayCreateAccountForm();
      }else if($_GET['form'] == "forgot"){
        displayForgotPasswordForm();
      }else if($_GET['form'] == "reset"){
        displayResetPasswordForm($_GET['user_id']);
      }
    }
  }
  if(empty($_GET) && empty($_POST)){
    displayLoginForm();
  }

}

function resetPassword($user_id, $password){
  $success = resetUserPassword($user_id, $password);
  if($success){
    displayLoginForm("Your password was successfully updated.");
  }else{
    displayLoginForm("The provided user ID does not exist.");
  }
}

function sendForgotPasswordEmail($username){
  $data = getUserData($username);
  $message = '<h2>myMovies Xpress!</h2>
                This email is sent because you clicked forgot password
                at myMovies Xpress! Follow the instructions below to reset your password.<br><br>
                <b>To reset your password, you MUST click the link below:</b><br><br>
                <a href="http://192.168.100.86/~sr153585/module5/logon.php?form=reset&user_id='.$data[0].'">http://192.168.100.86/~sr153585/module5/logon.php?form=reset&user_id='.$data[0].'</a>';
  $result = sendMail(266480228, $data[2], $data[1], "myMovies! Password Reset Request", $message);
  displayLoginForm("Password reset email sent.");
}

function sendValidationEmail($user_id, $displayName, $email){
  $message = '<h2>myMovies Xpress!</h2>
                This email is sent because you created an account
                on myMovies Xpress! Follow the instructions below to validate it was you.<br><br>
                <b>To finish setting up your account, you MUST click the link below:</b><br><br>
                <a href="http://192.168.100.86/~sr153585/module5/logon.php?action=validate&user_id='.$user_id.'">http://192.168.100.86/~sr153585/module5/logon.php?action=validate&user_id='.$user_id.'</a>';
  $result = sendMail(266480228, $email, $displayName, "myMovies! Account Validation", $message);
  displayLoginForm("Account validation email sent.");
}

function validateAccount($user_id){
  $activated = activateAccount($user_id);
  if($activated){
    displayLoginForm("Your account has been successfully activated.");
  }else{
    displayLoginForm("The specified User ID does not exist.");
  }
}
?>
</body>
</html>
