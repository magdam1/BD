<?php session_start();
require_once('db.php');

$link = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");

$username = $_POST['username'];
$users = pg_query($link, "SELECT * FROM users WHERE USERNAME='$username';");

//nazwa użytkownika zajęta
if (pg_numrows($users) != 0) {
	$_SESSION['username_free'] = 'false';
	header('Location:klient_rejestracja.php');
}

else {
	$password = $_POST['password'];
	$password2 = $_POST['password_repeat'];

	//hasła niezgodne ze sobą
	if ($password != $password2) {
		$_SESSION['password_correct'] = 'false';
		header('Location:klient_rejestracja.php');
	}

	else {
		$password = sha1($password);

		$name = $_POST['name'];
		$surname = $_POST['surname'];
		$email = $_POST['email'];
		$phone = $_POST['phone'];

		//wstawianie nowego użytkownika do bazy
		$wynik = pg_query($link, "INSERT INTO users(USERNAME, PASSWORD, NAME, SURNAME, EMAIL, PHONE) VALUES ('$username', '$password', '$name', '$surname', '$email', '$phone');");

		header('Location:po_rejestracji.html');
	}
}

pg_close($link);

?>