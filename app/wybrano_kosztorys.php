<?php session_start();
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
		$ORDER_ID = $_POST['orderID'];
		$EST_ID = $_POST['kosztorysID'];
		$app = pg_query($link, "SELECT APPRAISER FROM estimates WHERE ID = '$EST_ID';");
		$app = pg_fetch_result($app, 0, 0);

		$wynik = pg_query($link, "INSERT INTO estimates(ORDER_ID, APPRAISER, ARCHIVE) VALUES('$ORDER_ID', '$app', '1');");

		$ID_kosztorysu = pg_query($link, "SELECT ID FROM estimates WHERE ORDER_ID = '$ORDER_ID';");
		$ID_kosztorysu = pg_fetch_result($ID_kosztorysu, 0, 0);

		$jobs = pg_query($link, "SELECT JOB_ID FROM jobs_estimates WHERE ESTIMATE_ID = '$EST_ID';");

		while ($job = pg_fetch_row($jobs)) {
			$wynik = pg_query($link, "INSERT INTO jobs_estimates(JOB_ID, ESTIMATE_ID) VALUES('$job[0]', '$ID_kosztorysu');");
		}

		$wynik = pg_query($link, "UPDATE orders SET STATUS = '4' WHERE ID = '$ORDER_ID';");
?>
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset='utf-8'>
				<title>Panel pracownika - przydzielono kosztorys archiwalny</title>
			</head>
			<body>
				<span style='color:green'><b>Przydzielono archiwalny kosztorys nr <?php echo "$EST_ID"; ?> do zamówienia nr <?php echo "$ORDER_ID"; ?>.</b></span><br><br>
				
<?php
	}
?>
				_____________________<br><br>
				<a href='inspektor_zamowienia.php'>Powrót do listy zamówień</a><br><br>
				<a href='inspektor_po_zalogowaniu.php'>Powrót do panelu rzeczoznawcy</a><br><br>
				<a href='pracownik_wyloguj.php'>Wyloguj się</a><br><br>
				<a href='index.html'>Strona główna</a>
			</body>
		</html>
<?php

	}
//}

pg_close($link);

?>