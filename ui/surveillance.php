<?php
      exec("sudo -u pi /home/pi/surveillance.py");
      $name = "/home/pi/cam.jpg";
      $fp = fopen($name, "rb");
      header("Content-Type: image/jpg");
      header("Content-Length: " . filesize($name));

      fpassthru($fp);
      exit;
?>
