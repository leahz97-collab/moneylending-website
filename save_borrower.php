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
        "message" => "Database connection failed: " . $conn->connect_error
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

$full_name = trim($_POST["full_name"] ?? "");
$address = trim($_POST["address"] ?? "");
$contact_number = trim($_POST["contact_number"] ?? "");
$gender = trim($_POST["gender"] ?? "");
$occupation = trim($_POST["occupation"] ?? "");
$date_registered = trim($_POST["date_registered"] ?? "");
$rfid_uid = trim($_POST["rfid_uid"] ?? "");

if (
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

/* Check RFID */
$check = $conn->prepare(
    "SELECT `Rfid no` FROM client_info WHERE `Rfid no` = ?"
);

$check->bind_param("s", $rfid_uid);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        "success" => false,
        "message" => "This RFID Card UID is already registered."
    ]);
    exit;
}

$check->close();

/* Check contact number */
$check = $conn->prepare(
    "SELECT `cont no` FROM client_info WHERE `cont no` = ?"
);

$check->bind_param("s", $contact_number);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        "success" => false,
        "message" => "This contact number is already registered."
    ]);
    exit;
}

$check->close();

/* Save borrower */
$sql = $conn->prepare(
    "INSERT INTO client_info
    (`Rfid no`, `full name`, `adres`, `cont no`, `gend`, `occup`, `dtreg`)
    VALUES (?, ?, ?, ?, ?, ?, ?)"
);

$sql->bind_param(
    "sssssss",
    $rfid_uid,
    $full_name,
    $address,
    $contact_number,
    $gender,
    $occupation,
    $date_registered
);

if ($sql->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Borrower registered successfully!"
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Error saving borrower: " . $sql->error
    ]);

}

$sql->close();
$conn->close();

?>