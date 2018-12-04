function addMovie(movieID){
  window.location.replace("./index.php?action=add&movie_id=" + movieID);
  return true;
}

function confirmCancel(form){
  var confirmed = confirm("Please confirm you wish to cancel.");
  if(!confirmed){
    return false;
  }else{
    if(form == "search"){
      window.location.replace("./index.php");
    }else{
      window.location.replace("./logon.php");
    }
    return true;
  }
}

function changeMovieDisplay(){
  var select_order = document.getElementById("cartOrder").selectedOptions[0].value;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function(){
    document.getElementById("shopping_cart").innerHTML= this.responseText;
  }
  xhttp.open("GET", "./index.php?action=update&order=" + select_order, true);
  xhttp.send();
}

function confirmCheckout(){
  var confirmed = confirm("Please confirm you wish to checkout from myMovies Xpress!");
  if(!confirmed){
    return false;
  }else{
    window.location.replace("./index.php?action=checkout");
    return true;
  }
}

function confirmLogout(){
  var confirmed = confirm("Please confirm you wish to logout of myMovies Xpress!");
  if(!confirmed){
    return false;
  }else{
    window.location.replace("./logon.php?action=logoff");
    return true;
  }
}

function confirmRemove(title, movieID){
  var confirmed = confirm("Please confirm you wish to remove " + title);
  if(!confirmed){
    return false;
  }else{
    window.location.replace("./index.php?action=remove&movie_id=" + movieID);
    return true;
  }
}

function displayMovieInformation(movie_id){
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function(){
    document.getElementById("modalWindowContent").innerHTML= this.responseText;
    showModalWindow();
  }
  xhttp.open("GET", "./movieinfo.php?movie_id=" + movie_id, true);
  xhttp.send();
}

function forgotPassword(){
  window.location.replace("./logon.php?action=forgot");
  return true;
}

function showModalWindow()
{
    var modal = document.getElementById('modalWindow');
    var span = document.getElementsByClassName("close")[0];

    span.onclick = function()
    {
        modal.style.display = "none";
    }

    window.onclick = function(event)
    {
        if (event.target == modal)
        {
            modal.style.display = "none";
        }
    }

    modal.style.display = "block";
}

function validateCreateAccountForm(){
  var displayName = document.getElementById("displayName").value;
  var email = document.getElementById("email").value;
  var confEmail = document.getElementById("confirmEmail").value;
  var username = document.getElementById("username").value;
  var password = document.getElementById("password").value;
  var confPassword = document.getElementById("confirmPassword").value;
  if(/\s/.test(email) || /\s/.test(confEmail) || /\s/.test(username) || /\s/.test(password) || /\s/.test(confPassword)){
    alert("Your entered values contain spaces.");
    return false;
  }else{
    if(email != confEmail){
      alert("Emails do not match");
      return false;
    }else{
      if(password != confPassword){
        alert("Passwords do not match");
        return false;
      }else{
        return true;
      }
    }
  }
}

function validateResetPasswordForm(){
  var password = document.getElementById("password").value;
  var confPassword = document.getElementById("confirmPassword").value;

  if(/\s/.test(password) || /\s/.test(confPassword)){
    alert("Your entered values contain spaces.");
    return false;
  }else{
    if(password != confPassword){
      alert("Passwords do not match");
      return false;
    }else{
      return true;
    }
  }
}
