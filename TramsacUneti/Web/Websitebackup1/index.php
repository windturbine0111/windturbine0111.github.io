<?php
    include_once('esp-database.php');
    $readings_count = 10;
    $last_reading = getLastReadings();
    $last_reading_temp = $last_reading["value6"];
    $last_reading_humi = $last_reading["value2"];
    $last_reading_co2 = $last_reading["value3"];
    $last_reading_H2S = $last_reading["value4"];
    $last_reading_pf = $last_reading["value7"];
    $last_reading_time = $last_reading["reading_time"];
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" type="text/css" href="esp-style.css">
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
                 <a href="/esp-panelDC.php"><button>Solar Panel</button></a>
                 <a href="/esp-outputs.php"><button>Bảng Điều Khiển</button></a>
                </td>
			</tr>
		</table>
	</p>
    <section class="content">
	    <div class="box gauge--6">
	    <h3>Frequency</h3>
              <div class="mask">
			  <div class="semi-circle"></div>
			  <div class="semi-circle--mask"></div>
			</div>
		    <p style="font-size: 30px;" id="temp">--</p>
        </div>
        <div class="box gauge--2">
            <h3>Voltage Inverter</h3>
            <div class="mask">
                <div class="semi-circle"></div>
                <div class="semi-circle--mask"></div>
            </div>
            <p style="font-size: 30px;" id="humi">--</p>
        </div>
        <div class="box gauge--3">
            <h3>Current Inverter</h3>
            <div class="mask">
                <div class="semi-circle"></div>
                <div class="semi-circle--mask"></div>
            </div>
            <p style="font-size: 30px;" id="co2">--</p>
        </div>
        <div class="box gauge--4">
            <h3>Power Inverter</h3>
            <div class="mask">
                <div class="semi-circle"></div>
                <div class="semi-circle--mask"></div>
            </div>
            <p style="font-size: 30px;" id="H2S">--</p>
        </div>
        <div class="box gauge--7">
            <h3>Power Factor</h3>
            <div class="mask">
                <div class="semi-circle"></div>
                <div class="semi-circle--mask"></div>
            </div>
            <p style="font-size: 30px;" id="pf">--</p>
        </div>
        
    </section>
<?php
    echo   '<h2> Bảng ' . $readings_count . ' giá trị đọc cuối</h2>
            <table cellspacing="5" cellpadding="5" id="tableReadings">
                <tr>
                    <th>Số lần đo</th>
                    <th>Ðịa điểm</th>
                    <th>Voltage Battery(V)</th>
                    <th>Current Battery(A)</th>
                    <th>Power Battery(W)</th>
                    <th>Voltage Inverter(V)</th>
                    <th>Current Inverter(A)</th>
                    <th>Power Inverter(W)</th>
                    <th>Energy Inverter(Kwh)</th>
                    <th>Thời gian đọc</th>
                </tr>';
     $result = getAllReadings($readings_count);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $row_id = $row["id"];
            $row_sensor = $row["sensor"];
            $row_location = $row["location"];
            $row_value1 = $row["value1"];
            $row_value8 = $row["value8"];
            $row_value9 = $row["value9"];
            $row_value2 = $row["value2"];
            $row_value3 = $row["value3"];
            $row_value4 = $row["value4"];
            $row_value5 = $row["value5"];
            $row_reading_time = $row["reading_time"];
            // Uncomment to set timezone to - 1 hour (you can change 1 to any number)
            //$row_reading_time = date("Y-m-d H:i:s", strtotime("$row_reading_time - 1 hours"));
            // Uncomment to set timezone to + 7 hours (you can change 7 to any number)
            $row_reading_time = date("Y-m-d H:i:s", strtotime("$row_reading_time + 7 hours"));

            echo '<tr>
                    <td>' . $row_id . '</td>
                    <td>' . $row_location . '</td>
                    <td>' . $row_value1 . '</td>
                    <td>' . $row_value8 . '</td>
                    <td>' . $row_value9 . '</td>
                    <td>' . $row_value2 . '</td>
                    <td>' . $row_value3 . '</td>
                    <td>' . $row_value4 . '</td>
                    <td>' . $row_value5 . '</td>
                    <td>' . $row_reading_time . '</td>
                  </tr>';
        }
        echo '</table>';
        $result->free();
    }
?>

<script>
    var value6 = <?php echo $last_reading_temp; ?>;
    var value2 = <?php echo $last_reading_humi; ?>;
    var value3 = <?php echo $last_reading_co2; ?>;
    var value4 = <?php echo $last_reading_H2S; ?>;
    var value7 = <?php echo $last_reading_pf; ?>;
    setTemperature(value6);
    setHumidity(value2);
    setCO2(value3);
    setH2S(value4);
    setpf(value7);

    function setTemperature(curVal){
    	var minTemp = 0.0;
    	var maxTemp = 65.0;

    	var newVal = scaleValue(curVal, [minTemp, maxTemp], [0, 180]);
    	$('.gauge--6 .semi-circle--mask').attr({
    		style: '-webkit-transform: rotate(' + newVal + 'deg);' +
    		'-moz-transform: rotate(' + newVal + 'deg);' +
    		'transform: rotate(' + newVal + 'deg);'
    	});
    	$("#temp").text(curVal + ' HZ');
    }

    function setHumidity(curVal){
    	//set range for Humidity percentage 0 % to 100 %
    	var minHumi = 0;
    	var maxHumi = 270;

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
    	var maxH2S = 250;

    	var newVal = scaleValue(curVal, [minH2S, maxH2S], [0, 180]);
    	$('.gauge--4 .semi-circle--mask').attr({
    		style: '-webkit-transform: rotate(' + newVal + 'deg);' +
    		'-moz-transform: rotate(' + newVal + 'deg);' +
    		'transform: rotate(' + newVal + 'deg);'
    	});
    	$("#H2S").text(curVal + ' W');
    }
    
    function setpf(curVal){
    	var minpf = 0;
    	var maxpf = 3;

    	var newVal = scaleValue(curVal, [minpf, maxpf], [0, 180]);
    	$('.gauge--7 .semi-circle--mask').attr({
    		style: '-webkit-transform: rotate(' + newVal + 'deg);' +
    		'-moz-transform: rotate(' + newVal + 'deg);' +
    		'transform: rotate(' + newVal + 'deg);'
    	});
    	$("#pf").text(curVal + ' Pf');
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
