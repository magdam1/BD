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
		$EST_ID = $_POST['estimateID'];
		$appID = $_POST['appID'];
		$wynik = pg_query($link, "SELECT DESCRIPTION FROM orders WHERE ID = '$ORDER_ID';");
		$desc = pg_fetch_result($wynik, 0, 0);
		$appraiser = pg_query($link, "SELECT NAME, SURNAME FROM employees WHERE ID = '$appID';");
		$appraiser = pg_fetch_row($appraiser);
		$jobs = pg_query($link, "SELECT JOB_ID FROM jobs_estimates WHERE ESTIMATE_ID = '$EST_ID';");
?>
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset='utf-8'>
				<title>Panel pracownika - szczegóły kosztorysu nr <?php echo"$EST_ID"; ?></title>
			</head>
			<body>
				<b><u>Szczegóły kosztorysu nr <?php echo"$EST_ID"; ?></u></b><br><br>
				<b>Rzeczoznawca: </b><br><?php echo "$appraiser[0] $appraiser[1]";?><br><br>
				<b>Opis zamówienia:</b><br>
				<?php
						echo "$desc<br><br>";

				?>
				<b>Kosztorys:</b><br>
				<form action='zaakceptowano_kosztorys.php' method='POST'>
					<ul>
						<?php
							$sum = 0;
							while ($job = pg_fetch_row($jobs)) {
								$result = pg_query($link, "SELECT NAME, PRICE FROM jobs WHERE ID = '$job[0]';");
								$result = pg_fetch_row($result);
								$sum += $result[1];
								echo "<li>$result[0], $result[1] zł</li>";
							}
							echo "</ul><b>Razem: $sum zł</b><br><br>"
						?>
					<input type='hidden' name='estimateID' value='<?php echo $EST_ID;?>'>
					<input type='hidden' name='orderID' value='<?php echo $ORDER_ID;?>'>
					<input type='submit' value='Zatwierdź kosztorys'><br><br>
				</form>
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