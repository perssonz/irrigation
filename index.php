<!DOCTYPE html>
<html>
        <head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
                <title>IrrigationPi</title>
        </head>
        <body>
                <form method="get">
                        Volym
                        <input type="text" name="volume" value="0.4">
                        <input type="submit" value="Bevattna!">
                </form>
		<img id="surveillance" width="640" height="480">
                <?php
                        if (isset($_GET["volume"]) && is_numeric($_GET["volume"])) {
				echo exec("sudo -u pi /home/pi/irrigation.py " . escapeshellarg($_GET["volume"]));
                        }
                ?>
	<script>
		$(document).ready(function() {
			getSurveillance()
			setInterval(function() {
				getSurveillance();
			}, 5000);
		});
		function getSurveillance() {
			console.log("Fetching new image");
			d = new Date();
			$("#surveillance").attr("src", "surveillance.php?"+d.getTime());
		}
	</script>
        </body>
</html>

