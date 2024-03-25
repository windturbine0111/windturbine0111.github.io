
<?php
    include_once('esp-database.php');

    $result = getAllOutputs();
    $html_buttons = null;
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if ($row["state"] == "1"){
                $button_checked = "checked";
            }
            else {
                $button_checked = "";
            }
            $html_buttons .= '<h3>' . $row["name"] . ' - Board '. $row["board"] . ' - GPIO ' . $row["gpio"] . ' (<i><a onclick="deleteOutput(this)" href="javascript:void(0);" id="' . $row["id"] . '">Xóa</a></i>)</h3><label class="switch"><input type="checkbox" onchange="updateOutput(this)" id="' . $row["id"] . '" ' . $button_checked . '><span class="slider"></span></label>';
        }
    }

    $result2 = getAllBoards();
    $html_boards = null;
    if ($result2) {
        $html_boards .= '<h3>Boards</h3>';
        while ($row = $result2->fetch_assoc()) {
            $row_reading_time = $row["last_request"];
            $html_boards .= '<p><strong>Board ' . $row["board"] . '</strong> - Lần Đọc Cuối: '. $row_reading_time . '</p>';
        }
    }
?>

<!DOCTYPE HTML>
<html>
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="esp-style1.css">
        <link rel="shortcut icon" type="image/png" href="/UNETI.png"/>
        <title>Bảng Điều Khiển</title>
    </head>
<body>
       <h2>Bảng Điều Khiển</h2>
    <tr>
    <td>
           <a href="/index.php"><button>Inverter-Battery</button></a>
           <a href="/esp-panelDC.php"><button>Solar Panel</button></a>
				</td>
                </tr>
    <?php echo $html_buttons; ?>
    <br><br>
    <?php echo $html_boards; ?>
    <br><br>
    <div><form onsubmit="return createOutput();">
        <h3>Tạo Mới điều khiển</h3>
        <label for="outputName">Tên Thiết bị</label>
        <input type="text" name="name" id="outputName"><br>
        <label for="outputBoard">Board ID</label>
        <input type="number" name="board" min="0" id="outputBoard">
        <label for="outputGpio">Chân GPIO</label>
        <input type="number" name="gpio" min="0" id="outputGpio">
        <label for="outputState">Trạng Thái GPIO</label>
        <select id="outputState" name="state">
          <option value="1">1 = ON</option>
          <option value="0">0 = OFF</option>
        </select>
        <input type="submit" value="Khởi Tạo">
        <p><strong>Lưu ý:</strong> Ban đầu khi truy cập trang web. chuyển các nút bấm về trạng thái OFF. Khi tạo mới không thấy nút bấm vui lòng load lại trang. Các chân GPIO Sử dụng trên broad là GPIO 5,4,0,2. time delay 5s</p>
    </form></div>

    <script>
        function updateOutput(element) {
            var xhr = new XMLHttpRequest();
            if(element.checked){
                xhr.open("GET", "esp-outputs-action.php?action=output_update&id="+element.id+"&state=0", true);
            }
            else {
                xhr.open("GET", "esp-outputs-action.php?action=output_update&id="+element.id+"&state=1", true);
            }
            xhr.send();
        }

        function deleteOutput(element) {
            var result = confirm("Bạn muốn Xóa Đầu Ra Điều Khiển?");
            if (result) {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "esp-outputs-action.php?action=output_delete&id="+element.id, true);
                xhr.send();
                alert("Đã Xóa");
                setTimeout(function(){ window.location.reload(); });
            }
        }

        function createOutput(element) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "esp-outputs-action.php", true);

            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    alert("Đầu ra Điều Khiển đã được tạo");
                    setTimeout(function(){ window.location.reload(); });
                }
            }
            var outputName = document.getElementById("outputName").value;
            var outputBoard = document.getElementById("outputBoard").value;
            var outputGpio = document.getElementById("outputGpio").value;
            var outputState = document.getElementById("outputState").value;
            var httpRequestData = "action=output_create&name="+outputName+"&board="+outputBoard+"&gpio="+outputGpio+"&state="+outputState;
            xhr.send(httpRequestData);
        }
    </script>
    <h3>Design by HaiHoang</h3>
</body>
</html>
