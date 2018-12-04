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
  processPageRequest();

  function addMovieToCart($movieID){
    $myfile = file_get_contents("./data/cart.db");
    $array = explode("\n", $myfile);
    array_push($array, $movieID);
    $array = array_diff($array, array(''));
    $data = implode("\n", $array);
    $myfile = file_put_contents("./data/cart.db", $data);
    displayCart();
  }

  function checkout($name, $address){
    $myfile = file_get_contents("./data/cart.db");
    $array = explode("\n", $myfile);
    $count = count($array);
    $message = 'There are '.$count.' Movies in Your Shopping Cart';
    foreach($array as $movieID){
      if($movieID == ''){
        continue;
      }else{
        $movie = file_get_contents('http://www.omdbapi.com/?apikey=b3a0f702&i='.$movieID.'&type=movie&r=json');
        $movieArray = json_decode($movie, true);
        $message .= '<table>';
        if($movieArray["Poster"] != "N/A"){
          $message .= '<tr><td><img src="'.$movieArray["Poster"].'" alt="poster" height=100>';
        }else{
          $message .= '<tr><td>Poster unavailable';
        }
        $message .= '</td><td <a href="https://www.imdb.com/title/';
        $message .= $movieArray["imdbID"];
        $message .= '" target="_blank">';
        $message .= $movieArray["Title"];
        $message .= ' <small>(';
        $message .= $movieArray["Year"];
        $message .= ')</small></a></td></table>';
        }
      }
    $result = sendMail(266480228, $address, $name, "Your Receipt from myMovies!", $message);
    echo $result;
    return $result;
  }

  function displayCart(){
    $myfile = file_get_contents("./data/cart.db");
    $array = explode("\n", $myfile);
    echo "<title>myMovies Xpress Cart</title>";
    echo 'Welcome, ';
    Echo $_SESSION["displayName"];
    Echo ' <small><a href="#" onclick="confirmLogout()">(logout)</a></small>';
    echo '<br><br><img src="./images/xpress.jpg" alt="logo" width=25%, height=25%><br><br>';
    echo '<table class="b"><tr class="b"><td colspan="3" class="b">';
    if(count($array) == 0 || $array[0] == ''){
      echo "Add Some Movies to Your Cart";
    }else if(count($array) >= 1){
    echo count($array);
    echo " Movies in Your Shopping Cart</td></tr>";
    echo '<tr class="b">
              <th class="b">Poster</th>
              <th class="b">Title <small>(year)</small></th>
              <th class="b">Remove</th>
            </tr>';
    foreach($array as $movieID){
      if($movieID == ''){
        continue;
      }else{
        $movie = file_get_contents('http://www.omdbapi.com/?apikey=b3a0f702&i='.$movieID.'&type=movie&r=json');
        $movieArray = json_decode($movie, true);
        if($movieArray["Poster"] != "N/A"){
          echo '<tr class="b">
                  <td class="b"><img src="'.$movieArray["Poster"].'" alt="poster" height=100px>';
        }else{
          echo '<tr class="b">
                  <td class="b">Poster unavailable';
        }
        echo '</td>
                <td class="b"> <a href="https://www.imdb.com/title/';
        echo $movieArray["imdbID"];
        echo '" target="_blank">';
        echo $movieArray["Title"];
        echo ' <small>(';
        echo $movieArray["Year"];
        echo ')</a></td>';
        echo '<td class="b"><small><a href="#" onclick="confirmRemove(\''.$movieArray["Title"].'\', \''.$movieArray["imdbID"].'\'';
        echo ')">x</a></td></tr>';
      }
    }
  }
  echo '</table>';
  echo '<br><a href="search.php"><button type="button" name="addMovie" class="button">Add Movie</button></a>
        <button type="button" name="checkout" onClick="confirmCheckout()" class="button">Checkout</button>';
}

  function processPageRequest(){
    if(!isset($_GET['action'])){
      displayCart();
    }else{
      if($_GET['action'] == "add"){
        addMovieToCart($_GET['movie_id']);
      }else if($_GET['action'] == "checkout"){
        checkout($_SESSION["displayName"], $_SESSION["email"]);
      }else if($_GET['action'] == "remove"){
        removeMovieFromCart($_GET['movie_id']);
      }
    }
  }

  function removeMovieFromCart($removeMovieID){
    $myfile = file_get_contents("./data/cart.db");
    $array = explode("\n", $myfile);
    $array = array_diff($array, array($removeMovieID));
    $data = implode("\n", $array);
    $myfile = file_put_contents("./data/cart.db", $data);
    displayCart();
  }
?>
</body>
</html>
