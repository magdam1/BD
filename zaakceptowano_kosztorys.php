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
		$EST_ID = $_POST['estimateID'];
		$ORDER_ID = $_POST['orderID'];
		$wynik = pg_query($link, "UPDATE estimates SET ARCHIVE = '1' WHERE ID = '$EST_ID';");
		$wynik = pg_query($link, "UPDATE orders SET STATUS = '4' WHERE ID = '$ORDER_ID';");			
?>
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset='utf-8'>
				<title>Panel pracownika - zaakceptowano kosztorys</title>
			</head>
			<body>
				<span style='color:green'><b>Dodano kosztorys nr <?php echo"$EST_ID"; ?> do akt.</b></span><br><br>
				_____________________<br><br>
				<a href='inspektor_kosztorysy_do_akceptacji.php'>Powrót do listy kosztorysów</a><br><br>
				<a href='inspektor_po_zalogowaniu.php'>Powrót do panelu inspektora</a><br><br>
				<a href='pracownik_wyloguj.php'>Wyloguj się</a><br><br>
				<a href='index.html'>Strona główna</a>
			</body>
		</html>
<?php

	}
}

pg_close($link);

?>