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

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);
    exit;
}

$rfid_uid = trim($_POST["rfid_uid"] ?? "");

if (empty($rfid_uid)) {
    echo json_encode([
        "success" => false,
        "message" => "RFID UID is required."
    ]);
    exit;
}

$sql = $conn->prepare(
    "DELETE FROM client_info WHERE `Rfid no` = ?"
);

$sql->bind_param("s", $rfid_uid);

if ($sql->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Borrower deleted successfully!"
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Error deleting borrower: " . $sql->error
    ]);

}

$sql->close();
$conn->close();

?>