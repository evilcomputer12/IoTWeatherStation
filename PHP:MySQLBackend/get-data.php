<?php
include '../private_dir/config.php';
$db_con = new Connection();
$api_key = $temp = $hum = $pm25 = $pm10 = $vbat = $vsol = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $api_key = test_input($_GET["api_key"]);
        if(strcmp(getKey(),$api_key)==0) {
        // Create connection
        $conn = new mysqli($db_con->getHost(), $db_con->getUsername(), $db_con->getPWD(), $db_con->getDBName());
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        $url = "http://worldtimeapi.org/api/timezone/Europe/Skopje";

        $curl = curl_init();
        $decodedData = "";
           
        // Sending GET request to reqres.in
        // server to get JSON data
        curl_setopt($curl, CURLOPT_URL,$url);
           
        // Telling curl to store JSON
        // data in a variable instead
        // of dumping on screen
        curl_setopt($curl,
            CURLOPT_RETURNTRANSFER, true);
           
        // Executing curl
        $response = curl_exec($curl);
         
        // Checking if any error occurs
        // during request or not
        if($e = curl_error($curl)) {
            $response = curl_exec($curl);
        } else {
             
            // Decoding JSON data
            $decodedData = json_decode($response, true);
                 
            // Outputting JSON data in
            // Decoded form
            // var_dump($decodedData);
        }
         
        // Closing curl
        curl_close($curl);
        
        //echo "My Light Live token: ". $json_data["utc_offset"];
        //$sql1 = "SELECT * FROM  CurrentSensorData WHERE CONVERT_TZ(created_at,'+00:00','+02:00')";
        //$sql1 = "SELECT * FROM  CurrentSensorData WHERE CONVERT_TZ(reading_time,'+00:00','".$json_data["utc_offset"]."')";
        //change to +01:00 when daylight
        if($decodedData == "" || !empty($decodedData)){
        if($decodedData["utc_offset"] == "+01:00") {
            $sql1 = "SELECT id,temp_val,hum_val,pm25_val,pm10_val,vbat_val,vsol_val,CONVERT_TZ(reading_time,'+00:00','+01:00') as reading_time from CurrentSensorData";
        }
        else {
            $sql1 = "SELECT id,temp_val,hum_val,pm25_val,pm10_val,vbat_val,vsol_val,CONVERT_TZ(reading_time,'+00:00','+02:00') as reading_time from CurrentSensorData";
        }
        $result=mysqli_query($conn,$sql1);

        $_ResultSet = array();
        while ($row = mysqli_fetch_assoc($result)) {
           $_ResultSet[] = $row;
        }
           header("Content-type: application/json; charset=utf-8");
           echo json_encode($_ResultSet); 
    
        $conn->close();
    }
    }
}
else {
    echo "Error";
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>