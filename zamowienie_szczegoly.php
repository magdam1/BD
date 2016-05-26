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
		$wynik = pg_query($link, "SELECT DESCRIPTION FROM orders WHERE ID = '$ORDER_ID';");
		$desc = pg_fetch_result($wynik, 0, 0);
		$appraisers = pg_query($link, "SELECT ID, NAME, SURNAME FROM employees WHERE EMP_TYPE = '$appraiser' ORDER BY ID ASC;");
		$estimates = pg_query($link, "SELECT ID FROM estimates WHERE ARCHIVE = '1' ORDER BY ID ASC;");
?>
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset='utf-8'>
				<title>Panel pracownika - szczegóły zamówienia nr <?php echo"$ORDER_ID" ?></title>
			</head>
			<body>
				<b><u>Szczegóły zamówienia nr <?php echo"$ORDER_ID" ?></u></b><br><br>
				<b>Opis:</b><br>
				<?php
						echo "$desc<br><br>";

				?>
				<b>Wycena:</b><br><br>
				Wybierz kosztorys z akt:<br><br>
				<form action='wybrano_kosztorys.php' method='POST'>
					<select name='kosztorysID' required>
						<option disabled selected></option>
						<?php
							while($estimate = pg_fetch_row($estimates)) {
								echo"<option value='$estimate[0]'>Kosztorys nr $estimate[0]</option>";
							}
						?>
					</select><br><br>
					<input type='hidden' name='orderID' value='<?php echo $ORDER_ID;?>'>
					<input type='submit' value='Zatwierdź'><br><br>
				</form>
				<a href="podglad_kosztorysow.php" target='_blank'>Podgląd archiwalnych kosztorysów</a><br><br><br> 
				Przydziel rzeczoznawcę, aby stworzył nowy kosztorys:<br><br>
				<form action='przydzielono_rzeczoznawce.php' method='POST'>
					<select name='rzeczoznawca' required>
						<option disabled selected></option>
						<?php
							while($appraiser = pg_fetch_row($appraisers)) {
								echo"<option value='$appraiser[0]'>$appraiser[1] $appraiser[2]</option>";
							}
						?>
					</select><br><br>
					<input type='hidden' name='orderID' value='<?php echo $ORDER_ID;?>'>
					<input type='submit' value='Zatwierdź'><br><br>
				</form>
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