<?php
include('config.php');
require_once('protect.php');
?>
<html lang="en">
<!-- Author: Dmitri Popov, dmpop@linux.com
	 License: GPLv3 https://www.gnu.org/licenses/gpl-3.0.txt -->

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $title ?></title>
	<link rel="shortcut icon" href="favicon.png" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/uikit.min.css" />
	<script src="js/uikit.min.js"></script>
	<script src="js/uikit-icons.min.js"></script>
</head>

<body>
	<div class="uk-container uk-margin-small-top">
		<div class="uk-card uk-card-primary uk-card-body">
			<h1><?php echo $title ?></h1>
			<hr>
			<button class="uk-button uk-button-default uk-margin-top" onclick="getLocation()">Get coordinates</button>
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
					echo "Temperature: " . $temp . "°C<br>";
					echo "Current conditions: " . $weather . "<br>";
					echo "Wind speed: " . $wind . "m/s<br>";
					$f = fopen("data/" . $date . ".txt", "a");
					fwrite($f, $city . " (" . $country . ") " . $weather . ", " . $temp . "°C, " . $wind . "m/s. " . $note . "\n");
					fclose($f);
				}
				echo "<script>";
				echo "UIkit.notification({message: 'Note saved.'});";
				echo "</script";
			}
			?>
			<form method='post' action=''>
				<label for='note'>Note:</label>
				<textarea class="uk-textarea" name="note"></textarea>
				<button class="uk-button uk-button-default uk-margin-top" type='submit' role='button' name='save'>Save</button>
				<button class="uk-button uk-button-primary uk-margin-top" type='submit' role='button' name='view'>View</button>
			</form>
		</div>
		<?php
		if (isset($_POST['view'])) {
			$flist = array_reverse(glob('data/*.txt'));
			foreach (array_slice($flist, 0, $inum) as $f) {
				$fname = basename($f, ".txt");
				echo "<div class='uk-container uk-margin-small-top'>";
				echo "<div class='uk-card uk-card-default uk-card-body'>";
				echo "<h2>" . $fname . "</h2>";
				echo file_get_contents($f, true);
				echo "<br>";
				echo "</div>";
			}
		}
		?>
	</div>
</body>

</html>