<!DOCTYPE html>
<html>
<head>
<link rel="icon" type="image/ico" sizes="16x16" href="./images/favicon.ico">
</head>
<body>
<script src="script.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="../css/site.css">
<?php
  session_start();
  require_once '/home/mail.php';
  require_once '/home/dbInterface.php';
  processPageRequest();

  function addMovieToCart($movieID){
    $exists = movieExistsInDB($movieID);
    if($exists == 0){
      $movie = file_get_contents('http://www.omdbapi.com/?apikey=b3a0f702&i='.$movieID.'&type=movie&r=json');
      $movieArray = json_decode($movie, true);
      $exists = addMovie($movieArray["imdbID"], $movieArray["Title"], $movieArray["Year"],
               $movieArray["Rated"], $movieArray["Runtime"], $movieArray["Genre"],
               $movieArray["Actors"], $movieArray["Director"], $movieArray["Writer"],
               $movieArray["Plot"], $movieArray["Poster"]);
    }
    addMovieToShoppingCart($_SESSION["user_id"], $exists);
    displayCart();
  }

  function checkout($name, $address){
    $message = displayCart(true);
    $result = sendMail(266480228, $address, $name, "Your Receipt from myMovies!", $message);
    displayCart();
  }

  function createMovieList($forEmail=false){
    if(isset($_SESSION["cartOrder"])){
      $cart = getMoviesInCart($_SESSION["user_id"], $_SESSION["cartOrder"]);
    }else{
      $cart = getMoviesInCart($_SESSION["user_id"]);
    }
    if(!$forEmail){
      $html = '<table>
                <tr class="b">
                  <th class="b">Poster</th>
                  <th class="b">Title <small>(year)</small></th>
                  <th class="b">Info</th>
                  <th class="b">Remove</th>
                </tr>';
    }
    foreach($cart as $movieID){
      $data = getMovieData($movieID["ID"]);
      $html .= '<tr class="b">';
      if($data["Poster"] != "N/A"){
        $html .= '<td class="b"><img src="'.$data["Poster"].'" alt="poster" height=100px>';
      }else{
        $html .= '<td class="b">Poster unavailable';
      }
      $html .= '</td>
            <td class="b">'.$data["Title"].' ('.$data["Year"].')</td>';
            if($forEmail != true){
              $html .= '<td class="b"><a href="javascript:void(0);" onclick="displayMovieInformation(\''.$movieID["ID"].'\')">View More Info</a></td>
              <td class="b"><a href="#" onclick="confirmRemove(\''.$data["Title"].'\', \''.$movieID["ID"].'\')">x</a></td>';
            }
            $html .= '</tr>';
    }
    $html .= '</table>';
    return $html;
  }

  function displayCart($forEmail=false){
    $message = "";
    if(!$forEmail){
      $message .= "<title>myMovies Xpress Cart</title>";
      $message .= 'Welcome, ';
      $message .= $_SESSION["displayName"];
      $message .= ' <small><a href="#" onclick="confirmLogout()">(logout)</a></small><br><br>';
    }
    if(!$forEmail)
      $message .= '<img src="./images/xpress.jpg" alt="logo" width=25%, height=25%><br><br>';
    $count = countMoviesInCart($_SESSION["user_id"]);
    if($count == 0){
      $message .= "Add Some Movies to Your Cart";
    }else if($count >= 1){
      $message .= $count;
      $message .= " Movies in Your Shopping Cart</td></tr>";
      if(!$forEmail){
        $message .= '<br><br>Select order of movies:<br><select id="cartOrder" onchange="changeMovieDisplay();">
                <option value="0" selected>Movie Title</option>
                <option value="1">Runtime (shortest -> longest)</option>
                <option value="2">Runtime (longest -> shortest)</option>
                <option value="3">Year (old -> new)</option>
                <option value="4">Year (new -> old)</option>
              </select>';
        }
        $message .= '<div id="shopping_cart"> </div>';
      if($forEmail)
        $message .= createMovieList($forEmail);
      else
        createMovieList($forEmail);
  }
  //echo '</table>';
  if(!$forEmail){
    $message .= '<br><a href="search.php"><button type="button" name="addMovie" class="button">Add Movie</button></a>
          <button type="button" name="checkout" onClick="confirmCheckout()" class="button">Checkout</button>';
    $message .= "<div id='modalWindow' class='modal'>
          <div id='modalWindowContent' class='modal-content'>
          </div>
        </div>";
  }
  if(!$forEmail)
    echo $message;
  return $message;
}

  function processPageRequest(){
    if(!isset($_SESSION["displayName"])){
      header("location:./logon.php");
    }
    if(!isset($_GET['action'])){
      displayCart();
    }else{
      if($_GET['action'] == "add"){
        addMovieToCart($_GET["movie_id"]);
      }else if($_GET['action'] == "checkout"){
        checkout($_SESSION["displayName"], $_SESSION["email"]);
      }else if($_GET['action'] == "remove"){
        removeMovieFromCart($_GET["movie_id"]);
      }else if($_GET['action'] == "update"){
        updateMovieListing($_GET["order"]);
      }
    }
  }

  function removeMovieFromCart($removeMovieID){
    $removed = removeMovieFromShoppingCart($_SESSION["user_id"], $removeMovieID);
    displayCart();
  }

  function updateMovieListing($order){
    $_SESSION["cartOrder"] = $order;
    $result = createMovieList(false);
    echo $result;
  }
?>
</body>
</html>
