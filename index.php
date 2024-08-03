<?php
session_start();

require("src/connection.php");

if(!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_confirm'])){

	// VARIABLES

	$username       = $_POST['username'];
	$email          = $_POST['email'];
	$password       = $_POST['password'];
	$pass_confirm   = $_POST['password_confirm'];

	// CHECK IF PASSWORD = PASSWORD CONFIRM

	if($password != $pass_confirm){
			header('Location: index.php?error=1&pass=1');
				exit();

	}

	// CHECK IF EMAIL IS ALREADY IN USE
	$req = $db->prepare("SELECT count(*) as numberEmail FROM users WHERE email = ?");
	$req->execute(array($email));

	while($email_verification = $req->fetch()){
		if($email_verification['numberEmail'] != 0) {
			header('Location: index.php?error=1&email=1');
			exit();
		}
	}

	// HASH
	$secret = sha1($email).time();
	$secret = sha1($secret).time().time();

	// ENCRYPT THE PASSWORD
	$password = "aq1".sha1($password."1254")."25";

	// EXECUTE THE QUERY
	$req = $db->prepare("INSERT INTO users(username, email, password, secret) VALUES(?,?,?,?)");
	$req->execute(array($username, $email, $password, $secret));

	header('Location: index.php?success=1');
	exit();

}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>PHP and MySQL: The Ultimate Training</title>
	<link rel="icon" type="image/png" href="/logo.png">
	<link rel="stylesheet" type="text/css" href="design/default.css">
</head>

<body>
	<header>
		<h1>Sign Up</h1>
	</header>

	<div class="container">

		<?php
		if(!isset($_SESSION['connect'])){ ?>

		<p id="info">Welcome to my site. To explore more, please sign up. Otherwise, <a href="connection.php">Log in here.</a></p>

		<?php

			if(isset($_GET['error'])){

				if(isset($_GET['pass'])){
					echo '<p id="error">Passwords do not match.</p>';
				}
				else if(isset($_GET['email'])){
					echo '<p id="error">This email address is already in use.</p>';
				}
			}
			else if(isset($_GET['success'])){
				echo '<p id="success">Registration successfully completed.</p>';
			}

		?>

		<div id="form">
			<form method="POST" action="index.php">
				<table>
					<tr>
						<td>Username</td>
						<td><input type="text" name="username" placeholder="E.g.: Nicolas" required></td>
					</tr>
					<tr>
						<td>Email</td>
						<td><input type="email" name="email" placeholder="E.g.: example@google.com" required></td>
					</tr>
					<tr>
						<td>Password</td>
						<td><input type="password" name="password" placeholder="E.g.: ********" required></td>
					</tr>
					<tr>
						<td>Confirm Password</td>
						<td><input type="password" name="password_confirm" placeholder="E.g.: ********" required></td>
					</tr>
				</table>
				<div id="button">
					<button type='submit'>Sign Up</button>
				</div>
			</form>
		</div>

		<?php } else { ?>

		<p id="info">
			Hello <?= htmlspecialchars($_SESSION['pseudo']) ?><br>
			<a href="disconnection.php">Log Out</a>
		</p>

		<?php } ?>

	</div>
</body>
</html>
