
<?php
  $servername = "localhost";
  $dbname = "id20306392_database_tramsacuneti0012";
  $username = "id20306392_haihoang1098";
  $password = "Boy10c1vf12345@";


  function insertReading($sensor, $location, $value1, $value2, $value3, $value4, $value5, $value6, $value7, $value8, $value9) {
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO SensorData (sensor, location, value1, value2, value3, value4, value5, value6, value7, value8, value9)
    VALUES ('" . $sensor . "', '" . $location . "', '" . $value1 . "', '" . $value2 . "', '" . $value3 . "', '" . $value4 . "', '" . $value5 . "', '" . $value6 . "', '" . $value7 . "', '" . $value8 . "', '" . $value9 . "')";

    if ($conn->query($sql) === TRUE) {
      return "New record created successfully";
    }
    else {
      return "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
  }
  
  function getAllReadings($limit) {
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, sensor, location, value1, value2, value3, value4, value5, value6, value7, value8, value9, reading_time FROM SensorData order by reading_time desc limit " . $limit;
    if ($result = $conn->query($sql)) {
      return $result;
    }
    else {
      return false;
    }
    $conn->close();
  }
  function getLastReadings() {
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, sensor, location, value1, value2, value3, value4, value5, value6, value7, value8, value9, reading_time FROM SensorData order by reading_time desc limit 1" ;
    if ($result = $conn->query($sql)) {
      return $result->fetch_assoc();
    }
    else {
      return false;
    }
    $conn->close();
  }

  function minReading($limit, $value) {
     global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT MIN(" . $value . ") AS min_amount FROM (SELECT " . $value . " FROM SensorData order by reading_time desc limit " . $limit . ") AS min";
    if ($result = $conn->query($sql)) {
      return $result->fetch_assoc();
    }
    else {
      return false;
    }
    $conn->close();
  }

  function maxReading($limit, $value) {
     global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT MAX(" . $value . ") AS max_amount FROM (SELECT " . $value . " FROM SensorData order by reading_time desc limit " . $limit . ") AS max";
    if ($result = $conn->query($sql)) {
      return $result->fetch_assoc();
    }
    else {
      return false;
    }
    $conn->close();
  }

  function avgReading($limit, $value) {
     global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT AVG(" . $value . ") AS avg_amount FROM (SELECT " . $value . " FROM SensorData order by reading_time desc limit " . $limit . ") AS avg";
    if ($result = $conn->query($sql)) {
      return $result->fetch_assoc();
    }
    else {
      return false;
    }
    $conn->close();
  }
  function createOutput($name, $board, $gpio, $state) {
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO Outputs (name, board, gpio, state)
    VALUES ('" . $name . "', '" . $board . "', '" . $gpio . "', '" . $state . "')";

   if ($conn->query($sql) === TRUE) {
        return "New output created successfully";
    }
    else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}

function deleteOutput($id) {
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "DELETE FROM Outputs WHERE id='". $id .  "'";

   if ($conn->query($sql) === TRUE) {
        return "Output deleted successfully";
    }
    else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}

function updateOutput($id, $state) {
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "UPDATE Outputs SET state='" . $state . "' WHERE id='". $id .  "'";

   if ($conn->query($sql) === TRUE) {
        return "Output state updated successfully";
    }
    else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}

function getAllOutputs() {
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, name, board, gpio, state FROM Outputs ORDER BY board";
    if ($result = $conn->query($sql)) {
        return $result;
    }
    else {
        return false;
    }
    $conn->close();
}

function getAllOutputStates($board) {
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT gpio, state FROM Outputs WHERE board='" . $board . "'";
    if ($result = $conn->query($sql)) {
        return $result;
    }
    else {
        return false;
    }
    $conn->close();
}

function getOutputBoardById($id) {
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT board FROM Outputs WHERE id='" . $id . "'";
    if ($result = $conn->query($sql)) {
        return $result;
    }
    else {
        return false;
    }
    $conn->close();
}

function updateLastBoardTime($board) {
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "UPDATE Boards SET last_request=now() WHERE board='". $board .  "'";

   if ($conn->query($sql) === TRUE) {
        return "Output state updated successfully";
    }
    else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}

function getAllBoards() {
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT board, last_request FROM Boards ORDER BY board";
    if ($result = $conn->query($sql)) {
        return $result;
    }
    else {
        return false;
    }
    $conn->close();
}

function getBoard($board) {
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT board, last_request FROM Boards WHERE board='" . $board . "'";
    if ($result = $conn->query($sql)) {
        return $result;
    }
    else {
        return false;
    }
    $conn->close();
}

function createBoard($board) {
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO Boards (board) VALUES ('" . $board . "')";

   if ($conn->query($sql) === TRUE) {
        return "New board created successfully";
    }
    else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}

function deleteBoard($board) {
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "DELETE FROM Boards WHERE board='". $board .  "'";

   if ($conn->query($sql) === TRUE) {
        return "Board deleted successfully";
    }
    else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}
?>
