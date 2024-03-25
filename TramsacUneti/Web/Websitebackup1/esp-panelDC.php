<?php
    include_once('esp-databaseDC.php');
    $readings_count = 10;
    $last_reading = getLastReadings();
    $last_reading_temp = $last_reading["value5"];
    $last_reading_humi = $last_reading["value1"];
    $last_reading_co2 = $last_reading["value2"];
    $last_reading_H2S = $last_reading["value3"];
    $last_reading_NH3 = $last_reading["value4"];
    $last_reading_time = $last_reading["reading_time"];
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" type="text/css" href="esp-style2.css">
        <link rel="shortcut icon" type="image/png" href="/UNETI.png"/>
        <meta name="viewport" content="width=device-width, initial-scale=2">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<style>
		a{
				diplay:block;
				background: color #00CCFF;
				text-align:center;
		}
		a:link, a.visited{
			    color:white;
			    text-decoration:none;
		}
		a:hover{
			color:cyan;
		}
	</style>        
    </head>
    <header class="header">
        <h1 style="color:White;">Giao Diện Giám sát Năng lượng Trạm Sạc</h1>
    </header>
<body>
	<p>
		<table cellspacing="5" cellpadding="5">
			<tr>
				<td>
					Lần đọc cuối cùng: <?php echo date("Y-m-d H:i:s", strtotime("$last_reading_time + 7 hours")); ?>
				</td>
				<td>
                <a href="/index.php"><button>Inverter-Battery</button></a>
                 <a href="/esp-outputs.php"><button>Bảng Điều Khiển</button></a>
                </td>
			</tr>
		</table>
	</p>
    <section class="content">
	    <div class="box gauge--6">
	    <h3>Voltage Battery</h3>
              <div class="mask">
			  <div class="semi-circle"></div>
			  <div class="semi-circle--mask"></div>
			</div>
		    <p style="font-size: 30px;" id="temp">--</p>
        </div>
        <div class="box gauge--2">
            <h3>Voltage Solar</h3>
            <div class="mask">
                <div class="semi-circle"></div>
                <div class="semi-circle--mask"></div>
            </div>
            <p style="font-size: 30px;" id="humi">--</p>
        </div>
        <div class="box gauge--3">
            <h3>Current Solar</h3>
            <div class="mask">
                <div class="semi-circle"></div>
                <div class="semi-circle--mask"></div>
            </div>
            <p style="font-size: 30px;" id="co2">--</p>
        </div>
        <div class="box gauge--4">
            <h3>Power Solar</h3>
            <div class="mask">
                <div class="semi-circle"></div>
                <div class="semi-circle--mask"></div>
            </div>
            <p style="font-size: 30px;" id="H2S">--</p>
        </div>
        <div class="box gauge--5">
            <h3>Energy Solar</h3>
            <div class="mask">
                <div class="semi-circle"></div>
                <div class="semi-circle--mask"></div>
            </div>
            <p style="font-size: 30px;" id="NH3">--</p>
        </div>
        
    </section>
<?php
    echo   '<h2> Bảng ' . $readings_count . ' giá trị đọc cuối</h2>
            <table cellspacing="5" cellpadding="5" id="tableReadings">
                <tr>
                    <th>Số lần đo</th>
                    <th>Ðịa điểm</th>
                    <th>Voltage Battery(V)</th>
                    <th>Voltage Solar(V)</th>
                    <th>Current Solar(A)</th>
                    <th>Power Solar(W)</th>
                    <th>Energy Solar(Kwh)</th>
                    <th>Thời gian đọc</th>
                </tr>';
     $result = getAllReadings($readings_count);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $row_id = $row["id"];
            $row_sensor = $row["sensor"];
            $row_location = $row["location"];
            $row_value5 = $row["value5"];
            $row_value1 = $row["value1"];
            $row_value2 = $row["value2"];
            $row_value3 = $row["value3"];
            $row_value4 = $row["value4"];
            $row_reading_time = $row["reading_time"];
            // Uncomment to set timezone to - 1 hour (you can change 1 to any number)
            //$row_reading_time = date("Y-m-d H:i:s", strtotime("$row_reading_time - 1 hours"));
            // Uncomment to set timezone to + 7 hours (you can change 7 to any number)
            $row_reading_time = date("Y-m-d H:i:s", strtotime("$row_reading_time + 7 hours"));

            echo '<tr>
                    <td>' . $row_id . '</td>
                    <td>' . $row_location . '</td>
                    <td>' . $row_value5 . '</td>
                    <td>' . $row_value1 . '</td>
                    <td>' . $row_value2 . '</td>
                    <td>' . $row_value3 . '</td>
                    <td>' . $row_value4 . '</td>
                    <td>' . $row_reading_time . '</td>
                  </tr>';
        }
        echo '</table>';
        $result->free();
    }
