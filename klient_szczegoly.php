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
		$ORDER_ID = $_POST['orderID'];
		$ORDER_STATUS = $_POST['status'];

		$desc = pg_query($link, "SELECT DESCRIPTION FROM orders WHERE ID = '$ORDER_ID';");
		$desc = pg_fetch_result($desc, 0, 0);

		$date = pg_query($link, "SELECT ORDER_DATE FROM orders WHERE ID = '$ORDER_ID';");
		$date = pg_fetch_result($date, 0, 0);
?>
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset='utf-8'>
				<title>Panel klienta - szczegóły zamówienia nr <?php echo "$ORDER_ID"; ?></title>
			</head>
			<body>
			<b><u>Szczegóły zamówienia nr <?php echo "$ORDER_ID"; ?></u></b><br><br>
			<b>Data zamówienia:</b><br>
			<?php
				echo "$date<br><br>"; 
			?>
			<b>Opis zamówienia:</b><br>	
			<?php
				echo "$desc<br><br>";
			?>
			<b>Stan:</b><br>
			<?php
				switch ($ORDER_STATUS) {
					case 1:
						echo "Twoje zamówienie oczekuje na przyjęcie do realizacji.<br><br>";
					break;
					case 2: case 3:
						echo "Twoje zamówienie oczekuje na sporządzenie kosztorysu.<br><br>";
					break;
					case 4:
						echo "Na podstawie Twojego zamówienia został sporządzony kosztorys.<br>
							Poniżej możesz wybrać, które z proponowanych przez nas prac akceptujesz.<br>
							Prace, których nie zaznaczysz, zostaną usunięte z kosztorysu.<br><br>
							<span style='color:red'><b>UWAGA. Jeżeli nie zaznaczysz żadnej z prac, Twoje zamówienie zostanie anulowane.</b></span><br><br>";
						echo "<b>Kosztorys:</b><br><br>";

						$EST_ID = pg_query($link, "SELECT ID FROM estimates WHERE ORDER_ID = '$ORDER_ID';");
						$EST_ID = pg_fetch_result($EST_ID, 0, 0);

						$jobs = pg_query($link, "SELECT jobs.ID, jobs.NAME, jobs.PRICE FROM jobs JOIN jobs_estimates ON jobs_estimates.JOB_ID = jobs.ID WHERE jobs_estimates.ESTIMATE_ID = '$EST_ID';");

			?>		
					<form action='klient_akceptacja_prac.php' method='POST'>
						<?php
							$sum = 0;
							while($job = pg_fetch_row($jobs)) {
								echo"<input type='checkbox' name='job[]' value='$job[0]'>$job[1], $job[2] zł<br><br>";
								$sum += $job[2];
							}
							echo "<b>Razem: $sum zł</b><br><br>";
						?>
						<input type='hidden' name='orderID' value='<?php echo $ORDER_ID;?>'>
						<input type='hidden' name='estimateID' value='<?php echo $EST_ID;?>'>
						<input type='submit' value='Zatwierdź kosztorys'><br><br>
					</form>
			<?php
					break;
					case 5:
						echo "Poniżej możesz zapoznać się z listą zatwierdzonych przez Ciebie prac.<br>
							Gdy zaakceptujesz to zlecenie, zostanie ono przekazane do realizacji.<br><br>";

							echo "<b>Zlecenie:</b><br>";

							$EST_ID = pg_query($link, "SELECT ID FROM estimates WHERE ORDER_ID = '$ORDER_ID';");
							$EST_ID = pg_fetch_result($EST_ID, 0, 0);

							$jobs = pg_query($link, "SELECT jobs.ID, jobs.NAME, jobs.PRICE FROM jobs_estimates_used JOIN jobs ON jobs_estimates_used.JOB_ID = jobs.ID WHERE jobs_estimates_used.ESTIMATE_ID = '$EST_ID';");
							echo "<ul>";
							$sum = 0;
							while ($job = pg_fetch_row($jobs)) {
								echo "<li>$job[1], $job[2] zł</li>";
								$sum += $job[2];
							}
							echo "</ul>";
							echo "<b>Razem: $sum zł</b><br><br>";
			?>
							<form action='klient_akceptacja_zlecenia.php' method='POST'>
								<input type='hidden' name='orderID' value=<?php echo "'$ORDER_ID'"; ?>>
								<input type='submit' value='Zatwierdź zlecenie'><br><br>
							</form>
			<?php
					break;
					case 6:
						echo "Twoje zamówienie jest w fazie wykonawczej.<br>Zlecone przez Ciebie prace są w trakcie realizacji.<br><br>
						<b>Lista zleconych prac:</b><br>";
						$EST_ID = pg_query($link, "SELECT ID FROM estimates WHERE ORDER_ID = '$ORDER_ID';");
						$EST_ID = pg_fetch_result($EST_ID, 0, 0);

						$jobs = pg_query($link, "SELECT jobs.ID, jobs.NAME, jobs.PRICE FROM jobs_estimates_used JOIN jobs ON jobs_estimates_used.JOB_ID = jobs.ID WHERE jobs_estimates_used.ESTIMATE_ID = '$EST_ID';");
						echo "<ul>";
						$sum = 0;
						while ($job = pg_fetch_row($jobs)) {
							echo "<li>$job[1], $job[2] zł</li>";
							$sum += $job[2];
						}
						echo "</ul>";
						echo "<b>Razem: $sum zł</b><br><br>";
					break;
					case -1:
						echo "Twoje zamówienie zostało anulowane.<br><br>";

				}
			?>
				_____________________<br><br>
				<a href='klient_zamowienia.php'>Powrót do listy Twoich zamówień</a><br><br>
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