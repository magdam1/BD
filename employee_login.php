<?php session_start();
require_once('db.php');

$link = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");

$ID = $_POST['ID'];
$password = $_POST['password'];
$password = sha1($password);

$wynik = pg_query($link, "SELECT PASSWORD FROM employees WHERE ID = '$ID';");

$ile = pg_numrows($wynik);

//zły ID
if ($ile == 0) {
	$_SESSION['login_exists'] = 'false';
	header('Location:pracownik_logowanie.php');
}

else {

	//logowanie udane
	if ($password == pg_fetch_result($wynik, 0, 0)) {
		$_SESSION['auth'] = '';
		$_SESSION['login_exists'] = '';

		$wynik = pg_query($link, "SELECT EMP_TYPE FROM employees WHERE ID = '$ID';");
		$type = pg_fetch_result($wynik, 0, 0);

		setcookie('Bruk&Trawnik_pracownik_login', $ID, time()+(3600*5));
		setcookie('Bruk&Trawnik_pracownik_hasło', $password, time()+(3600*5));
		setcookie('Bruk&Trawnik_pracownik_typ', $type, time()+(3600*5));

		if ($type == $appraiser) {
			header('Location:rzeczoznawca_po_zalogowaniu.php');
		}

		if ($type == $inspector) {
			header('Location:inspektor_po_zalogowaniu.php');
		}
	}

	//złe hasło
	else {
		$_SESSION['auth'] = 'false';
		header('Location:pracownik_logowanie.php');
	}

}

pg_close($link);

?>
