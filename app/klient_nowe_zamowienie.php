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
		$wynik = pg_query($link, "SELECT NAME FROM users WHERE USERNAME = '$username';");
		$name = pg_fetch_result($wynik, 0, 0);
?>
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset='utf-8'>
				<title>Panel klienta - składanie nowego zamówienia</title>
			</head>
			<body>
			<?php
				if ($_SESSION['order_complete'] == 'true') {
					echo "<span style='color:green'><b>Twoje zamówienie zostało wysłane!</b></span><br><br>";
					$_SESSION['order_complete'] = '';
				}
				else {
			?>
				<b><u>Złóż nowe zamówienie</u></b><br><br>
				<form name='nowe_zamowienie' action='new_order.php' method='POST'>
					<textarea name='description' placeholder='Tu wprowadź opis zamówienia...' style='width:300px; height:200px;' required></textarea><br><br>
					<input type='submit' value="Wyślij"><br><br>
				</form>
				<?php
					if ($_SESSION['content'] == 'false') {
						echo "<span style='color:red'><b>Wprowadź treść zamówienia.</b></span><br><br>";
						$_SESSION['content'] = '';
					} 
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