<?php
include '../private_dir/config.php';


// $api_key_value = getKey();

$db_con = new Connection();

$api_key = $temp = $hum = $pm25 = $pm10 = $vbat = $vsol = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $api_key = test_input($_POST["api_key"]);
    if(strcmp(getKey(),$api_key)==0) {
        $temp = test_input($_POST["temp"]);
        $hum = test_input($_POST["hum"]);
        $pm25 = test_input($_POST["pm25"]);
        $pm10 = test_input($_POST["pm10"]);
        $vbat = test_input($_POST["vbat"]);
        $vsol = test_input($_POST["vsol"]);
    
    // Create connection
        $conn = new mysqli($db_con->getHost(), $db_con->getUsername(), $db_con->getPWD(), $db_con->getDBName());
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        
         $sql = "INSERT INTO SensorData (temp_val, hum_val, pm25_val, pm10_val, vbat_val, vsol_val)
        VALUES ('" . $temp . "', '" . $hum . "', '" . $pm25 . "', '" . $pm10 . "', '" . $vbat . "' , '" . $vsol . "')";
        
        $sql1 = "UPDATE CurrentSensorData SET temp_val = '" . $temp . "', hum_val = '" . $hum . "', pm25_val = '" . $pm25 . "', pm10_val = '" . $pm10 . "' , vbat_val = '" . $vbat . "' , vsol_val = '" . $vsol . "' WHERE id=1";
        
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } 
        else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        
        if ($conn->query($sql1) === TRUE) {
            echo "New record created successfully";
        } 
        else {
            echo "Error: " . $sql1 . "<br>" . $conn->error;
        }
    
        $conn->close();
    }
    
    else {
        echo "Wrong API Key provided.";
    }

}
else {
    echo "No data posted with HTTP POST.";
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>