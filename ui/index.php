<!DOCTYPE html>
<html>
      <head>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.min.js"></script>
            <link rel="stylesheet" type="text/css" href="style.css">
            <title>IrrigationPi</title>
      </head>
      <body>
            <div id="result" class="results">
            </div>
            <div class="main">
                  <div class="control">
                        <h2>Styrning</h2>
                        <hr>
                        <h3>Manuell bevattning</h3>
                        <form id="irrigation"">
                              Volym
                              <input type="text" name="volume" value="0.3">
                              <input type="submit" value="Bevattna!">
                        </form>
                        <hr>
                        <h3>Automatik</h3>
                        <form id="automatic_settings">
                              <table>
                                    <tr><td>Volym</td><td><input type="text" name="volume" value="0.2"></td></tr>
                                    <tr><td>Fukttröskel</td><td><input type="text" name="mthres" value="3"></td></tr>
                                    <tr><td>Tidströskel</td><td><input type="text" name="tthres" value="48"></td></tr>
                              </table>
                              <input type="submit" value="Spara">
                        </form>
                        <hr>
                        <h3>Övriga inställningar</h3>
                        <a href="cphsettings.php">Ställ in klorofyllmätning</a>
                  </div>
                  <div class="measurements">
                        <?php
                        
                              $servername = "localhost";
                              $username = "irrigation_web";
                              $password = "";
                              $db = "irrigation";

                              $temperature = array();
                              $light = array();
                              $moisture = array();
                              $chlorophyll = array();
                              $time = array();

                              // Create connection
                              $conn = new mysqli($servername, $username, $password, $db);

                              // Check connection
                              if ($conn->connect_errno) {
                                    die("Connection failed: " . $conn->connect_error);
                              }

                              $rs = $conn->query("SELECT * FROM measurement ORDER BY id DESC");
                              $i = 0;
                              if ($rs->num_rows > 0) {
                                    while ($row = $rs->fetch_assoc()) {
                                          if ($i > 24*7)
                                                break;//Only take values one week back.
                                    
                                          $temperature[] = floatval($row["temperature"]);
                                          $light[] = floatval($row["light"]);
                                          $moisture[] = floatval($row["moisture"]);
                                          $time[] = "'" . $row["time"] . "'";
                                          $chlorophyll[] = floatval($row["chlorophyll"]);
                                          $i++;
                                    }
                              } else {
                                    echo $conn->error;
                              }
                              $temperature = array_reverse($temperature);
                              $light = array_reverse($light);
                              $moisture = array_reverse($moisture);
                              $time = array_reverse($time);
                              $chlorophyll = array_reverse($chlorophyll);

                              $conn->close();
                        ?>
                        <canvas id="myChart" width="400" height="400"></canvas>
                  </div>
            </div>
            <script>
                  $(document).ready(function () {
                        $("#result").hide();
                        $("#automatic_settings").submit(function (event) {
                        event.preventDefault();
                        $.ajax({
                                    type: "POST",
                                    url: "save_settings.php",
                                    data: $(this).serialize(),
                                    success: function(response) {
                                          light_message(response);
                                    },
                                    error: function(errResponse) {
                                          console.log(errResponse);
                                    }
                        });
                        return false;
                        });
                        $("#irrigation").submit(function (event) {
                        event.preventDefault();
                        $.ajax({
                                    type: "POST",
                                    url: "irrigate.php",
                                    data: $(this).serialize(),
                                    success: function(response) {
                                          light_message(response);
                                    },
                                    error: function(errResponse) {
                                          console.log(errResponse);
                                    }
                        });
                        return false;
                        });
                  });
                  
                  var ctx = document.getElementById("myChart").getContext('2d');
                  var myChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                              labels: [<?php echo implode(", ", $time); ?>],
                              datasets: [{
                                    label: 'Temperature',
                                    fill: false,
                                    backgroundColor: "red",
                                    borderColor: "red",
                                    data: [<?php echo implode(", ", $temperature); ?>],
                                    borderWidth: 1
                              }, {
                                    label: 'Light',
                                    fill: false,
                                    backgroundColor: "orange",
                                    borderColor: "orange",
                                    data: [<?php echo implode(", ", $light); ?>],
                                    borderWidth: 1
                              }, {
                                    label: 'Moisture',
                                    fill: false,
                                    backgroundColor: "blue",
                                    borderColor: "blue",
                                    data: [<?php echo implode(", ", $moisture); ?>],
                                    borderWidth: 1
                              }, {
                                    label: 'Chlorophyll',
                                    fill: false,
                                    backgroundColor: "green",
                                    borderColor: "green",
                                    data: [<?php echo implode(", ", $chlorophyll); ?>],
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
                  function light_message(str) {
                        $("#result").html(str);
                        $("#result").fadeIn();
                        window.setTimeout(function () {
                        $("#result").fadeOut();
                        },5000);
                  }
            </script>               
      </body>
</html>
