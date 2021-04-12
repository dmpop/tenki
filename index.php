<?php
include('config.php');
require_once('protect.php');
?>
<html lang="en"  data-theme="<?php echo $theme ?>">
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
		<button onclick="getLocation()">Get coordinates</button>
		<p id="geolocation"></p>
		<script>
			var x = document.getElementById("geolocation");

			function getLocation() {
				if (navigator.geolocation) {
					navigator.geolocation.getCurrentPosition(showPosition);
				} else {
					x.innerHTML = "Geolocation is not supported by this browser.";
				}
			}

			function showPosition(position) {
				x.innerHTML = "Latitude: " + position.coords.latitude +
					"<br>Longitude: " + position.coords.longitude;
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
				$request = "https://api.weatherbit.io/v2.0/current?lat=" . $lat . "&lon=" . $lon . "&key=" . $weatherbit_api_key;
				$response = file_get_contents($request);
				$data = json_decode($response, true);
				$city = $data['data']['0']['city_name'];
				$country = $data['data']['0']['country_code'];
				$temp = $data['data']['0']['temp'];
				$weather = $data['data']['0']['weather']['description'];
				$wind = floor($data['data']['0']['wind_spd'] * 100) / 100;
				$f = fopen("data/" . $date . ".txt", "a");
				fwrite($f, $city . " (" . $country . ") " . $weather . ", " . $temp . "°C, " . $wind . "m/s. " . $note . "\n");
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