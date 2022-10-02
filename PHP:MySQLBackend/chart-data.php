<?php
include '../private_dir/config.php';
$db_con = new Connection();
$api_key = $temp = $hum = $pm25 = $pm10 = $vbat = $vsol = "";
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
            $sql1 = "SELECT id,temp_val,hum_val,pm25_val,pm10_val,vbat_val,vsol_val,CONVERT_TZ(reading_time,'+00:00','+01:00') as reading_time from SensorData ORDER BY reading_time DESC limit 40";
        }
        else {
            $sql1 = "SELECT id,temp_val,hum_val,pm25_val,pm10_val,vbat_val,vsol_val,CONVERT_TZ(reading_time,'+00:00','+02:00') as reading_time from SensorData ORDER BY reading_time DESC limit 40";
        }
        $result=mysqli_query($conn,$sql1);
}

while ($data = $result->fetch_assoc()){
    $sensor_data[] = $data;
}

$readings_time = array_column($sensor_data, 'reading_time');

// ******* Uncomment to convert readings time array to your timezone ********
/*$i = 0;
foreach ($readings_time as $reading){
    // Uncomment to set timezone to - 1 hour (you can change 1 to any number)
    $readings_time[$i] = date("Y-m-d H:i:s", strtotime("$reading - 1 hours"));
    // Uncomment to set timezone to + 4 hours (you can change 4 to any number)
    //$readings_time[$i] = date("Y-m-d H:i:s", strtotime("$reading + 4 hours"));
    $i += 1;
}*/

$value1 = json_encode(array_reverse(array_column($sensor_data, 'temp_val')), JSON_NUMERIC_CHECK);
$value2 = json_encode(array_reverse(array_column($sensor_data, 'hum_val')), JSON_NUMERIC_CHECK);
$value3 = json_encode(array_reverse(array_column($sensor_data, 'pm10_val')), JSON_NUMERIC_CHECK);
$value4 = json_encode(array_reverse(array_column($sensor_data, 'pm25_val')), JSON_NUMERIC_CHECK);
$value5 = json_encode(array_reverse(array_column($sensor_data, 'vbat_val')), JSON_NUMERIC_CHECK);
$value6 = json_encode(array_reverse(array_column($sensor_data, 'vsol_val')), JSON_NUMERIC_CHECK);
$reading_time = json_encode(array_reverse($readings_time), JSON_NUMERIC_CHECK);

/*echo $value1;
echo $value2;
echo $value3;
echo $reading_time;*/

$result->free();
$conn->close();
?>

<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <style>
    body {
      min-width: 310px;
    	max-width: 1280px;
    	height: 500px;
      margin: 0 auto;
    }
    h2 {
      font-family: Arial;
      font-size: 2.5rem;
      text-align: center;
    }
  </style>
  <body>
    <h2>Air Quality Monitor Graph</h2>
    <div id="chart">
    <div id="chart-temperature" class="container"></div>
    <div id="chart-humidity" class="container"></div>
    <div id="chart-pm10" class="container"></div>
    <div id="chart-pm25" class="container"></div>
    <div id="chart-vbat" class="container"></div>
    <div id="chart-vsol" class="container"></div>
    </div>
<script>

var value1 = <?php echo $value1; ?>;
var value2 = <?php echo $value2; ?>;
var value3 = <?php echo $value3; ?>;
var value4 = <?php echo $value4; ?>;
var value5 = <?php echo $value5; ?>;
var value6 = <?php echo $value6; ?>;
var reading_time = <?php echo $reading_time; ?>;

var chartT = new Highcharts.Chart({
  chart:{ renderTo : 'chart-temperature' },
  title: { text: 'Temperature' },
  series: [{
    showInLegend: false,
    data: value1
  }],
  plotOptions: {
    line: { animation: false,
      dataLabels: { enabled: true }
    },
    series: { color: '#059e8a' }
  },
  xAxis: { 
    type: 'datetime',
    categories: reading_time
  },
  yAxis: {
    title: { text: 'Temperature (°C)' }
    //title: { text: 'Temperature (Fahrenheit)' }
  },
  credits: { enabled: false }
});

var chartH = new Highcharts.Chart({
  chart:{ renderTo:'chart-humidity' },
  title: { text: 'Relative Humidity' },
  series: [{
    showInLegend: false,
    data: value2
  }],
  plotOptions: {
    line: { animation: false,
      dataLabels: { enabled: true }
    }
  },
  xAxis: {
    type: 'datetime',
    //dateTimeLabelFormats: { second: '%H:%M:%S' },
    categories: reading_time
  },
  yAxis: {
    title: { text: 'Relative Humidity (%)' }
  },
  credits: { enabled: false }
});


var chartP = new Highcharts.Chart({
  chart:{ renderTo:'chart-pm10' },
  title: { text: 'PM10' },
  series: [{
    showInLegend: false,
    data: value3
  }],
  plotOptions: {
    line: { animation: false,
      dataLabels: { enabled: true }
    },
    series: { color: '#18009c' }
  },
  xAxis: {
    type: 'datetime',
    categories: reading_time
  },
  yAxis: {
    title: { text: 'PM10 (ug/m3)' }
  },
  credits: { enabled: false }
});

var chartQ = new Highcharts.Chart({
  chart:{ renderTo:'chart-pm25' },
  title: { text: 'PM2.5' },
  series: [{
    showInLegend: false,
    data: value4
  }],
  plotOptions: {
    line: { animation: false,
      dataLabels: { enabled: true }
    },
    series: { color: '#18029c' }
  },
  xAxis: {
    type: 'datetime',
    categories: reading_time
  },
  yAxis: {
    title: { text: 'PM2.5 (ug/m3)' }
  },
  credits: { enabled: false }
});

var vbat = new Highcharts.Chart({
  chart:{ renderTo:'chart-vbat' },
  title: { text: 'Battery Voltage mV' },
  series: [{
    showInLegend: false,
    data: value5
  }],
  plotOptions: {
    line: { animation: false,
      dataLabels: { enabled: true }
    },
    series: { color: '#ff936a' }
  },
  xAxis: {
    type: 'datetime',
    categories: reading_time
  },
  yAxis: {
    title: { text: 'Battery Voltage mV' }
  },
  credits: { enabled: false }
});

var vsol = new Highcharts.Chart({
  chart:{ renderTo:'chart-vsol' },
  title: { text: 'Solar Panel Voltage (mV)' },
  series: [{
    showInLegend: false,
    data: value6
  }],
  plotOptions: {
    line: { animation: false,
      dataLabels: { enabled: true }
    },
    series: { color: '#a3ffb4' }
  },
  xAxis: {
    type: 'datetime',
    categories: reading_time
  },
  yAxis: {
    title: { text: 'Solar Panel Voltage (mV)' }
  },
  credits: { enabled: false }
});

</script>

<script>
        $(document).ready(function(){
            $('body').find('img[src$="https://cdn.000webhost.com/000webhost/logo/footer-powered-by-000webhost-white2.png"]').parent().closest('a').closest('div').remove();
        });
</script>
<script>
function doRefresh(){
    $('#chart-temperature').load("#chart-temperature");
    $('#chart-humidity').load("#chart-humidity");
    $('#chart-pm25').load("#chart-pm25");
    $('#chart-pm10').load("#chart-pm10");
    $('#chart-vbat').load("#chart-vbat");
    $('#chart-vsol').load("#chart-vsol");
    
}
$(function() {
    setInterval(doRefresh, 5000);
});
</script>
</body>
</html>
