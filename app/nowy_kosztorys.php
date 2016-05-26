<?php session_start();
require_once('db.php');

$link = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");

if ((!isset($_COOKIE['Bruk&Trawnik_pracownik_login'])) or ($_COOKIE['Bruk&Trawnik_pracownik_typ'] != $appraiser)) {
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
		$ile=0;

		foreach ($_POST['job'] as $job) {
			$ile++;
		}

		if ($ile == 0) {
?>
			<!DOCTYPE html>
			<html>
				<head>
					<meta charset='utf-8'>
					<title>Panel pracownika - błąd przy dodawaniu nowego kosztorysu</title>
				</head>
				<body>
					<span style='color:red'><b>Wystąpił błąd - nie wybrano żadnej z prac do kosztorysu. Proszę spróbować ponownie.</b></span><br><br>

<?php
		}

		else {

			$ORDER_ID = $_POST['orderID'];
			$wynik = pg_query($link, "INSERT INTO estimates(ORDER_ID, APPRAISER) VALUES('$ORDER_ID', '$ID');");
			$ID_kosztorysu = pg_query($link, "SELECT ID FROM estimates WHERE ORDER_ID = '$ORDER_ID'");
			$ID_kosztorysu = pg_fetch_result($ID_kosztorysu, 0, 0);

			foreach ($_POST['job'] as $job) {
				$wynik = pg_query($link, "INSERT INTO jobs_estimates VALUES('$job', '$ID_kosztorysu');");
			}

			$wynik = pg_query($link, "UPDATE orders SET STATUS = '3' WHERE ID = '$ORDER_ID'");
			$wynik = pg_query($link, "UPDATE orders SET APPRAISER = '-1' WHERE ID = '$ORDER_ID'");
?>
			<!DOCTYPE html>
			<html>
				<head>
					<meta charset='utf-8'>
					<title>Panel pracownika - dodano nowy kosztorys</title>
				</head>
				<body>
					<span style='color:green'><b>Dodano nowy kosztorys do zamówienia nr <?php echo"$ORDER_ID" ?>.</b></span><br><br>
					
<?php
		}
?>
					_____________________<br><br>
					<a href='rzeczoznawca_zamowienia.php'>Powrót do listy zamówień</a><br><br>
					<a href='rzeczoznawca_po_zalogowaniu.php'>Powrót do panelu rzeczoznawcy</a><br><br>
					<a href='pracownik_wyloguj.php'>Wyloguj się</a><br><br>
					<a href='index.html'>Strona główna</a>
				</body>
			</html>
<?php

	}
}

pg_close($link);

?>