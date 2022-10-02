<?php
include '../private_dir/config.php';
$db_con = new Connection();
$api_key = $temp = $hum = $pm25 = $pm10 = $vbat = $vsol = "";

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
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
            $sql1 = "SELECT id,temp_val,hum_val,pm25_val,pm10_val,vbat_val,vsol_val,CONVERT_TZ(reading_time,'+00:00','+01:00') as reading_time from SensorData ORDER BY reading_time DESC";
        }
        else {
            $sql1 = "SELECT id,temp_val,hum_val,pm25_val,pm10_val,vbat_val,vsol_val,CONVERT_TZ(reading_time,'+00:00','+02:00') as reading_time from SensorData ORDER BY reading_time DESC";
        }
        $result=mysqli_query($conn,$sql1);
        }
        }
        ?>
        
        
        <!DOCTYPE html> 
<html> 
	<head> 
		<title>sensor data</title>
		<meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>  
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.css" integrity="sha512-bYPO5jmStZ9WI2602V2zaivdAnbAhtfzmxnEGh9RwtlI00I9s8ulGe4oBa5XxiC6tCITJH/QG70jswBhbLkxPw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.js" integrity="sha512-+UiyfI4KyV1uypmEqz9cOIJNwye+u+S58/hSwKEAeUMViTTqM9/L4lqu8UxJzhmzGpms8PzFJDzEqXL9niHyjA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <link rel="stylesheet" href="styles.css">
	</head> 
	<body> 
	<!--<table align="center" border="1px" style="width:600px; line-height:40px;"> -->
	<br /><br />  
	<div class="container">
	<div class="col-md-3">  
    <input type="text" name="from_date" id="from_date" class="form-control" placeholder="From date/hour" />  
    </div>  
    <div class="col-md-3">  
    <input type="text" name="to_date" id="to_date" class="form-control" placeholder="To date/hour" />  
    </div>  
    <div class="col-md-5">  
    <input type="button" name="Filter" id="filter" value="Filter" class="btn btn-info" />  
    </div>  
    <div style="clear:both"></div>                 
    <br />
    <div class="table-responsive">
    <div id="order_table">
	<table align="center" class="table table-bordered table-condensed table-sm w-auto text-xsmall">
	<thead>
	<th colspan="8"><h2>History of measured sensor data</h2></th> 
	<tr> 
		
		</tr> 
			  <th> ID </th> 
			  <th> Temperature </th> 
			  <th> Humidity </th>
			  <th> PM2.5 </th>
			  <th> PM10 </th>
			  <th> Bat Voltage</th>
			  <th> Sol Voltage</th>
			  <th> Date/time of record</th>
			  
		</tr> 
		
		<?php while($row = mysqli_fetch_assoc($result)) { ?> 
		<tr> <td><?php echo $row['id']; ?></td> 
		<td><?php echo $row["temp_val"]; ?></td> 
		<td><?php echo $row["hum_val"]; ?></td> 
		<td><?php echo $row["pm25_val"]; ?></td>
		<td><?php echo $row["pm10_val"]; ?></td>
		<td><?php echo $row["vbat_val"]; ?></td>
		<td><?php echo $row["vsol_val"]; ?></td>
		<td><?php echo $row['reading_time']; ?></td>
		</tr> 
        <?php  
            }  
            ?>  
        </table>  
            </div>  
        </div>
        </div>
      </body>  
 </html>
<script>
        $(document).ready(function(){
            $('body').find('img[src$="https://cdn.000webhost.com/000webhost/logo/footer-powered-by-000webhost-white2.png"]').parent().closest('a').closest('div').remove();
        });
</script>
<script>  
      $(document).ready(function(){  
           $(function(){  
                $("#from_date").datetimepicker();  
                $("#to_date").datetimepicker();  
           });  
           $('#filter').click(function(){  
                var from_date = $('#from_date').val();  
                var to_date = $('#to_date').val();  
                if(from_date != '' && to_date != '')  
                {  
                     $.ajax({  
                          url:"filter.php",  
                          method:"POST",  
                          data:{from_date:from_date, to_date:to_date},  
                          success:function(data)  
                          {  
                               $('#order_table').html(data);  
                          }  
                     });  
                }  
                else  
                {  
                     alert("Please Select Date");  
                }  
           });  
      });  
 </script>
