<table align="center" class="table table-bordered table-condensed table-sm w-auto text-xsmall">
	<thead>
	<th colspan="8"><h2>History of measured sensor data</h2></th>
                                <tr>
                                    <th> ID </th> 
                        			<th> Temperature </th> 
                        			<th> Humidity </th>
                        			<th> PM2.5 </th>
                        			<th> PM10 </th>
                        			<th> Bat Voltage</th>
                        			<th> Sol Voltage</th>
                        			<th> Date/time of record</th>
                                </tr>
                            </thead>
                            <tbody>
                            
                            <?php
                                include '../private_dir/config.php';
                                $db_con = new Connection();
                                $conn = new mysqli($db_con->getHost(), $db_con->getUsername(), $db_con->getPWD(), $db_con->getDBName());

                                if(isset($_POST['from_date']) && isset($_POST['to_date']))
                                {
                                    $from_date = $_POST['from_date'];
                                    $to_date = $_POST['to_date'];
                                    // $query = "SELECT * FROM SensorData WHERE reading_time BETWEEN  "."'$from_date'"."  AND  "."'$to_date'";
                                    
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
                                        $sql1 = "SELECT id,temp_val,hum_val,pm25_val,pm10_val,vbat_val,vsol_val,CONVERT_TZ(reading_time,'+00:00','+01:00') as reading_time from SensorData WHERE reading_time BETWEEN  "."'$from_date'"."  AND  "."'$to_date'";
                                    }
                                    else {
                                        $sql1 = "SELECT id,temp_val,hum_val,pm25_val,pm10_val,vbat_val,vsol_val,CONVERT_TZ(reading_time,'+00:00','+02:00') as reading_time from SensorData WHERE reading_time BETWEEN  "."'$from_date'"."  AND  "."'$to_date'";
                                    }
                                    $query_run=mysqli_query($conn,$sql1);
                                    }
                                    

                                    if(mysqli_num_rows($query_run) > 0)
                                    {
                                        foreach($query_run as $row)
                                        {
                                            ?>
                                            <tr>
                                                
                                                <td><?= $row["id"] ; ?></td>  
                                                <td><?= $row["temp_val"]; ?></td>  
                                                <td><?= $row["hum_val"]; ?></td>  
                                                <td><?= $row["pm25_val"]; ?></td>  
                                                <td><?= $row["pm10_val"]; ?></td>
                                                <td><?= $row["vbat_val"]; ?></td>  
                                                <td><?= $row["vsol_val"]; ?></td> 
                                                <td><?= $row["reading_time"]; ?></td> 
                                            </tr>
                                            <?php
                                        }
                                    }
                                    else
                                    {
                                        echo "No Record Found";
                                    }
                                }
                            ?>
                            </tbody>
                        </table>