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
		$estimates = pg_query($link, "SELECT estimates.ID, estimates.APPRAISER, orders.ID, ORDER_DATE FROM orders JOIN estimates ON orders.ID = estimates.ORDER_ID WHERE orders.status = '3' AND ARCHIVE = '0' ORDER BY estimates.ID ASC;");
		$ile = pg_num_rows($estimates);
?>
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset='utf-8'>
				<title>Panel pracownika - kosztorysy oczekujące na akceptację</title>
			</head>
			<body>
			<?php
			if ($ile == 0) {
				echo "<b>Brak kosztorysów oczekujących na akceptację.</b><br><br>";
			}

			else {
			?>
				<b><u>Kosztorysy oczekujące na akceptację</u></b><br><br>
				<table>
					<tr>
						<td><center><b>ID kosztorysu</b></center></td>
						<td><center><b>Rzeczoznawca</b></center></td>
						<td><center><b>ID zamówienia</b></center></td>
						<td><center><b>Data zamówienia</b></center></td>
						<td></td>
					</tr>
					<?php
						while ($estimate=pg_fetch_row($estimates)) {
							$appraiser = pg_query($link, "SELECT NAME, SURNAME FROM employees WHERE ID = '$estimate[1]';");
							$appraiser = pg_fetch_row($appraiser);
							echo "<tr>
									<td><center>$estimate[0]</center></td>
									<td><center>$appraiser[0] $appraiser[1]</center></td>
									<td><center>$estimate[2] </center></td>
									<td><center>$estimate[3] </center></td>
									<td><form action='kosztorys_szczegoly.php' method='POST'>
											<input type='hidden' name='orderID' value='$estimate[2]'>
											<input type='hidden' name='appID' value='$estimate[1]'>
											<input type='hidden' name='estimateID' value='$estimate[0]'>
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