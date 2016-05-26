<!DOCTYPE html>

<?php session_start();
require_once('db.php');

$link = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");

$ID = $_COOKIE['Bruk&Trawnik_pracownik_login'];
$wynik = pg_query($link, "SELECT PASSWORD FROM employees WHERE ID = '$ID';");
$password = pg_fetch_result($wynik, 0, 0);

if (((isset($_COOKIE['Bruk&Trawnik_pracownik_login'])) and (isset($_COOKIE['Bruk&Trawnik_pracownik_typ'])))
	and ($_COOKIE['Bruk&Trawnik_pracownik_hasło'] == $password)) {

	if ($_COOKIE['Bruk&Trawnik_pracownik_typ'] == $inspector) {
		header('Location:inspektor_po_zalogowaniu.php');
	}

	if ($_COOKIE['Bruk&Trawnik_pracownik_typ'] == $appraiser) {
		header('Location:rzeczoznawca_po_zalogowaniu.php');
	}
}

else {
?>
	<html>
		<head>
			<meta charset='utf-8'>
			<title>Logowanie do panelu pracownika</title>
		</head>
		<body>
			<b>Logowanie do panelu pracownika</b><br><br>
			<form id='employee_log' action='employee_login.php' method='POST'>
				ID pracownika:<br>
				<input type='text' name='ID'><br><br>
				Hasło:<br>
				<input type='password' name='password'><br><br>
				<input type='submit' value="Zaloguj"><br><br>
			</form>
			<?php
				//błedny login
				if ($_SESSION['login_exists'] == 'false') {
					echo "<span style='color:red'><b>Błędny ID.</b></span><br><br>";
					$_SESSION['login_exists'] = '';
				}

				//błędne hasło
				if ($_SESSION['auth'] == 'false') {
					echo "<span style='color:red'><b>Błędne hasło.</b></span><br><br>";
					$_SESSION['auth'] = '';
				}
			?>
			<a href='index.html'>Strona główna</a>
		</body>
	</html>

<?php
}

pg_close($link);

?>