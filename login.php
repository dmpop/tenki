<?php
$config = include('config.php');
$pw_hash = password_hash($password, PASSWORD_DEFAULT);

if ($protect) {
	session_start();
}

if (isset($_POST['password']) && password_verify($_POST['password'], $pw_hash)) {
	$_SESSION["password"] = $pw_hash;
	if (file_exists(".page")) {
		$page = file_get_contents('.page');
		$url = "index.php?page=" . $page;
	} else {
		$url = "index.php";
	}
	header('Location: ' . $url);
	exit;
}
?>

<html lang="en" data-theme="<?php echo $theme ?>">

<head>
	<title><?php echo $title; ?></title>
	<meta charset="utf-8">
	<link rel="shortcut icon" href="favicon.png" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/classless.css" />
	<link rel="stylesheet" href="css/themes.css" />
	<link rel="stylesheet" href="css/tabbox.css" />
</head>

<body>
	<div style="text-align: center;">
		<img style="display: inline; height: 2em; border-radius: 0; vertical-align: middle;" src="favicon.svg" alt="logo" />
		<h1 style="display: inline; margin-left: 0.19em; vertical-align: middle; letter-spacing: 3px; margin-top: 0em;"><?php echo $title ?></h1>
		<hr style="margin-bottom: 2em; margin-top: 1em;">
		<form method="POST">
			<label>Password:
				<input style="margin-top: 1em; display: inline;" type="password" name="password">
				<button type="submit" name="go">Go</button>
			</label>
		</form>
		<hr style="margin-top: 2em; margin-bottom: 1.5em;">
		<?php echo $footer; ?>
	</div>
</body>

</html>