<?php

header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/config.php";

function responseJson($success, $message = "", $extra = array(), $status = 200)
{
    http_response_code($status);
    $response = array("success" => $success, "message" => $message);
    foreach ($extra as $key => $value) {
        $response[$key] = $value;
    }
    echo json_encode($response);
    exit;
}

$action = isset($_GET["action"]) ? $_GET["action"] : "";

/* LOAD BORROWERS */
if ($action === "borrowers") {
    try {
        $sql = "
            SELECT
                `Rfid no`,
                `full name`,
                `adres`,
                `cont no`,
                `gend`,
                `occup`,
                `dtreg`
            FROM client_info
            ORDER BY `full name` ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        responseJson(true, "Borrowers loaded successfully.", array(
            "borrowers" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ));
    } catch (PDOException $e) {
        responseJson(false, "Unable to retrieve borrowers.", array(
            "error" => $e->getMessage()
        ), 500);
    }
}

/* SAVE OR UPDATE BORROWER */
if ($action === "save_borrower") {
    $fullName       = trim(isset($_POST["full_name"]) ? $_POST["full_name"] : "");
    $address        = trim(isset($_POST["address"]) ? $_POST["address"] : "");
    $contact        = trim(isset($_POST["contact_number"]) ? $_POST["contact_number"] : "");
    $gender         = trim(isset($_POST["gender"]) ? $_POST["gender"] : "");
    $occupation     = trim(isset($_POST["occupation"]) ? $_POST["occupation"] : "");
    $dateRegistered = trim(isset($_POST["date_registered"]) ? $_POST["date_registered"] : "");
    $rfid           = trim(isset($_POST["rfid_uid"]) ? $_POST["rfid_uid"] : "");
    $oldRfid        = trim(isset($_POST["old_rfid"]) ? $_POST["old_rfid"] : "");

    if ($fullName === "" || $address === "" || $contact === "" || $gender === "" || $occupation === "" || $dateRegistered === "" || $rfid === "") {
        responseJson(false, "Please complete all borrower fields.", array(), 400);
    }

    if (strtolower($gender) === "male") {
        $genderDb = "M";
    } elseif (strtolower($gender) === "female") {
        $genderDb = "F";
    } else {
        $genderDb = $gender;
    }

    try {
        /* UPDATE BORROWER */
        if ($oldRfid !== "") {
            $check = $pdo->prepare("
                SELECT `Rfid no`
                FROM client_info
                WHERE `Rfid no` = :rfid
                  AND `Rfid no` <> :old_rfid
                LIMIT 1
            ");
            $check->execute(array(":rfid" => $rfid, ":old_rfid" => $oldRfid));

            if ($check->fetch()) {
                responseJson(false, "That RFID UID is already assigned to another borrower.", array(), 409);
            }

            $stmt = $pdo->prepare("
                UPDATE client_info
                SET
                    `Rfid no`   = :rfid,
                    `full name` = :full_name,
                    `adres`     = :address,
                    `cont no`   = :contact,
                    `gend`      = :gender,
                    `occup`     = :occupation,
                    `dtreg`     = :date_registered
                WHERE `Rfid no` = :old_rfid
            ");
            $stmt->execute(array(
                ":rfid"            => $rfid,
                ":full_name"       => $fullName,
                ":address"         => $address,
                ":contact"         => $contact,
                ":gender"          => $genderDb,
                ":occupation"      => $occupation,
                ":date_registered" => $dateRegistered,
                ":old_rfid"        => $oldRfid
            ));
            responseJson(true, "Borrower updated successfully.");
        }

        /* CHECK EXISTING RFID */
        $check = $pdo->prepare("
            SELECT `Rfid no`
            FROM client_info
            WHERE `Rfid no` = :rfid
            LIMIT 1
        ");
        $check->execute(array(":rfid" => $rfid));

        if ($check->fetch()) {
            responseJson(false, "That RFID UID is already registered.", array(), 409);
        }

        /* INSERT BORROWER */
        $stmt = $pdo->prepare("
            INSERT INTO client_info
            (`Rfid no`, `full name`, `adres`, `cont no`, `gend`, `occup`, `dtreg`)
            VALUES
            (:rfid, :full_name, :address, :contact, :gender, :occupation, :date_registered)
        ");
        $stmt->execute(array(
            ":rfid"            => $rfid,
            ":full_name"       => $fullName,
            ":address"         => $address,
            ":contact"         => $contact,
            ":gender"          => $genderDb,
            ":occupation"      => $occupation,
            ":date_registered" => $dateRegistered
        ));
        responseJson(true, "Borrower saved successfully.");
    } catch (PDOException $e) {
        responseJson(false, "Database error while saving borrower.", array(
            "error" => $e->getMessage()
        ), 500);
    }
}

/* DELETE BORROWER */
if ($action === "delete_borrower") {
    $rawInput = file_get_contents("php://input");
    $json     = json_decode($rawInput, true);

    if (!is_array($json)) {
        $json = array();
    }

    $rfid = trim(isset($json["rfid"]) ? $json["rfid"] : "");

    if ($rfid === "" && isset($_POST["rfid"])) {
        $rfid = trim($_POST["rfid"]);
    }

    if ($rfid === "") {
        responseJson(false, "RFID UID is required.", array(), 400);
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM client_info WHERE `Rfid no` = :rfid");
        $stmt->execute(array(":rfid" => $rfid));

        if ($stmt->rowCount() === 0) {
            responseJson(false, "Borrower was not found.", array(), 404);
        }

        responseJson(true, "Borrower deleted successfully.");
    } catch (PDOException $e) {
        responseJson(false, "Database error while deleting borrower.", array(
            "error" => $e->getMessage()
        ), 500);
    }
}

/* LOAD LOANS */
if ($action === "loans") {
    try {
        $sql = "
            SELECT
                l.id,
                l.rfid_no,
                c.`full name` AS full_name,
                l.loan_amount,
                l.interest_rate,
                l.interest_amount,
                l.total_interest,
                l.total_payable,
                l.amount_paid,
                l.balance,
                l.loan_term,
                l.payment_frequency,
                l.start_date,
                l.status,
                l.created_at,
                l.updated_at
            FROM loans l
            LEFT JOIN client_info c ON l.rfid_no = c.`Rfid no`
            ORDER BY l.id DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        responseJson(true, "Loans loaded successfully.", array(
            "loans" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ));
    } catch (PDOException $e) {
        responseJson(false, "Unable to retrieve loans.", array(
            "error" => $e->getMessage()
        ), 500);
    }
}

/* SAVE LOAN */
if ($action === "save_loan") {
    $rfid             = trim(isset($_POST["borrower_id"]) ? $_POST["borrower_id"] : "");
    $loanAmount       = (float)(isset($_POST["loanAmount"]) ? $_POST["loanAmount"] : 0);
    $interestRate     = (float)(isset($_POST["interestRate"]) ? $_POST["interestRate"] : 0);
    $loanTerm         = trim(isset($_POST["loanTerm"]) ? $_POST["loanTerm"] : "");
    $paymentFrequency = trim(isset($_POST["paymentFrequency"]) ? $_POST["paymentFrequency"] : "Monthly");
    $startDate        = trim(isset($_POST["startDate"]) ? $_POST["startDate"] : "");
    $status           = trim(isset($_POST["loanStatus"]) ? $_POST["loanStatus"] : "Active");

    if ($rfid === "" || $loanAmount <= 0 || $interestRate < 0 || $loanTerm === "" || $startDate === "" || $status === "") {
        responseJson(false, "Please complete all loan fields.", array(), 400);
    }

    try {
        $borrowerCheck = $pdo->prepare("SELECT `Rfid no` FROM client_info WHERE `Rfid no` = :rfid LIMIT 1");
        $borrowerCheck->execute(array(":rfid" => $rfid));

        if (!$borrowerCheck->fetch()) {
            responseJson(false, "Selected borrower was not found.", array(), 404);
        }

        /* Extract numeric months from loan term (e.g., "3 Months" -> 3) */
        $loanTermMonths = (int)filter_var($loanTerm, FILTER_SANITIZE_NUMBER_INT);

        /* =====================================================
           ROUNDED UP — Interest per month
           Before: round(($loanAmount * $interestRate) / 100, 2)
           After:  ceil(...) → rounds UP to the nearest peso.
        ===================================================== */
        $interestAmount = ceil(($loanAmount * $interestRate) / 100);

        /* =====================================================
           ROUNDED UP — Total interest over the entire term
           Before: round($interestAmount * $loanTermMonths, 2)
           After:  ceil(...) → rounds UP to the nearest peso.
        ===================================================== */
        $totalInterest  = ceil($interestAmount * $loanTermMonths);

        $amountPaid     = 0;

        /* We remove total_payable and balance from the INSERT because they are generated columns */
        $stmt = $pdo->prepare("
            INSERT INTO loans
            (rfid_no, loan_amount, interest_rate, interest_amount, total_interest, amount_paid, loan_term, payment_frequency, start_date, status)
            VALUES
            (:rfid_no, :loan_amount, :interest_rate, :interest_amount, :total_interest, :amount_paid, :loan_term, :payment_frequency, :start_date, :status)
        ");
        $stmt->execute(array(
            ":rfid_no"           => $rfid,
            ":loan_amount"       => $loanAmount,
            ":interest_rate"     => $interestRate,
            ":interest_amount"   => $interestAmount,
            ":total_interest"    => $totalInterest,
            ":amount_paid"       => $amountPaid,
            ":loan_term"         => $loanTerm,
            ":payment_frequency" => $paymentFrequency,
            ":start_date"        => $startDate,
            ":status"            => $status
        ));
        responseJson(true, "Loan saved successfully.", array(
            "loan_id" => $pdo->lastInsertId()
        ));
    } catch (PDOException $e) {
        responseJson(false, "Database error while saving loan.", array(
            "error" => $e->getMessage()
        ), 500);
    }
}

/* SAVE PAYMENT */
if ($action === "save_payment") {
    $loanId = isset($_POST["loan_id"]) ? (int)$_POST["loan_id"] : 0;

    $paymentAmount = 0;
    if (isset($_POST["payment_amount"])) {
        $paymentAmount = (float)$_POST["payment_amount"];
    } elseif (isset($_POST["paymentAmount"])) {
        $paymentAmount = (float)$_POST["paymentAmount"];
    } elseif (isset($_POST["amount"])) {
        $paymentAmount = (float)$_POST["amount"];
    }

    $paymentDate = "";
    if (isset($_POST["payment_date"])) {
        $paymentDate = trim($_POST["payment_date"]);
    } elseif (isset($_POST["paymentDate"])) {
        $paymentDate = trim($_POST["paymentDate"]);
    }

    $paymentMethod = "";
    if (isset($_POST["payment_method"])) {
        $paymentMethod = trim($_POST["payment_method"]);
    } elseif (isset($_POST["paymentMethod"])) {
        $paymentMethod = trim($_POST["paymentMethod"]);
    }

    $notes = "";
    if (isset($_POST["notes"])) {
        $notes = trim($_POST["notes"]);
    }

    if ($loanId <= 0) {
        responseJson(false, "Invalid loan ID.", array(), 400);
    }

    if ($paymentAmount <= 0) {
        responseJson(false, "Please enter a valid payment amount.", array(), 400);
    }

    if ($paymentDate === "") {
        $paymentDate = date("Y-m-d");
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    try {
        $requestId = isset($_POST["payment_request_id"]) ? trim($_POST["payment_request_id"]) : "";

        if ($requestId !== "") {
            if (!isset($_SESSION["processed_payment_requests"])) {
                $_SESSION["processed_payment_requests"] = array();
            }

            if (isset($_SESSION["processed_payment_requests"][$requestId])) {
                responseJson(false, "This payment has already been submitted.", array(), 409);
            }
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            SELECT id, rfid_no, loan_amount, total_interest, amount_paid, status
            FROM loans
            WHERE id = :loan_id
            LIMIT 1
            FOR UPDATE
        ");
        $stmt->execute(array(":loan_id" => $loanId));
        $loan = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$loan) {
            $pdo->rollBack();
            responseJson(false, "Loan was not found.", array(), 404);
        }

        $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE loan_id = :loan_id");
        $stmt->execute(array(":loan_id" => $loanId));
        $actualAmountPaid = (float)$stmt->fetchColumn();

        $totalPayable   = round((float)$loan["loan_amount"] + (float)$loan["total_interest"], 2);
        $currentBalance = round($totalPayable - $actualAmountPaid, 2);

        if ($currentBalance < 0) {
            $currentBalance = 0;
        }

        $currentStatus = $currentBalance <= 0 ? "Paid" : $loan["status"];

        /* We remove total_payable and balance from the UPDATE because they are generated columns */
        $stmt = $pdo->prepare("
            UPDATE loans
            SET amount_paid = :amount_paid, status = :status, updated_at = NOW()
            WHERE id = :loan_id
        ");
        $stmt->execute(array(
            ":amount_paid" => $actualAmountPaid,
            ":status"      => $currentStatus,
            ":loan_id"     => $loanId
        ));

        if ($paymentAmount > $currentBalance) {
            $pdo->rollBack();
            responseJson(false, "Payment cannot be greater than the outstanding balance.", array("balance" => $currentBalance), 400);
        }

        $newAmountPaid = round($actualAmountPaid + $paymentAmount, 2);
        $newBalance    = round($totalPayable - $newAmountPaid, 2);

        if ($newBalance < 0) {
            $newBalance = 0;
        }

        $newStatus  = $newBalance <= 0 ? "Paid" : $loan["status"];
        $recordedBy = isset($_SESSION["user_id"]) ? (int)$_SESSION["user_id"] : null;

        $stmt = $pdo->prepare("
            INSERT INTO payments
            (loan_id, rfid_no, amount, payment_date, payment_method, notes, outstanding_before, outstanding_after, recorded_by)
            VALUES
            (:loan_id, :rfid_no, :amount, :payment_date, :payment_method, :notes, :outstanding_before, :outstanding_after, :recorded_by)
        ");
        $stmt->execute(array(
            ":loan_id"            => $loanId,
            ":rfid_no"            => $loan["rfid_no"],
            ":amount"             => $paymentAmount,
            ":payment_date"       => $paymentDate,
            ":payment_method"     => $paymentMethod,
            ":notes"              => $notes,
            ":outstanding_before" => $currentBalance,
            ":outstanding_after"  => $newBalance,
            ":recorded_by"        => $recordedBy
        ));

        /* We remove total_payable and balance from the UPDATE because they are generated columns */
        $stmt = $pdo->prepare("
            UPDATE loans
            SET amount_paid = :amount_paid, status = :status, updated_at = NOW()
            WHERE id = :loan_id
        ");
        $stmt->execute(array(
            ":amount_paid" => $newAmountPaid,
            ":status"      => $newStatus,
            ":loan_id"     => $loanId
        ));

        if ($requestId !== "") {
            $_SESSION["processed_payment_requests"][$requestId] = time();

            foreach ($_SESSION["processed_payment_requests"] as $key => $timestamp) {
                if ((time() - (int)$timestamp) > 3600) {
                    unset($_SESSION["processed_payment_requests"][$key]);
                }
            }
        }

        $pdo->commit();

        responseJson(true, "Payment saved successfully.", array(
            "loan_id"        => $loanId,
            "payment_amount" => $paymentAmount,
            "amount_paid"    => $newAmountPaid,
            "balance"        => $newBalance,
            "status"         => $newStatus
        ));
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        responseJson(false, "Database error while saving payment.", array(
            "error" => $e->getMessage()
        ), 500);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        responseJson(false, "Unable to save payment.", array(
            "error" => $e->getMessage()
        ), 500);
    }
}

/* RFID SCAN */
if ($action === "scan") {
    $rfid = isset($_GET["rfid"]) ? trim($_GET["rfid"]) : "";
    $rfid = preg_replace('/\s+/', '', $rfid);

    if ($rfid === "") {
        responseJson(true, "Empty RFID.", array("authorized" => false));
    }

    try {
        $stmt = $pdo->prepare("
            SELECT
                l.id AS loan_id,
                c.`Rfid no` AS rfid_no,
                c.`full name` AS full_name,
                l.loan_amount,
                l.interest_amount,
                l.total_payable,
                l.amount_paid,
                l.balance AS outstanding_balance,
                l.status AS loan_status,
                l.loan_term,
                l.start_date
            FROM client_info c
            LEFT JOIN loans l
                ON l.id = (
                    SELECT l2.id
                    FROM loans l2
                    WHERE REPLACE(l2.rfid_no, ' ', '') = REPLACE(c.`Rfid no`, ' ', '')
                      AND l2.balance > 0
                    ORDER BY l2.id DESC
                    LIMIT 1
                )
            WHERE REPLACE(c.`Rfid no`, ' ', '') = :rfid
            LIMIT 1
        ");
        $stmt->execute(array(":rfid" => $rfid));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $log = $pdo->prepare("INSERT INTO rfid_scans (rfid_no, result) VALUES (:rfid, 'Unauthorized')");
            $log->execute(array(":rfid" => $rfid));
            responseJson(true, "RFID not found.", array("authorized" => false));
        }

        $log = $pdo->prepare("INSERT INTO rfid_scans (rfid_no, result) VALUES (:rfid, 'Authorized')");
        $log->execute(array(":rfid" => $rfid));

        /* CALCULATE MISSING FIELDS FOR THE UI */
        $loanTermMonths        = 0;
        $monthlyPayment        = 0;
        $paymentsMade          = 0;
        $amountDueThisMonth    = 0;
        $balanceAfterThisMonth = 0;
        $nextDue               = "--";

        if ($row["loan_id"] !== null) {
            $loanTermMonths = (int)filter_var($row["loan_term"] ?? "0", FILTER_SANITIZE_NUMBER_INT);

            if ($loanTermMonths > 0) {
                /* =====================================================
                   ROUNDED UP — Monthly instalment
                   Before: round($row["total_payable"] / $loanTermMonths, 2)
                   After:  ceil(...) → rounds UP to the nearest peso.
                ===================================================== */
                $monthlyPayment = ceil($row["total_payable"] / $loanTermMonths);
            }

            $payStmt = $pdo->prepare("SELECT COUNT(*) FROM payments WHERE loan_id = :loan_id");
            $payStmt->execute(array(":loan_id" => $row["loan_id"]));
            $paymentsMade = (int)$payStmt->fetchColumn();

            if ($row["outstanding_balance"] > 0) {
                $amountDueThisMonth    = min($monthlyPayment, $row["outstanding_balance"]);
                $balanceAfterThisMonth = max(0, $row["outstanding_balance"] - $amountDueThisMonth);
            }

            if ($row["loan_status"] !== 'Paid' && !empty($row["start_date"])) {
                $startDate = new DateTime($row["start_date"]);
                $startDate->modify("+" . ($paymentsMade + 1) . " months");
                $nextDue = $startDate->format("Y-m-d");
            }
        }

        responseJson(true, "RFID found.", array(
            "authorized" => true,
            "data"       => array(
                "loan_id"                  => $row["loan_id"] !== null ? $row["loan_id"] : "",
                "rfid_no"                  => $row["rfid_no"],
                "full_name"                => $row["full_name"],
                "loan_amount"              => $row["loan_amount"] !== null ? $row["loan_amount"] : 0,
                "interest_amount"          => $row["interest_amount"] !== null ? $row["interest_amount"] : 0,
                "total_payable"            => $row["total_payable"] !== null ? $row["total_payable"] : 0,
                "amount_paid"              => $row["amount_paid"] !== null ? $row["amount_paid"] : 0,
                "outstanding_balance"      => $row["outstanding_balance"] !== null ? $row["outstanding_balance"] : 0,
                "loan_term"                => $row["loan_term"] ?? "--",
                "monthly_payment"          => $monthlyPayment,
                "payments_made"            => $paymentsMade,
                "amount_due_this_month"    => $amountDueThisMonth,
                "balance_after_this_month" => $balanceAfterThisMonth,
                "next_due"                 => $nextDue,
                "loan_status"              => !empty($row["loan_status"]) ? $row["loan_status"] : "No active loan"
            )
        ));
    } catch (PDOException $e) {
        responseJson(false, "Scan error.", array("error" => $e->getMessage()), 500);
    }
}

/* SCAN HISTORY */
if ($action === "scans") {
    try {
        $stmt = $pdo->query("
            SELECT id, rfid_no, result, scanned_at
            FROM rfid_scans
            ORDER BY id DESC
            LIMIT 10
        ");
        responseJson(true, "Scan history loaded.", array(
            "scans" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ));
    } catch (PDOException $e) {
        responseJson(false, "Unable to load scan history.", array(), 500);
    }
}

/* DASHBOARD */
if ($action === "dashboard") {
    try {
        /* TOTAL LOAN AMOUNT */
        $stmt = $pdo->query("SELECT COALESCE(SUM(loan_amount), 0) FROM loans");
        $totalLoanAmount = (float)$stmt->fetchColumn();

        /* ACTIVE LOANS */
        $stmt = $pdo->query("SELECT COUNT(*) FROM loans WHERE LOWER(TRIM(status)) = 'active'");
        $activeLoans = (int)$stmt->fetchColumn();

        /* OVERDUE LOANS */
        $stmt = $pdo->query("SELECT COUNT(*) FROM loans WHERE LOWER(TRIM(status)) NOT IN ('paid', 'completed')");
        $overdueLoans = (int)$stmt->fetchColumn();

        /* TOTAL COLLECTED */
        $stmt = $pdo->query("SELECT COALESCE(SUM(amount_paid), 0) FROM loans");
        $totalCollected = (float)$stmt->fetchColumn();

        /* RECENT LOANS */
        $stmt = $pdo->query("
            SELECT l.id, l.rfid_no, c.`full name` AS full_name, l.loan_amount, l.status, l.created_at
            FROM loans l
            LEFT JOIN client_info c ON l.rfid_no = c.`Rfid no`
            ORDER BY l.id DESC
            LIMIT 5
        ");
        $recentLoans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /* UPCOMING PAYMENTS */
        $stmt = $pdo->query("
            SELECT
                l.id, l.rfid_no, c.`full name` AS full_name, l.balance, l.total_payable, l.loan_term, l.start_date, l.status,
                (SELECT COUNT(*) FROM payments p WHERE p.loan_id = l.id) AS payments_made
            FROM loans l
            LEFT JOIN client_info c ON l.rfid_no = c.`Rfid no`
            WHERE l.balance > 0
              AND LOWER(TRIM(l.status)) NOT IN ('paid', 'completed')
            ORDER BY l.start_date ASC
            LIMIT 10
        ");
        $upcomingRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $upcomingPayments = array();

        foreach ($upcomingRows as $u) {
            $paymentsMade = (int)$u["payments_made"];

            if (!empty($u["start_date"])) {
                $dueDate = date("Y-m-d", strtotime($u["start_date"] . " +" . ($paymentsMade + 1) . " months"));
            } else {
                $dueDate = date("Y-m-d");
            }

            $upcomingPayments[] = array(
                "id"        => $u["id"],
                "rfid_no"   => $u["rfid_no"],
                "full_name" => $u["full_name"],
                "due_date"  => $dueDate,
                "balance"   => $u["balance"],
                "status"    => $u["status"]
            );
        }

        responseJson(true, "Dashboard loaded successfully.", array(
            "stats" => array(
                "total_loan_amount" => $totalLoanAmount,
                "active_loans"      => $activeLoans,
                "overdue_loans"     => $overdueLoans,
                "total_collected"   => $totalCollected
            ),
            "recent"   => $recentLoans,
            "upcoming" => $upcomingPayments
        ));
    } catch (PDOException $e) {
        responseJson(false, "Unable to load dashboard.", array(
            "error" => $e->getMessage()
        ), 500);
    }
}

if ($action === "payments") {
    try {
        $sql = "
            SELECT
                p.id, p.loan_id, p.rfid_no,
                c.`full name` AS full_name,
                p.amount, p.payment_date, p.payment_method, p.notes,
                p.outstanding_before, p.outstanding_after,
                p.recorded_by,
                u.username AS recorded_by_name,
                p.created_at
            FROM payments p
            LEFT JOIN client_info c
                ON REPLACE(p.rfid_no, ' ', '') = REPLACE(c.`Rfid no`, ' ', '')
            LEFT JOIN users u
                ON u.id = p.recorded_by
            ORDER BY p.created_at DESC, p.id DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        responseJson(true, "Payments loaded successfully.", array(
            "payments" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ));
    } catch (PDOException $e) {
        responseJson(false, "Unable to retrieve payments.", array(
            "error" => $e->getMessage()
        ), 500);
    }
}

/* =====================================================
   REPORTS
   ===================================================== */

function getReportRange() {
    $from = trim(isset($_GET["from"]) ? $_GET["from"] : "");
    $to   = trim(isset($_GET["to"])   ? $_GET["to"]   : "");
    if ($from === "") $from = date("Y-m-01");
    if ($to   === "") $to   = date("Y-m-d");
    return array($from, $to);
}

/* COLLECTIONS (payments received in a date range) */
if ($action === "report_collections") {
    list($from, $to) = getReportRange();
    try {
        $stmt = $pdo->prepare("
            SELECT p.id, p.loan_id, p.rfid_no,
                   c.`full name` AS full_name,
                   p.amount, p.payment_date, p.payment_method, p.notes,
                   p.outstanding_before, p.outstanding_after
            FROM payments p
            LEFT JOIN client_info c
                ON REPLACE(p.rfid_no, ' ', '') = REPLACE(c.`Rfid no`, ' ', '')
            WHERE p.payment_date BETWEEN :from AND :to
            ORDER BY p.payment_date ASC, p.id ASC
        ");
        $stmt->execute(array(":from" => $from, ":to" => $to));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = 0.0;
        foreach ($rows as $r) $total += (float)$r["amount"];

        responseJson(true, "Collections report loaded.", array(
            "report"  => "collections",
            "from"    => $from,
            "to"      => $to,
            "rows"    => $rows,
            "summary" => array("count" => count($rows), "total" => $total)
        ));
    } catch (PDOException $e) {
        responseJson(false, "Unable to load collections report.", array("error" => $e->getMessage()), 500);
    }
}

/* LOANS RELEASED (loans started in a date range) */
if ($action === "report_loans") {
    list($from, $to) = getReportRange();
    try {
        $stmt = $pdo->prepare("
            SELECT l.id, l.rfid_no, c.`full name` AS full_name,
                   l.loan_amount, l.interest_rate, l.interest_amount,
                   l.total_interest, l.total_payable, l.amount_paid,
                   l.balance, l.loan_term, l.start_date, l.status
            FROM loans l
            LEFT JOIN client_info c ON l.rfid_no = c.`Rfid no`
            WHERE l.start_date BETWEEN :from AND :to
            ORDER BY l.start_date ASC, l.id ASC
        ");
        $stmt->execute(array(":from" => $from, ":to" => $to));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalAmount = 0.0;
        $totalInterest = 0.0;
        $totalPayable = 0.0;
        foreach ($rows as $r) {
            $totalAmount   += (float)$r["loan_amount"];
            $totalInterest += (float)$r["total_interest"];
            $totalPayable  += (float)$r["total_payable"];
        }

        responseJson(true, "Loans report loaded.", array(
            "report"  => "loans",
            "from"    => $from,
            "to"      => $to,
            "rows"    => $rows,
            "summary" => array(
                "count"          => count($rows),
                "total_amount"   => $totalAmount,
                "total_interest" => $totalInterest,
                "total"          => $totalPayable
            )
        ));
    } catch (PDOException $e) {
        responseJson(false, "Unable to load loans report.", array("error" => $e->getMessage()), 500);
    }
}

/* INTEREST EARNED (interest portion of payments received in range) */
if ($action === "report_interest") {
    list($from, $to) = getReportRange();
    try {
        $stmt = $pdo->prepare("
            SELECT p.id, p.loan_id, p.rfid_no,
                   c.`full name` AS full_name,
                   p.amount, p.payment_date,
                   l.total_interest, l.total_payable
            FROM payments p
            LEFT JOIN loans l ON p.loan_id = l.id
            LEFT JOIN client_info c
                ON REPLACE(p.rfid_no, ' ', '') = REPLACE(c.`Rfid no`, ' ', '')
            WHERE p.payment_date BETWEEN :from AND :to
            ORDER BY p.payment_date ASC, p.id ASC
        ");
        $stmt->execute(array(":from" => $from, ":to" => $to));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $out = array();
        $totalInterest = 0.0;
        $totalPayment  = 0.0;

        foreach ($rows as $r) {
            $amount = (float)$r["amount"];
            $tp     = (float)$r["total_payable"];
            $ti     = (float)$r["total_interest"];
            $interestPortion = $tp > 0 ? round($amount * ($ti / $tp), 2) : 0.0;

            $totalInterest += $interestPortion;
            $totalPayment  += $amount;

            $out[] = array(
                "payment_date" => $r["payment_date"],
                "full_name"    => $r["full_name"],
                "rfid_no"      => $r["rfid_no"],
                "loan_id"      => $r["loan_id"],
                "amount"       => $amount,
                "interest"     => $interestPortion,
                "principal"    => round($amount - $interestPortion, 2)
            );
        }

        responseJson(true, "Interest report loaded.", array(
            "report"  => "interest",
            "from"    => $from,
            "to"      => $to,
            "rows"    => $out,
            "summary" => array(
                "count"          => count($out),
                "total"          => $totalPayment,
                "total_interest" => $totalInterest
            )
        ));
    } catch (PDOException $e) {
        responseJson(false, "Unable to load interest report.", array("error" => $e->getMessage()), 500);
    }
}

/* OUTSTANDING BALANCES (all unpaid loans) */
if ($action === "report_outstanding") {
    try {
        $stmt = $pdo->prepare("
            SELECT l.id, l.rfid_no, c.`full name` AS full_name,
                   l.loan_amount, l.total_payable, l.amount_paid, l.balance,
                   l.loan_term, l.start_date, l.status
            FROM loans l
            LEFT JOIN client_info c ON l.rfid_no = c.`Rfid no`
            WHERE l.balance > 0
              AND LOWER(TRIM(l.status)) NOT IN ('paid', 'completed')
            ORDER BY l.balance DESC, l.id DESC
        ");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalBalance = 0.0;
        foreach ($rows as $r) $totalBalance += (float)$r["balance"];

        responseJson(true, "Outstanding report loaded.", array(
            "report"  => "outstanding",
            "rows"    => $rows,
            "summary" => array("count" => count($rows), "total" => $totalBalance)
        ));
    } catch (PDOException $e) {
        responseJson(false, "Unable to load outstanding report.", array("error" => $e->getMessage()), 500);
    }
}

/* OVERDUE ACCOUNTS */
if ($action === "report_overdue") {
    try {
        $stmt = $pdo->prepare("
            SELECT l.id, l.rfid_no, c.`full name` AS full_name,
                   l.loan_amount, l.total_payable, l.amount_paid, l.balance,
                   l.loan_term, l.start_date, l.status,
                   (SELECT COUNT(*) FROM payments p WHERE p.loan_id = l.id) AS payments_made
            FROM loans l
            LEFT JOIN client_info c ON l.rfid_no = c.`Rfid no`
            WHERE l.balance > 0
              AND LOWER(TRIM(l.status)) NOT IN ('paid', 'completed')
            ORDER BY l.start_date ASC
        ");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $today = new DateTime();
        $today->setTime(0, 0, 0);

        $out = array();
        $totalOverdue = 0.0;

        foreach ($rows as $r) {
            if (empty($r["start_date"])) continue;

            $paymentsMade = (int)$r["payments_made"];
            $start        = new DateTime($r["start_date"]);
            $due          = clone $start;
            $due->modify("+" . ($paymentsMade + 1) . " months");

            $daysOverdue = 0;
            if ($due < $today) {
                $diff        = $today->diff($due);
                $daysOverdue = (int)$diff->days;
            }

            if ($daysOverdue > 0) {
                $r["next_due"]     = $due->format("Y-m-d");
                $r["days_overdue"] = $daysOverdue;
                $out[]             = $r;
                $totalOverdue     += (float)$r["balance"];
            }
        }

        usort($out, function ($a, $b) {
            return $b["days_overdue"] - $a["days_overdue"];
        });

        responseJson(true, "Overdue report loaded.", array(
            "report"  => "overdue",
            "rows"    => $out,
            "summary" => array("count" => count($out), "total" => $totalOverdue)
        ));
    } catch (PDOException $e) {
        responseJson(false, "Unable to load overdue report.", array("error" => $e->getMessage()), 500);
    }
}

/* BORROWER STATEMENT */
if ($action === "report_statement") {
    $rfid = trim(isset($_GET["rfid"]) ? $_GET["rfid"] : "");
    if ($rfid === "") {
        responseJson(false, "Borrower RFID is required.", array(), 400);
    }

    try {
        $stmt = $pdo->prepare("
            SELECT `Rfid no`, `full name`, `adres`, `cont no`, `gend`, `occup`, `dtreg`
            FROM client_info
            WHERE REPLACE(`Rfid no`, ' ', '') = REPLACE(:rfid, ' ', '')
            LIMIT 1
        ");
        $stmt->execute(array(":rfid" => $rfid));
        $borrower = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$borrower) {
            responseJson(false, "Borrower not found.", array(), 404);
        }

        $stmt = $pdo->prepare("
            SELECT l.id, l.rfid_no, l.loan_amount, l.interest_rate,
                   l.interest_amount, l.total_interest, l.total_payable,
                   l.amount_paid, l.balance, l.loan_term, l.start_date, l.status,
                   (SELECT COUNT(*) FROM payments p WHERE p.loan_id = l.id) AS payments_made
            FROM loans l
            WHERE REPLACE(l.rfid_no, ' ', '') = REPLACE(:rfid, ' ', '')
            ORDER BY l.id ASC
        ");
        $stmt->execute(array(":rfid" => $rfid));
        $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("
            SELECT p.id, p.loan_id, p.amount, p.payment_date,
                   p.payment_method, p.notes,
                   p.outstanding_before, p.outstanding_after
            FROM payments p
            WHERE REPLACE(p.rfid_no, ' ', '') = REPLACE(:rfid, ' ', '')
            ORDER BY p.payment_date ASC, p.id ASC
        ");
        $stmt->execute(array(":rfid" => $rfid));
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalBorrowed = 0.0;
        $totalPaid     = 0.0;
        $totalBalance  = 0.0;
        foreach ($loans as $l) {
            $totalBorrowed += (float)$l["loan_amount"];
            $totalPaid     += (float)$l["amount_paid"];
            $totalBalance  += (float)$l["balance"];
        }

        responseJson(true, "Statement loaded.", array(
            "report"   => "statement",
            "borrower" => $borrower,
            "loans"    => $loans,
            "payments" => $payments,
            "summary"  => array(
                "loans"          => count($loans),
                "total_borrowed" => $totalBorrowed,
                "total_paid"     => $totalPaid,
                "total_balance"  => $totalBalance
            )
        ));
    } catch (PDOException $e) {
        responseJson(false, "Unable to load statement.", array("error" => $e->getMessage()), 500);
    }
}

/* INVALID ACTION */
responseJson(false, "Invalid API action: " . $action, array(), 400);

?>