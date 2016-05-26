<?php
require_once('db.php');

$link = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");

if (!isset($_COOKIE['Bruk&Trawnik_klient_login'])) {
	header('Location:klient_logowanie.php');
}

else {
	$username = $_COOKIE['Bruk&Trawnik_klient_login'];
	$wynik = pg_query($link, "SELECT PASSWORD FROM users WHERE USERNAME = '$username';");
	$password = pg_fetch_result($wynik, 0, 0);
	if ($_COOKIE['Bruk&Trawnik_klient_hasło'] != $password) {
		header('Location:klient_logowanie.php');
	}
	else {
		$wynik = pg_query($link, "SELECT NAME FROM users WHERE USERNAME = '$username';");
		$name = pg_fetch_result($wynik, 0, 0);
?>
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset='utf-8'>
				<title>Panel klienta</title>
			</head>
			<body>
				<b>Witaj, <?php echo $name; ?>!</b><br><br>
				<a href='klient_zamowienia.php'>Twoje zamówienia</a><br><br>
				<a href='klient_nowe_zamowienie.php'>Złóż nowe zamówienie</a><br><br>
				<a href='klient_wyloguj.php'>Wyloguj się</a><br><br>
				<a href='index.html'>Strona główna</a>
			</body>
		</html>
<?php
	}
}

pg_close($link);
?>