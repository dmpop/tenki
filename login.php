<?php
$config = include('config.php');

/* Redirects here after login */
$redirect_after_login = 'index.php';

/* Set timezone to UTC */

date_default_timezone_set('UTC');

/* Will not ask password again for */
$remember_password = strtotime('+30 days'); // 30 days

if (isset($_POST['password']) && $_POST['password'] == $passwd) {
	setcookie("password", $passwd, $remember_password);
	header('Location: ' . $redirect_after_login);
	exit;
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $theme ?>">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $title ?></title>
<link rel="shortcut icon" href="favicon.png" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="classless.css" />
<title><?php echo $title ?></title>
</head>

<body>

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $title ?></title>
		<link rel="shortcut icon" href="favicon.png" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="css/classless.css" />
		<link rel="stylesheet" href="css/themes.css" />
		<div class="card text-center" style="margin-top: 0em;">
			<img style="display: inline; height: 2em; vertical-align: middle;" src="favicon.png" alt="logo" />
			<h1 class="text-center" style="display: inline; margin-left: 0.19em; vertical-align: middle; letter-spacing: 3px; margin-top: 0em;"><?php echo $title ?></h1>
			<hr style="margin-bottom: 1em;">
			<form method="POST">
				<label for='pagename'>Password: </label>
				<input type="password" name="password">
				<button type="submit" name="login">Log in</button>
			</form>
		</div>
</body>

</html>