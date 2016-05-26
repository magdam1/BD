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
		$descriptions = pg_query($link, "SELECT estimates.ID, orders.DESCRIPTION FROM estimates JOIN orders ON estimates.ORDER_ID = orders.ID WHERE estimates.ARCHIVE = '1' ORDER BY estimates.ID ASC;");
?>
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset='utf-8'>
				<title>Archiwum kosztorysów</title>
			</head>
			<body>
				<b><u>Kosztorysy archiwalne</u></b><br>
				__________________<br><br>
				<?php
					while ($desc = pg_fetch_row($descriptions)) {
						echo "<b>Kosztorys nr $desc[0]</b><br><br>";
						echo "$desc[1]<br>";
						$jobs = pg_query($link, "SELECT jobs.NAME, jobs.PRICE FROM jobs JOIN jobs_estimates ON jobs.ID = jobs_estimates.JOB_ID WHERE jobs_estimates.ESTIMATE_ID = '$desc[0]';");
						echo "<ul>";
						$sum = 0;
						while ($job = pg_fetch_row($jobs)) {
							echo "<li>$job[0], $job[1] zł</li>";
							$sum += $job[1];
						}
						echo "</ul>";
						echo "<b>Razem: $sum zł</b><br>";
						echo "__________________<br><br>";
					}
				?>
			</body>
		</html>
<?php
	}
}

pg_close($link);

?>