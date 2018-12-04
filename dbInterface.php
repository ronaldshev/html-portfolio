<?php

require_once '/home/dbConnect.php'; 

/*
	Activiate a new user's account.
	
	Parameters:
		userId - The user ID of the user.
				
	Return:
		true - The specified user's accout was activated.
		false - The specified user ID is invalid.
 */
function activateAccount($userId)
{	
	try
	{
		$conn = connect();
		$query = $conn->prepare("UPDATE Users SET Active = 1 WHERE ID = ?");
		$query->execute([$userId]);
		return $query->rowCount() == 1 ? true : false;
	}
	catch(PDOException $e)
	{
		exit("Database Error: " . $e->getMessage());
	}
}

/*
	Add a new movie to the database.
	
	Parameters:
		imdbId - The IMDB ID (tt0123456) of the movie.
		title - The title of the movie.
		year - The year of the movie.
		rating - The rating (G, PG, PG-13, R, etc.) of the movie.
		runtime - The runtime (in minutes) of the movie.
		genre - The genre(s) of the movie.
		actors - The actor(s) of the movie.
		director - The director(s) of the movie.
		writer - The writer(s) of the movie.
		plot - The plot of the movie.
		poster - The URL of the movie poster.
		
	Return:
		>0 - The ID of the movie added to or found in the database.
 */
function addMovie($imdbId, $title, $year, $rating, $runtime, $genre, $actors, $director, $writer, $plot, $poster)
{
	try 
	{
		$movieId = movieExistsInDB($imdbId); // Get the movie ID if it already exists in the database
		
		if($movieId > 0) // Movie already exist - no need to add it
		{
			return $movieId;
		}
		else
		{
			$conn = connect();
			$query = $conn->prepare("INSERT INTO Movies (IMDB_ID, Title, Year, Rating, Runtime, Genre, Actors, Director, Writer, Plot, Poster) VALUES (:imdbId, :title, :year, :rating, :runtime, :genre, :actors, :director, :writer, :plot, :poster)");
			$query->execute(array('imdbId' => $imdbId, 'title' => $title, 'year' => $year, 'rating' => $rating, 'runtime' => $runtime, 'genre' => $genre, 'actors' => $actors, 'director' => $director, 'writer' => $writer, 'plot' => $plot, 'poster' => $poster));
			return $conn->lastInsertId(); // Get the ID of the movie just added
		}
	}
	catch(PDOException $e)
	{
		exit("Database Error: " . $e->getMessage());
	}  
}

/*
	Add a movie to the specified user's shopping cart.
	Note: A specific movie can only be added to the user's cart once.
	
	Parameters:
		userId - The ID of the user.
		movieId - The ID of the movie (not the IMDB ID).
				
	Return:
		Nothing
 */
function addMovieToShoppingCart($userId, $movieId)
{
	try 
	{
		if(!movieExistsInCart($userId, $movieId)) // Movie is not already in the cart
		{
			if(movieExistsInCart($userId, $movieId, 0)) // Movie is in cart, but inactive (deleted)
			{
				activateMovieInCart($userId, $movieId);
			}
			else 
			{
				$conn = connect();
				$query = $conn->prepare("INSERT INTO Cart (UserID, MovieID) VALUES (:userid, :movieid)");
				$query->execute(array('userid' => $userId, 'movieid' => $movieId));
			}
		}
	}
	catch(PDOException $e)
	{
		exit("Database Error: " . $e->getMessage());
	}  
}

/*
	Create a new user account.
	Note: The new user account is disabled by default.
	
	Parameters:
		username - Username selected by the user.
		password - Password selected by the user.
		displayName - First and Last name of the user.
		email - Email address of the user.
		
	Return:
		>0 - The ID of the user account created.
		 0 - The specified username already exists.
 */
function addUser($username, $password, $displayName, $email)
{
	try 
	{
		if(uniqueUsername($username)) // Username does not already exist
		{
			$conn = connect();
			$query = $conn->prepare("INSERT INTO Users (Username, Password, DisplayName, Email) VALUES (:username, :password, :displayname, :email)");
			$query->execute(array('username' => $username, 'password' => $password, 'displayname' => $displayName, 'email' => $email));
			return $conn->lastInsertId(); // Get the ID of the user account created
		}
		else 
		{
			return 0; // Username already exists
		}
	}
	catch(PDOException $e)
	{
		exit("Database Error: " . $e->getMessage());
	}  
}