?>

<script>
    var value5 = <?php echo $last_reading_temp; ?>;
    var value1 = <?php echo $last_reading_humi; ?>;
    var value2 = <?php echo $last_reading_co2; ?>;
    var value3 = <?php echo $last_reading_H2S; ?>;
    var value4 = <?php echo $last_reading_NH3; ?>;
    setTemperature(value5);
    setHumidity(value1);
    setCO2(value2);
    setH2S(value3);
    setNH3(value4);

    function setTemperature(curVal){
    	var minTemp = 0;
    	var maxTemp = 20;

    	var newVal = scaleValue(curVal, [minTemp, maxTemp], [0, 180]);
    	$('.gauge--6 .semi-circle--mask').attr({
    		style: '-webkit-transform: rotate(' + newVal + 'deg);' +
    		'-moz-transform: rotate(' + newVal + 'deg);' +
    		'transform: rotate(' + newVal + 'deg);'
    	});
    	$("#temp").text(curVal + ' V');
    }

    function setHumidity(curVal){
    	//set range for Humidity percentage 0 % to 100 %
    	var minHumi = 0;
    	var maxHumi = 30;

    	var newVal = scaleValue(curVal, [minHumi, maxHumi], [0, 180]);
    	$('.gauge--2 .semi-circle--mask').attr({
    		style: '-webkit-transform: rotate(' + newVal + 'deg);' +
    		'-moz-transform: rotate(' + newVal + 'deg);' +
    		'transform: rotate(' + newVal + 'deg);'
    	});
    	$("#humi").text(curVal + ' V');
    }

    function setCO2(curVal){
    	//Ð?t giá tr? cho n?ng d? CO2: 0 - 1000 PPM
    	var minCO2 = 0;
    	var maxCO2 = 10;

    	var newVal = scaleValue(curVal, [minCO2, maxCO2], [0, 180]);
    	$('.gauge--3 .semi-circle--mask').attr({
    		style: '-webkit-transform: rotate(' + newVal + 'deg);' +
    		'-moz-transform: rotate(' + newVal + 'deg);' +
    		'transform: rotate(' + newVal + 'deg);'
    	});
    	$("#co2").text(curVal + ' A');
    }
    function setH2S(curVal){
    	//Ð?t giá tr? cho n?ng d? H2S: 0 - 1000 PPM
    	var minH2S = 0;
    	var maxH2S = 30;

    	var newVal = scaleValue(curVal, [minH2S, maxH2S], [0, 180]);
    	$('.gauge--4 .semi-circle--mask').attr({
    		style: '-webkit-transform: rotate(' + newVal + 'deg);' +
    		'-moz-transform: rotate(' + newVal + 'deg);' +
    		'transform: rotate(' + newVal + 'deg);'
    	});
    	$("#H2S").text(curVal + ' W');
    }
    
    function setNH3(curVal){
    	//Ð?t giá tr? cho n?ng d? NH3: 0 - 1000 PPM
    	var minNH3 = 0;
    	var maxNH3 = 10;

    	var newVal = scaleValue(curVal, [minNH3, maxNH3], [0, 180]);
    	$('.gauge--5 .semi-circle--mask').attr({
    		style: '-webkit-transform: rotate(' + newVal + 'deg);' +
    		'-moz-transform: rotate(' + newVal + 'deg);' +
    		'transform: rotate(' + newVal + 'deg);'
    	});
    	$("#NH3").text(curVal + ' Kwh');
    }

    function scaleValue(value, from, to) {
        var scale = (to[1] - to[0]) / (from[1] - from[0]);
        var capped = Math.min(from[1], Math.max(from[0], value)) - from[0];
        return ~~(capped * scale + to[0]);
    }
</script>
<br>
<h3>Design by HaiHoang</h3>
</body>
</html>
