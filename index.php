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
	<link rel="stylesheet" href="css/classless.css" />
	<link rel="stylesheet" href="css/themes.css" />
	<link rel="stylesheet" href="css/tabbox.css" />
	<style>
		textarea {
			font-size: 15px;
			width: 100%;
			height: 9em;
			line-height: 1.9;
			margin-top: 1em;
			margin-bottom: 1em;
		}
	</style>
</head>

<body>
	<!-- Suppress form re-submit prompt on refresh -->
	<script>
		if (window.history.replaceState) {
			window.history.replaceState(null, null, window.location.href);
		}
	</script>
	<div class="text-center" style="margin-bottom: 1.5em;">
		<img style="display: inline; height: 2em; vertical-align: middle;" src="favicon.png" alt="logo" />
		<h1 class="text-center" style="display: inline; margin-left: 0.19em; vertical-align: middle; letter-spacing: 3px; margin-top: 0em;"><?php echo $title ?></h1>
	</div>
	<div class="tabs">
		<input type="radio" name="tabs" id="tabone" checked="checked">
		<label for="tabone">🌤️ Record</label>
		<div class="tab">
			<div class="text-center">
				<p id="geolocation"></p>
				<script>
					window.onload = getLocation();
					var x = document.getElementById("geolocation");

					function getLocation() {
						if (navigator.geolocation) {
							navigator.geolocation.getCurrentPosition(showPosition);
						} else {
							x.innerHTML = "Geolocation is not supported.";
						}
					}

					function showPosition(position) {
						x.innerHTML = "<div style='margin: 0 auto; margin-top: 2em; margin-bottom: 1em;'><a href='geo:" + position.coords.latitude + "," + position.coords.longitude + "'>" + position.coords.latitude + ", " + position.coords.longitude + "</a></div>";
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

				$timestamp = time();
				$dt = new DateTime("now", new DateTimeZone($tz));
				$dt->setTimestamp($timestamp);

				$wpt_file = "data/waypoints.csv";

				if (!is_file($wpt_file)) {
					$contents = "date,time,lat,lon,desc\n";
					file_put_contents($wpt_file, $contents);
				}

				$date = date('Y-m-d');
				if (!empty($_POST["note"])) {
					$note = $_POST["note"];
				} else {
					$note = "";
				}

				if (isset($_POST['save'])) {
					if (isset($_POST['waypoint'])) {
						$lat = $_COOKIE['posLat'];
						$lon = $_COOKIE['posLon'];
						$f = fopen($wpt_file, "a");
						fwrite($f, $dt->format('Y/m/d,H:i:s') . "," . $lat . "," . $lon . "," . $note . "\n");
						fclose($f);
						echo "<script>";
						echo 'alert("Coordinates have been saved.")';
						echo "</script>";
					} else {
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
				}
				?>
				<form method='POST' action=''>
					Note (or waypoint description):<br />
					<textarea name="note"></textarea><br />
					<div style="margin-bottom: 1em;"><input type="checkbox" name="waypoint" value="wpt"> Save waypoint</div>
					<button type='submit' name='save'>Save</button>
					<button onClick="window.location.reload();">Refresh</button>
					<button type="submit" name="download">Download</button>
				</form>
			</div>
		</div>
		<input type="radio" name="tabs" id="tabtwo">
		<label for="tabtwo">🧭 Previous <?php echo $inum ?> days</label>
		<div class="tab">
			<?php
			$flist = array_reverse(glob('data/*.txt'));
			foreach (array_slice($flist, 0, $inum) as $f) {
				$fname = basename($f, ".txt");
				echo "<div class='text-left'>";
				echo "<h4>" . $fname . "</h4>";
				echo file_get_contents($f, true);
				echo "<br>";
				echo "</div>";
			}
			?>
		</div>

		<input type="radio" name="tabs" id="tabthree">
		<label for="tabthree">📍 Waypoints</label>
		<div class="tab">
			<table>
				<?php
				$row = 1;
				if (($handle = fopen($wpt_file, "r")) !== FALSE) {
					while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
						if ($row == 1) {
							echo '<thead><tr>';
						} else {
							echo '<tr>';
						}
						$value0 = $data[0];
						$value1 = $data[1];
						$value2 = $data[2];
						$value3 = $data[3];
						$value4 = $data[4];
						if ($row == 1) {
							echo '<th class="text-left">Date</th>';
							echo '<th class="text-left">Description</th>';
							echo '<th class="text-left">Map</th>';
						} else {
							echo '<td class="text-left">' . $value0 . '</td>';
							echo '<td class="text-left">' . $value4 . '</td>';
							echo '<td class="text-left"><a href="geo:' . $value2 . ',' . $value3 . '">Open</a></td>';
						}
						if ($row == 1) {
							echo '</tr></thead><tbody>';
						} else {
							echo '</tr>';
						}
						$row++;
					}
					fclose($handle);
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
	</div>

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

	<div class="text-center">
		<p style="margin-right: 0.5em;"><?php echo $footer; ?>
</body>

</html>