/*
	Get the number of movies in the specified user's shoppting cart.
	
	Parameters:
		userId - The ID of the user.
	
	Return:
		0 to n - The number movies in the user's shopping cart.
		False - The userId is invalid.
 */
function countMoviesInCart($userId)
{
	try 
	{
		$conn = connect();
		$query = $conn->prepare("SELECT COUNT(ID) FROM Movies, Cart WHERE ID = MovieId AND UserID = ? AND Active = 1");
		$query->execute([$userId]);
		return $query->fetchColumn();
	}
	catch(PDOException $e)
	{
		exit("Database Error: " . $e->getMessage());
	}
}

/*
	Retrieve the movies in shopping cart for the specified user in the specified order.
	
	Parameters:
		userId - The ID of the user of whose shopping cart to retrieve.
		order - The order to arrange the movies in the shopping cart.
			0 - By Title (default order)
			1 - By Runtime (shortest to longest)
			2 - By Runtime (longest to shortest)
			3 - By Year (oldest to newest)
			4 - By Year (newest to oldest)
	
	Return:
		Array containing the movie data for all the movies in the user's shopping cart.
 */
function getMoviesInCart($userId, $order = 0)
{
	switch($order)
	{
		case 0:
			return getMoviesInCartByTitle($userId);
			break;
		case 1:
			return getMoviesInCartByRuntime($userId, true);
			break;
		case 2:
			return getMoviesInCartByRuntime($userId, false);
			break;
		case 3:
			return getMoviesInCartByYear($userId, true);
			break;
		case 4:
			return getMoviesInCartByYear($userId, false);
			break;
	}
}

/*
	Retrieve information about the specified movie.
	
	Parameters:
		movieId - The ID of the movie to retrieve.
	
	Return:
		Array containing the movie's data (ID, IMDB_ID, Title, Year, Rating, Runtime, Genre, Actors, Director, Writer, Plot and Poster).
		NULL - The movieId does not exist in the database.
 */
function getMovieData($movieId)
{
	try 
	{
		$conn = connect();
		$query = $conn->prepare("SELECT * FROM Movies WHERE ID = ?");
		$query->execute([$movieId]);
		return $query->fetch() ?: null;
	}
	catch(PDOException $e)
	{
		exit("Database Error: " . $e->getMessage());
	}
}

/*
	Get a list of the movies in the specified user's shoppting cart (ordered by runtime).
	
	Parameters:
		userId - The ID of the user.
		ascending - The order in which to list movies.
			true: ascending order [A to Z]
			false: descending order [Z to A]
	
	Return:
		Array containing the movies in the shopping cart (order by runtime).
			Note: Only the following movie information is included (ID, IMDB_ID, Title, Year and Poster).
		NULL - No movies exist in the specified user's shopping cart.
 */
function getMoviesInCartByRuntime($userId, $ascending)
{
	try 
	{
		$conn = connect();
		$query = $conn->prepare("SELECT ID FROM Movies, Cart WHERE ID = MovieId AND UserID = ? AND Active = 1 ORDER BY Runtime " . ($ascending ? "ASC" : "DESC"));
		$query->execute([$userId]);
		return $query->fetchAll();
	}
	catch(PDOException $e)
	{
		exit("Database Error: " . $e->getMessage());
	}
}

/*
	Get a list of the movies in the specified user's shoppting cart (ordered by title).
	
	Parameters:
		userId - The ID of the user.
	
	Return:
		Array containing the movies in the shopping cart (order by title).
			Note: Only the following movie information is included (ID, IMDB_ID, Title, Year and Poster).
		NULL - No movies exist in the specified user's shopping cart.
 */
function getMoviesInCartByTitle($userId)
{
	try 
	{
		$conn = connect();
		$query = $conn->prepare("SELECT ID FROM Movies, Cart WHERE ID = MovieId AND UserID = ? AND Active = 1 ORDER BY Title");
		$query->execute([$userId]);
		return $query->fetchAll();
	}
	catch(PDOException $e)
	{
		exit("Database Error: " . $e->getMessage());
	}
}

/*
	Get a list of the movies in the specified user's shoppting cart (ordered by year).
	
	Parameters:
		userId - The ID of the user.
		ascending - The order in which to list movies.
			true: ascending order [A to Z]
			false: descending order [Z to A]
	
	Return:
		Array containing the movies in the shopping cart (order by year).
			Note: Only the following movie information is included (ID, IMDB_ID, Title, Year and Poster).
		NULL - No movies exist in the specified user's shopping cart.
 */
