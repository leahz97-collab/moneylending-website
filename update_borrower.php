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

$original_rfid = trim($_POST["original_rfid_uid"] ?? "");

$full_name = trim($_POST["full_name"] ?? "");
$address = trim($_POST["address"] ?? "");
$contact_number = trim($_POST["contact_number"] ?? "");
$gender = trim($_POST["gender"] ?? "");
$occupation = trim($_POST["occupation"] ?? "");
$date_registered = trim($_POST["date_registered"] ?? "");
$rfid_uid = trim($_POST["rfid_uid"] ?? "");

if (
    empty($original_rfid) ||
    empty($full_name) ||
    empty($address) ||
    empty($contact_number) ||
    empty($gender) ||
    empty($occupation) ||
    empty($date_registered) ||
    empty($rfid_uid)
) {
    echo json_encode([
        "success" => false,
        "message" => "Please fill in all fields."
    ]);
    exit;
}

if (!preg_match('/^09\d{9}$/', $contact_number)) {
    echo json_encode([
        "success" => false,
        "message" => "Contact number must be 11 digits and start with 09."
    ]);
    exit;
}

$sql = $conn->prepare("
    UPDATE client_info
    SET
        `Rfid no` = ?,
        `full name` = ?,
        `adres` = ?,
        `cont no` = ?,
        `gend` = ?,
        `occup` = ?,
        `dtreg` = ?
    WHERE `Rfid no` = ?
");

$sql->bind_param(
    "ssssssss",
    $rfid_uid,
    $full_name,
    $address,
    $contact_number,
    $gender,
    $occupation,
    $date_registered,
    $original_rfid
);

if ($sql->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Borrower updated successfully!"
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Error updating borrower: " . $sql->error
    ]);

}

$sql->close();
$conn->close();

?>