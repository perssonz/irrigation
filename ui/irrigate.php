<?php
if (isset($_POST["volume"]) && is_numeric($_POST["volume"])) {
                                echo exec("sudo -u pi /home/pi/irrigation.py " . escapeshellarg($_POST["volume"]));
                        }

?>