function getMoviesInCartByYear($userId, $ascending)
{
	try 
	{
		$conn = connect();
		$query = $conn->prepare("SELECT ID FROM Movies, Cart WHERE ID = MovieId AND UserID = ? AND Active = 1 ORDER BY Year " . ($ascending ? "ASC" : "DESC"));
		$query->execute([$userId]);
		return $query->fetchAll();
	}
	catch(PDOException $e)
	{
		exit("Database Error: " . $e->getMessage());
	}
}

/*
	Retrieve information about the specified user.
	
	Parameters:
		username - The username of the user to retrieve.
	
	Return:
		Array containing the user's data (ID, Display Name, and Email Address).
		NULL - The username does not exist in the database.
 */
function getUserData($username)
{
	try 
	{
		$conn = connect();
		$query = $conn->prepare("SELECT ID, DisplayName, Email FROM Users WHERE Username = ?");
		$query->execute([$username]);
		return $query->fetch() ?: null;
	}
	catch(PDOException $e)
	{
		exit("Database Error: " . $e->getMessage());
	}
}

/*
	Tests whether the specified movie already exists.
	
	Parameters:
		imdbId - The IMDB ID (tt0123456) to test.
	
	Return:
		>0 - The movie ID of the specified movie (no need to add it to the database).
		0 - The movie does not exist (it needs to be added to the database).
 */
function movieExistsInDB($imdbId)
{
	try 
	{
		$conn = connect();
		$query = $conn->prepare("SELECT ID FROM Movies WHERE IMDB_ID = ?");
		$query->execute([$imdbId]);
		return $query->fetchColumn() ?: 0;
	}
	catch(PDOException $e)
	{
		exit("Database Connection Failed: " . $e->getMessage());
	}
}

/*
	Remove a movie from the specified user's shopping cart.
	Note: A virtual delete is performed (Active is set to 0).
	
	Parameters:
		userId - The ID of the user.
		movieId - The ID of the movie (not the IMDB ID).
				
	Return:
		true - The specified movie was successfully removed (virtually) from the specified user's shopping cart.
		false - The specified movie does not exist in the specified user's shopping cart.
 */
function removeMovieFromShoppingCart($userId, $movieId)
{	
	try 
	{
		$conn = connect();
		$query = $conn->prepare("UPDATE Cart SET Active = 0 WHERE UserId = :userid AND MovieId = :movieid");
		$query->execute(array('userid' => $userId, 'movieid' => $movieId));
		return $query->rowCount() == 1 ? true : false;
	}
	catch(PDOException $e)
	{
		exit("Database Error: " . $e->getMessage());
	}
}

/*
	Reset the specified user's password.
	
	Parameters:
		userId - The user ID of the user.
		password - The user's new password.
				
	Return:
		true - The specified user's password was successfully changed.
		false - The specified user ID is invalid.
 */
function resetUserPassword($userId, $password)
{	
	try
	{
		$conn = connect();
		$query = $conn->prepare("UPDATE Users SET Password = :password WHERE ID = :userid and Active = 1");
		$query->execute(array('password' => $password, 'userid' => $userId));
		return $query->rowCount() == 1 ? true : false;
	}
	catch(PDOException $e)
	{
		exit("Database Error: " . $e->getMessage());
	}
}

/*
	Tests whether the specified username already exists.
	
	Parameters:
		username - The username to test.
	
	Return:
		true - The username does not exist (it can be added to the database).
		false - The username already exists (a new username must be selected).
 */
function uniqueUsername($username)
{
	try 
	{
		$conn = connect();
		$query = $conn->prepare("SELECT COUNT(*) as Count FROM Users WHERE Username = ?");
		$query->execute([$username]);
		return $query->fetchColumn() == 0 ? true : false;
	}
	catch(PDOException $e)
	{
		exit("Database Error: " . $e->getMessage());
	}
}

/*
	Tests user's credentials and returns user data.
	
	Parameters:
		username - The username to test.
		password - The password to test.
	
	Return:
		Array containing the user's ID, Display Name and Email Address.
		NULL - The credentials do not match.
		
 */
function validateUser($username, $password)
{
	try 
	{
		$conn = connect();
		$query = $conn->prepare("SELECT ID, DisplayName, Email FROM Users WHERE Username = :username AND Password = :password");
		$query->execute(array('username' => $username, 'password' => $password));
		return $query->fetch() ?: null;
	}
	catch(PDOException $e)
	{
		exit("Database Error: " . $e->getMessage());
	}
}

?>
