<html>
      <head>
            <title>Konfigurera klorofyllmätning</title>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
      </head>
      <body>
            <?php
                  $servername = "localhost";
                  $username = "irrigation_web";
                  $password = "";
                  $db = "irrigation";

                  // Create connection
                  $conn = new mysqli($servername, $username, $password, $db);

                  // Check connection
                  if ($conn->connect_errno) {
                        die();
                  }

                  $rs = $conn->query("SELECT * FROM settings");
                  $l = $rs->fetch_assoc();

                  $x = $l["imgcrop_x"];
                  $y = $l["imgcrop_y"];
                  $w = $l["imgcrop_w"];
                  $h = $l["imgcrop_h"];

                  if (isset($_GET["x"])) {
                        $x = $_GET["x"];
                        $y = $_GET["y"];
                        $w = $_GET["w"];
                        $h = $_GET["h"];

                        $stmt = $conn->prepare("UPDATE settings SET imgcrop_x = ?, imgcrop_y = ?, imgcrop_w = ?, imgcrop_h = ?");
                        $stmt->bind_param("dddd", $x, $y, $w, $h);
                        $stmt->execute();
                        $stmt->close();
                  }
                  $conn->close();
            ?>
            <form>
                  <table>
                        <tr><td>X</td><td><input class="boxprop" type="text" id="xval" name="x" value="<?php echo $x; ?>"></td></tr>
                        <tr><td>Y</td><td><input class="boxprop" type="text" id="yval" name="y" value="<?php echo $y; ?>"></td></tr>
                        <tr><td>Bredd</td><td><input class="boxprop" type="text" id="wval" name="w" value="<?php echo $w; ?>"></td></tr>
                        <tr><td>Höjd</td><td><input class="boxprop" type="text" id="hval" name="h" value="<?php echo $h; ?>"></td></tr>
                  </table>
                  <input type="submit" value="Spara">
            </form>
            <canvas id="selection" width="640" height="480">
            </canvas>
            <script>
                  function redraw(img) {
                        var c = $("#selection")[0];
                        var ctx = c.getContext("2d");
                        var x = parseFloat($("#xval").val())*c.offsetWidth;
                        var y = parseFloat($("#yval").val())*c.offsetHeight;
                        var w = parseFloat($("#wval").val())*c.offsetWidth;
                        var h = parseFloat($("#hval").val())*c.offsetHeight;
                        ctx.clearRect(0, 0, c.offsetWidth, c.offsetHeight);
                        ctx.beginPath();
                        ctx.drawImage(img,0,0,640,480);
                        ctx.rect(x,y,w,h);
                        ctx.stroke();
                  }
                  $(document).ready(function () {
                        var img = new Image();
                        img.onload = function () {
                              redraw(img);
                        };
                        img.src = "surveillance.php";
                        $(".boxprop").on("keyup", function () {
                              redraw(img);
                        });
                  });
            </script>
      </body>
</html>
