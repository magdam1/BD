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
		$wynik = pg_query($link, "SELECT DESCRIPTION FROM orders WHERE ID = '$ORDER_ID';");
		$desc = pg_fetch_result($wynik, 0, 0);
		$jobs = pg_query($link, "SELECT ID, NAME, PRICE FROM jobs;");
		$ile = pg_num_rows($jobs);
?>
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset='utf-8'>
				<title>Panel pracownika - szczegóły zamówienia nr <?php echo"$ORDER_ID"; ?></title>
			</head>
			<body>
				<b><u>Szczegóły zamówienia nr <?php echo"$ORDER_ID"; ?></u></b><br><br>
				<b>Opis:</b><br>

				<?php
					echo "$desc<br><br>";
				?>
						<b>Wybierz prace do kosztorysu:</b><br><br>
				<?php
					if ($ile != 0) {
				?>
						<form action='nowy_kosztorys.php' method='POST'>
							<?php
								while($job = pg_fetch_row($jobs)) {
									echo"<input type='checkbox' name='job[]' value='$job[0]'>$job[1], $job[2] zł<br><br>";
								}
							?>
							<input type='hidden' name='orderID' value='<?php echo $ORDER_ID;?>'>
							<input type='submit' value='Zatwierdź kosztorys'><br><br>
						</form>
				<?php
					}
					else {
						echo"<b>Brak prac w bazie.</b><br><br>";
					}
				?>
				<form action='nowa_praca.php' method='POST'>
					<input type='hidden' name='orderID' value='<?php echo "$ORDER_ID"; ?>'>
					<input type='submit' value='Dodaj nową pracę do bazy'><br><br>
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