<!DOCTYPE html>
<html>
      <head>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.min.js"></script>
	    <title>IrrigationPi</title>
      </head>
      <body>
                  <form method="get">
                        Volym
                        <input type="text" name="volume" value="0.4">
                        <input type="submit" value="Bevattna!">
                  </form>
                  <img id="surveillance" width="640" height="480"><br>
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
                  <?php
                        $servername = "localhost";
                        $username = "irrigation_web";
                        $password = "7083255q";
                        $db = "irrigation";

			$temperature = array();
			$light = array();
			$moisture = array();

                        // Create connection
                        $conn = new mysqli($servername, $username, $password, $db);

                        // Check connection
                        if ($conn->connect_errno) {
                              die("Connection failed: " . $conn->connect_error);
                        }

                        $rs = $conn->query("SELECT * FROM measurement");

                        if ($rs->num_rows > 0) {
			      echo "Mätningar<br><table><tr><td>Time</td><td>Temperature</td><td>Light</td><td>Moisture</td>";
                              while ($row = $rs->fetch_assoc()) {
                                    echo "<tr><td>" . $row["time"] . "</td><td>" . $row["temperature"] . "</td><td>" . $row["light"] . "</td><td>" . $row["moisture"] . "</td></tr>";
				    $temperature[] = floatval($row["temperature"]);
				    $light[] = floatval($row["light"]);
				    $moisture[] = floatval($row["moisture"]);
			      }
			      echo "</table>";
                        } else {
				echo $conn->error;
			}

                        $conn->close();
                  ?>
	  <canvas id="myChart" width="400" height="400"></canvas>
	  <script>
var ctx = document.getElementById("myChart").getContext('2d');
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        datasets: [{
            label: 'Temperature',
            data: [<?php echo implode(", ", $temperature); ?>],
            borderWidth: 1
        }, {
            label: 'Light',
            data: [<?php echo implode(", ", $light); ?>],
            borderWidth: 1
        }, {
            label: 'Moisture',
            data: [<?php echo implode(", ", $moisture); ?>],
            borderWidth: 1
        }] 
 

    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
</script>
      </body>
</html>
