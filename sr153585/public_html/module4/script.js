function addMovie(movieID){
  window.location.replace("./index.php?action=add&movie_id=" + movieID);
  return true;
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

function confirmLogout($message){
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
