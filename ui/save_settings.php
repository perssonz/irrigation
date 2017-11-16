<?php
$servername = "localhost";
$username = "irrigation_web";
$password = "7083255q";
$db = "irrigation";

// Create connection
$conn = new mysqli($servername, $username, $password, $db);

// Check connection
if ($conn->connect_errno) {
	echo "Error saving.";
        die();
}

if (isset($_POST["volume"])) {
        $volume = $_POST["volume"];
        $trigger_m = $_POST["mthres"];
        $trigger_t = $_POST["tthres"];

        $stmt = $conn->prepare("UPDATE settings SET volume = ?, trigger_m = ?, trigger_t = ?");
        $stmt->bind_param("ddd", $volume, $trigger_m, $trigger_t);
        $stmt->execute();
        $stmt->close();
	echo "Settings saved.";
} else {
	echo "Error, parameters not set.";
}

$conn->close();
?>
