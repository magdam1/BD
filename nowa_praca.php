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
		$ORDER_ID = $_POST['orderID'];
		$OLD_ORDER_ID = $_SESSION['order_id'];
		$_SESSION['order_id'] = $ORDER_ID;
?>
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset='utf-8'>
				<title>Panel pracownika - dodawanie nowej pracy</title>
			</head>
			<body>
				<?php
					if ($_SESSION['job_added'] != true) {
				?>
						<b>Dodaj nową pracę do bazy</b><br><br>
						<form action='new_job.php' method='POST'>
							Nazwa:<br>
							<input type='text' name='job_name' required><br><br>
							Cena:<br>
							<input type='number' name='price' required><br><br>
							<input type='submit' value='Zatwierdź'><br><br>
						</form>
				<?php
					}
					else {
						echo "<span style='color:green'><b>Dodano nową pracę do bazy.</b></span><br><br>";
						$_SESSION['job_added'] = '';
						$ORDER_ID = $OLD_ORDER_ID;
					}
				?>
				<form action='zamowienie_szczegoly_rzeczoznawca.php' method='POST'>
					<input type='hidden' name='orderID' value='<?php echo "$ORDER_ID"; ?>'>
					<input type='submit' value='Wróć'><br><br>
				</form>
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