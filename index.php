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
            Mätningar<br>
            <table>
                  <?php
                        $servername = "localhost";
                        $username = "irrigation_ui";
                        $password = "6h5g4l8)%f";
                        $db = "irrigation";
            
                        // Create connection
                        $conn = new mysqli($servername, $username, $password. $db);

                        // Check connection
                        if ($conn->connect_error) {
                              die("Connection failed: " . $conn->connect_error);
                        } 
                        
                        $rs = $conn->query("SELECT * FROM Measurement");
                        
                        if ($rs->num_rows > 0) {
                              while ($row = $rs->fetch_assoc()) {
                                    echo "<tr><td>" . $row["time"] . "</td><td>" . $row["temperature"] . "</td><td>" . $row["light"] . "</td><td>" . $row["moisture"] - "</td></tr>";
                              }
                        }
                        
                        $conn->close();
                  ?>
            </table>
      </body>
</html>