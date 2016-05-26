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
		$appraiser = $_POST['rzeczoznawca'];
		$name = pg_query($link, "SELECT NAME, SURNAME FROM employees WHERE ID = '$appraiser';");
		$name = pg_fetch_row($name);
		$wynik = pg_query($link, "UPDATE orders SET APPRAISER = '$appraiser' WHERE ID = '$ORDER_ID';");
		$wynik = pg_query($link, "UPDATE orders SET STATUS = '2' WHERE ID = '$ORDER_ID';");
?>
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset='utf-8'>
				<title>Panel pracownika - przydzielono rzeczoznawcę</title>
			</head>
			<body>
				<span style='color:green'><b>Przydzielono rzeczoznawcę <?php echo "$name[0] $name[1]"; ?> do wyceny zamówienia nr <?php echo "$ORDER_ID"; ?>.</b></span><br><br>
				_____________________<br><br>
				<a href='inspektor_zamowienia.php'>Powrót do listy zamówień</a><br><br>
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