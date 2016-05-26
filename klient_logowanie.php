<?php session_start();
require_once('db.php');

$link = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");

$username = $_COOKIE['Bruk&Trawnik_klient_login'];
$wynik = pg_query($link, "SELECT PASSWORD FROM users WHERE USERNAME = '$username';");
$password = pg_fetch_result($wynik, 0, 0);

if ((isset($_COOKIE['Bruk&Trawnik_klient_login'])) and ($_COOKIE['Bruk&Trawnik_klient_hasło'] == $password)) {
	header('Location:klient_po_zalogowaniu.php');
}	
else {
?>
	<!DOCTYPE html>
	<html>
		<head>
			<meta charset='utf-8'>
			<title>Logowanie do panelu klienta</title>
		</head>
		<body>
			<b>Logowanie do panelu klienta</b><br><br>
			<!-- formularz logowania -->
			<form id='client_log' action='client_login.php' method='POST'>
				Login:<br>
				<input type='text' name='username'><br><br>
				Hasło:<br>
				<input type='password' name='password'><br><br>
				<input type='submit' value="Zaloguj"><br><br>
			</form>
			<?php
				//błedny login
				if ($_SESSION['login_exists'] == 'false') {
					echo "<span style='color:red'><b>Błędna nazwa użytkownika.</b></span><br><br>";
					$_SESSION['login_exists'] = '';
				}

				//błędne hasło
				if ($_SESSION['auth'] == 'false') {
					echo "<span style='color:red'><b>Błędne hasło.</b></span><br><br>";
					$_SESSION['auth'] = '';
				}
			?>
			<!-- link do rejestracji -->
			Nie posiadasz konta? <a href='klient_rejestracja.php'>Zarejestruj się.</a><br><br>
			<a href='index.html'>Strona główna</a>
		</body>
	</html>

	<?php
}
pg_close($link);
?>