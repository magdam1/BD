<?php session_start();
require_once('db.php');

$link = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");

$username = $_POST['username'];
$password = $_POST['password'];
$password = sha1($password);

$wynik = pg_query($link, "SELECT PASSWORD FROM users WHERE USERNAME = '$username';");

$ile = pg_numrows($wynik);

//zła nazwa użytkownika
if ($ile == 0) {
	$_SESSION['login_exists'] = 'false';
	header('Location:klient_logowanie.php');
}

else {

	//logowanie udane
	if ($password == pg_fetch_result($wynik, 0, 0)) {
		$_SESSION['auth'] = '';
		$_SESSION['login_exists'] = '';
		setcookie('Bruk&Trawnik_klient_login', $username, time()+(3600*5));
		setcookie('Bruk&Trawnik_klient_hasło', $password, time()+(3600*5));
		header('Location:klient_po_zalogowaniu.php');
	}

	//złe hasło
	else {
		$_SESSION['auth'] = 'false';
		header('Location:klient_logowanie.php');
	}

}

pg_close($link);

?>