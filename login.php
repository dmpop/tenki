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
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $title ?></title>
	<link rel="shortcut icon" href="favicon.png" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/uikit.min.css" />
	<script src="js/uikit.min.js"></script>
	<script src="js/uikit-icons.min.js"></script>
	<title><?php echo $title ?></title>
    </head>
    <body>
	<div class="uk-container uk-margin-small-top">
		<div class="uk-card uk-card-primary uk-card-body">
            <form method="POST">
                <label for='pagename'>Password: </label>
	        <input type="password" name="password">
	    </form>
		</div>
	</div>
    </body>
</html>
