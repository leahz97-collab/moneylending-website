<?php

header("Content-Type: application/json");

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "money_lending_db";
$port = 3307;

$conn = new mysqli(
    $servername,
    $username,
    $password,
    $dbname,
    $port
);

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed."
    ]);
    exit;
}

$sql = "
    SELECT
        `Rfid no` AS rfid,
        `full name` AS fullName,
        `adres` AS address,
        `cont no` AS contact,
        `gend` AS gender,
        `occup` AS occupation,
        `dtreg` AS dateRegistered
    FROM client_info
    ORDER BY `dtreg` DESC
";

$result = $conn->query($sql);

$borrowers = [];

if ($result) {

    while ($row = $result->fetch_assoc()) {
        $borrowers[] = $row;
    }

    echo json_encode($borrowers);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Unable to get borrowers."
    ]);

}

$conn->close();

?>