<?php
require_once('db.php');

$link = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");

if ((!isset($_COOKIE['Bruk&Trawnik_pracownik_login'])) or ($_COOKIE['Bruk&Trawnik_pracownik_typ'] != $inspector)) {
	header('Location:pracownik_logowanie.php');
}

else {
	$ID = $_COOKIE['Bruk&Trawnik_pracownik_login'];
	$wynik = pg_query($link, "SELECT PASSWORD FROM employees WHERE ID = '$ID';");
	$password = pg_fetch_result($wynik, 0, 0);
	if ($_COOKIE['Bruk&Trawnik_pracownik_hasło'] != $password) {
		header('Location:pracownik_logowanie.php');
	}

	else {
		$name = pg_query($link, "SELECT NAME, SURNAME FROM employees WHERE ID = '$ID'");
		$name = pg_fetch_row($name);
?>
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset='utf-8'>
				<title>Panel pracownika - inspektor ds. rzeczoznawców</title>
			</head>
			<body>
				<b>Zalogowan(a/y) jako:  <?php echo "$name[0] $name[1] ";?>/ inspektor ds. rzeczoznawców</b><br><br>
				<a href='inspektor_zamowienia.php'>Zamówienia oczekujące na wycenę</a><br><br>
				<a href='inspektor_kosztorysy_do_akceptacji.php'>Kosztorysy oczekujące na akceptację</a><br><br>
				<a href='pracownik_wyloguj.php'>Wyloguj się</a><br><br>
				<a href='index.html'>Strona główna</a>
			</body>
		</html>
<?php
	}
}

pg_close($link);
?>