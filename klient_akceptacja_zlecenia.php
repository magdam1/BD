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
		$ORDER_ID = $_POST['orderID'];
		$wynik = pg_query($link, "UPDATE orders SET STATUS = '6' WHERE ID = '$ORDER_ID';");	
?>
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset='utf-8'>
				<title>Panel klienta - szczegóły zamówienia nr <?php echo "$ORDER_ID"; ?></title>
			</head>
			<body>
				<span style='color:green'><b>Lista prac dla zamówienia nr <?php echo "$ORDER_ID"; ?> została zaakceptowana.</b></span><br><br>
				_____________________<br><br>
				<a href='klient_zamowienia.php'>Powrót do listy Twoich zamówień</a><br><br>
				<a href='klient_po_zalogowaniu.php'>Powrót do panelu klienta</a><br><br>
				<a href='klient_wyloguj.php'>Wyloguj się</a><br><br>
				<a href='index.html'>Strona główna</a>
			</body>
		</html>
<?php

	}
}

pg_close($link);

?>