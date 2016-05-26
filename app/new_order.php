<?php session_start();
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
		$desc = $_POST['description'];
		if ($desc == '') {
			$_SESSION['content'] = 'false';
			header('Location:klient_nowe_zamowienie.php');
		}

		else {
			$wynik = pg_query($link, "SELECT ID FROM users WHERE USERNAME = '$username';");
			$ID = pg_fetch_result($wynik, 0, 0);
			$wynik = pg_query($link, "INSERT INTO orders(CLIENT_ID, DESCRIPTION, STATUS, ORDER_DATE) VALUES ('$ID', '$desc', '1', CURRENT_DATE);");
			$_SESSION['content'] = '';
			$_SESSION['order_complete'] = 'true';
			header('Location:klient_nowe_zamowienie.php');
		}
	}
}

pg_close($link);

?>