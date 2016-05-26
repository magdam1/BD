<!DOCTYPE html>

<?php session_start();
?>

<html>
	<head>
		<meta charset='utf-8'>
		<title>Rejestracja klienta</title>
	</head>
	<body>
		<b>Rejestracja nowego klienta</b><br><br>
		<form id='client_register' action='client_register.php' method='POST'>
			Nazwa użytkownika:<br>
			<input type='text' name='username' required><br><br>
			Imię:<br>
			<input type='text' name='name' required><br><br>
			Nazwisko:<br>
			<input type='text' name='surname' required><br><br>
			Hasło:<br>
			<input type='password' name='password' required><br><br>
			Powtórz hasło:<br>
			<input type='password' name='password_repeat' required><br><br>
			E-mail:<br>
			<input type='email' name='email'><br><br>
			Numer telefonu:<br>
			<input type='text' name='phone'><br><br>
			<input type='submit' value="Zarejestruj"><br><br>
		</form>
		<?php
			//nazwa użytkownika zajęta
			if ($_SESSION['username_free'] == 'false') {
				echo "<span style='color:red'><b>Podana nazwa użytkownika jest zajęta.</b></span><br><br>";
				$_SESSION['username_free'] = '';
			}

			//hasła różnią się
			if ($_SESSION['password_correct'] == 'false') {
				echo "<span style='color:red'><b>Podane hasła różnią się od siebie.</b></span><br><br>";
				$_SESSION['password_correct'] = '';
			}

		?>
			_____________________<br><br>
			<a href='klient_logowanie.php'>Powrót</a><br><br>
			<a href='index.html'>Strona główna</a>
	</body>
</html>