<?php
session_start();

if(isset($_SESSION['connect'])){
	header('Location: index.php');
	exit();
}

require('src/connection.php');

// LOGIN
if(!empty($_POST['email']) && !empty($_POST['password'])){

	// VARIABLES
	$email 		= $_POST['email'];
	$password 	= $_POST['password'];
	$error		= 1;

	// ENCRYPT THE PASSWORD
	$password = "aq1".sha1($password."1254")."25";

	echo $password;

	$req = $db->prepare('SELECT * FROM users WHERE email = ?');
	$req->execute(array($email));

	while($user = $req->fetch()){

		if($password == $user['password']){
			$error = 0;
			$_SESSION['connect'] = 1;
			$_SESSION['pseudo']	 = $user['pseudo'];

			if(isset($_POST['connect'])) {
				setcookie('log', $user['secret'], time() + 365*24*3600, '/', null, false, true);
			}

			header('Location: connection.php?success=1');
			exit();
		}

	}

	if($error == 1){
		header('Location: connection.php?error=1');
		exit();
	}

}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
</head>
<body>
	<header>
		<h1>Login</h1>
	</header>

	<div class="container">
		<p id="info">Welcome to my site. If you are not registered, <a href="index.php">sign up here.</a></p>
	 	
		<?php
			if(isset($_GET['error'])){
				echo '<p id="error">We cannot authenticate you.</p>';
			}
			else if(isset($_GET['success'])){
				echo '<p id="success">You are now logged in.</p>';
			}
		?>

	 	<div id="form">
			<form method="POST" action="connection.php">
				<table>
					<tr>
						<td>Email</td>
						<td><input type="email" name="email" placeholder="E.g.: example@google.com" required></td>
					</tr>
					<tr>
						<td>Password</td>
						<td><input type="password" name="password" placeholder="E.g.: ********" required ></td>
					</tr>
				</table>
				<p><label><input type="checkbox" name="connect" checked> Remember me</label></p>
				<div id="button">
					<button type='submit'>Login</button>
				</div>
			</form>
		</div>
	</div>
</body>
</html>
