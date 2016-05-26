<?php session_start();
require_once('db.php');

$link = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");

if (!isset($_COOKIE['Bruk&Trawnik_klient_login'])) {
	header('Location:klient_logowanie.php');
}

else {
	$username = $_COOKIE['Bruk&Trawnik_klient_login'];
	$wynik = pg_query($link, "SELECT PASSWORD FROM users WHERE USERNAME = '$username';");
	$password = pg_fetch_result($wynik, 0, 0);
	if ($_COOKIE['Bruk&Trawnik_klient_hasło'] != $password) {
		header('Location:klient_logowanie.php');
	}
	else {
		$wynik = pg_query($link, "SELECT ID FROM users WHERE USERNAME = '$username';");
		$ID = pg_fetch_result($wynik, 0, 0);
		$orders = pg_query($link, "SELECT orders.ID, DESCRIPTION, ORDER_DATE, statuses.NAME FROM orders JOIN statuses ON statuses.ID = orders.STATUS WHERE CLIENT_ID = '$ID' ORDER BY ID DESC;");
		$ile = pg_num_rows($orders);
?>
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset='utf-8'>
				<title>Panel klienta - Twoje zamówienia</title>
			</head>
			<body>
			<?php
			if ($ile == 0) {
				echo "<b>Brak zamówień.</b><br><br>";
			}

			else {
			?>
				<b><u>Twoje zamówienia</u></b><br><br>
				<table>
					<tr>
						<td><center><b>ID zamówienia</b></center></td>
						<td><center><b>Opis</b></center></td>
						<td><center><b>Data zamówienia</b></center></td>
						<td><center><b>Status</b></center></td>
						<td></td>
					</tr>
					<?php
						while ($order=pg_fetch_row($orders)) {
							$status = pg_query($link, "SELECT STATUS FROM orders WHERE ID = '$order[0]';");
							$status = pg_fetch_result($status, 0, 0);
							echo "<tr>
									<td><center>$order[0]</center></td>
									<td><center>$order[1]</center></td>
									<td><center>$order[2]</center></td>
									<td><center>$order[3]</center></td>
									<td><form action='klient_szczegoly.php' method='POST'>
											<input type='hidden' name='orderID' value='$order[0]'>
											<input type='hidden' name='status' value='$status'>
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
				<a href='klient_po_zalogowaniu.php'>Powrót do panelu klienta</a><br><br>
				<a href='klient_wyloguj.php'>Wyloguj się</a><br><br>
				<a href='index.html'>Strona główna</a>
			</body>
		</html>
<?php
	}
}

pg_close($link);

?>