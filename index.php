<?php
include('config.php');
require_once('protect.php');
?>
<html lang="en" data-theme="<?php echo $theme ?>">
<!-- Author: Dmitri Popov, dmpop@linux.com
	 License: GPLv3 https://www.gnu.org/licenses/gpl-3.0.txt -->

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $title ?></title>
	<link rel="shortcut icon" href="favicon.png" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="classless.css" />
	<style>
		textarea {
			font-size: 15px;
			width: 100%;
			height: 15em;
			line-height: 1.9;
			margin-top: 1em;
			margin-bottom: 1em;
		}
	</style>
</head>

<body>
	<div class="text-center">
		<img style="display: inline; height: 2em; vertical-align: middle;" src="favicon.png" alt="logo" />
		<h1 class="text-center" style="display: inline; margin-left: 0.19em; vertical-align: middle; letter-spacing: 3px; margin-top: 0em;"><?php echo $title ?></h1>
		<hr style="margin-bottom: 2em;">
		<p id="geolocation"></p>
		<script>
			window.onload = getLocation();
			var x = document.getElementById("geolocation");

			function getLocation() {
				if (navigator.geolocation) {
					navigator.geolocation.getCurrentPosition(showPosition);
				} else {
					x.innerHTML = "Geolocation is not supported by this browser.";
				}
			}

			function showPosition(position) {
				x.innerHTML = "<div style='margin: 0 auto; border: 1px solid gray; border-radius: 5px; width: 17em'>Lat: " + position.coords.latitude +
					" Lon: " + position.coords.longitude + "</div>";
				document.cookie = "posLat = ; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
				document.cookie = "posLon = ; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
				document.cookie = "posLat = " + position.coords.latitude;
				document.cookie = "posLon = " + position.coords.longitude;
			}
		</script>
		<?php
		setcookie("posLat", "", time() - 3600);
		setcookie("posLon", "", time() - 3600);
		if (!file_exists("data")) {
			mkdir("data", 0777, true);
		}
		$date = date('Y-m-d');
		if (!empty($_POST["note"])) {
			$note = $_POST["note"];
		} else {
			$note = "";
		}
		if (isset($_POST['save'])) {
			if (!isset($_COOKIE['posLat']) || !isset($_COOKIE['posLon'])) {
				$f = fopen("data/" . $date . ".txt", "a");
				fwrite($f, $note . "\n");
				fclose($f);
			} else {
				$lat = $_COOKIE['posLat'];
				$lon = $_COOKIE['posLon'];
				$request = "https://api.openweathermap.org/data/2.5/weather?lat=" . $lat . "&lon=" . $lon . "&appid=" . $api_key . "&units=metric&cnt=7&lang=en&units=metric&cnt=7";
				$response = file_get_contents($request);
				$data = json_decode($response, true);
				$city = $data['name'];
				$country = $data['sys']['country'];
				$temp = $data['main']['temp'];
				$weather = $data['weather'][0]['description'];
				$wind = $data['wind']['speed'];
				$f = fopen("data/" . $date . ".txt", "a");
				fwrite($f, $city . " (" . $country . "), " . ucfirst(strtolower($weather)) . ", " . $temp . "°C, " . $wind . "m/s. " . $note . "\n");
				fclose($f);
			}
			echo "<script>";
			echo "window.location.replace('.');";
			echo "</script>";
		}
		?>
		<form method='POST' action=''>
			<label for='note'>Note:</label><br />
			<textarea name="note"></textarea><br />
			<button type='submit' name='save'>Save</button>
			<button type="submit" name="download">Download</button>
		</form>
		<?php
		$flist = array_reverse(glob('data/*.txt'));
		foreach (array_slice($flist, 0, $inum) as $f) {
			$fname = basename($f, ".txt");
			echo "<div class='text-left'>";
			echo "<h2>" . $fname . "</h2>";
			echo file_get_contents($f, true);
			echo "<br>";
			echo "</div>";
		}
		?>

		<?php
		if (isset($_POST["download"])) {
			$dir = 'data';
			$archive = time() . '-download.zip';
			$zip = new ZipArchive;
			$zip->open($archive, ZipArchive::CREATE);
			$files = scandir($dir);
			unset($files[0], $files[1]);
			foreach ($files as $file) {
				$zip->addFile($dir . '/' . $file);
			}
			$zip->close();
			header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
			header('Content-Type: application/zip');
			header("Content-Transfer-Encoding: Binary");
			header('Content-disposition: attachment; filename=' . $archive);
			header('Content-Length: ' . filesize($archive));
			while (ob_get_level()) {
				ob_end_clean();
			}
			readfile($archive);
			unlink($archive);
			ob_start();
		}
		?>

		<hr style="margin-top: 2em;">
		<p>This is <a href="https://github.com/dmpop/tenki">Tenki</a></p>
	</div>
</body>

</html>