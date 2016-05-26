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
		$orders = pg_query($link, "SELECT ID, CLIENT_ID, ORDER_DATE FROM orders WHERE orders.status = '1' ORDER BY ID DESC;");
		$ile = pg_num_rows($orders);
?>
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset='utf-8'>
				<title>Panel pracownika - zamówienia oczekujące na wycenę</title>
			</head>
			<body>
			<?php
			if ($ile == 0) {
				echo "<b>Brak zamówień oczekujących na wycenę.</b><br><br>";
			}

			else {
			?>
				<b><u>Zamówienia oczekujące na wycenę</u></b><br><br>
				<table>
					<tr>
						<td><center><b>ID zamówienia</b></center></td>
						<td><center><b>ID klienta</b></center></td>
						<td><center><b>Data zamówienia</b></center></td>
						<td></td>
					</tr>
					<?php
						while ($order=pg_fetch_row($orders)) {
							echo "<tr>
									<td><center>$order[0]</center></td>
									<td><center>$order[1]</center></td>
									<td><center>$order[2] </center></td>
									<td><form action='zamowienie_szczegoly.php' method='POST'>
											<input type='hidden' name='orderID' value='$order[0]'>
											<input type='submit' value='Więcej'>
										</form>
									</td>
								</tr>";
						}
					?>
				</table><br><br>
			<?php 
			}
			?>
				_____________________<br><br>
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