<?php
require_once __DIR__ . "/auth.php";
requireLogin();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RFID Money Lending Management System - RFID Scanner</title>

<style>
* { box-sizing: border-box; }

html, body { margin:0; padding:0; width:100%; min-height:100%; background:#f4f7f5; }

body {
    font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    color: #1c2b27;
    min-height: 100dvh;
    overflow-y: auto;
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
}

body::-webkit-scrollbar { width: 7px; }
body::-webkit-scrollbar-track { background: #f4f7f5; }
body::-webkit-scrollbar-thumb { background: #b7c5c0; border-radius: 10px; }

#sidebar.open { transform: translateX(0) !important; }
#overlay.open { opacity: 1 !important; visibility: visible !important; }

#rfidInput {
    width: 100%;
    padding: 12px;
    border: 1px solid #dce5e1;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    background: #fafcfb;
    color: #1c2b27;
    text-align: center;
}

#rfidInput:focus { border-color: #1a8a63; box-shadow: 0 0 0 3px rgba(26,138,99,.10); }

.field { width: 100%; margin-bottom: 14px; }

.field-label {
    display: block;
    font-size: 12.5px;
    font-weight: 700;
    color: #3c4b47;
    margin-bottom: 6px;
}

.input-control {
    width: 100%;
    min-height: 44px;
    padding: 12px 13px;
    border: 1px solid #e6ece9;
    border-radius: 9px;
    font-size: 13.5px;
    color: #1c2b27;
    background: #fff;
    font-family: inherit;
}

.input-control:focus {
    outline: none;
    border-color: #1a8a63 !important;
    box-shadow: 0 0 0 2px rgba(26,138,99,.08);
}

#successModal {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100dvh;
    background: rgba(13,59,46,.45);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity .2s ease, visibility .2s ease;
}

#successModal.open { opacity: 1; visibility: visible; pointer-events: auto; }

.success-card {
    width: 100%;
    max-width: 360px;
    background: #fff;
    border-radius: 14px;
    padding: 24px 20px 20px;
    text-align: center;
    box-shadow: 0 12px 40px rgba(15,74,56,.25);
}

.success-icon {
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: #e3f3ea;
    color: #146c4f;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 13px;
    font-size: 28px;
    font-weight: 800;
}

.success-title { font-size: 18px; font-weight: 800; color: #0f4a38; margin-bottom: 5px; }
.success-message { font-size: 12.5px; color: #6b7a76; line-height: 1.5; margin-bottom: 18px; }

.success-button {
    width: 100%;
    border: none;
    background: #146c4f;
    color: #fff;
    padding: 12px;
    border-radius: 9px;
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
}

.success-button:hover { background: #0f5b43; }
</style>
</head>

<body>

<!-- SIDEBAR -->
<aside id="sidebar" style="position:fixed; top:0; left:0; bottom:0; width:min(250px,82vw); background:linear-gradient(180deg,#0d3b2e 0%,#0f4a38 100%); transform:translateX(-100%); transition:transform .25s; z-index:200; display:flex; flex-direction:column; padding:20px 14px;">

    <div style="display:flex; justify-content:flex-end;">
        <button id="closeMenu" type="button" style="background:none; border:none; color:#fff; font-size:18px; cursor:pointer;">✕</button>
    </div>

    <div style="display:flex; justify-content:center; padding:4px 6px 24px;">
        <img src="image/logo.png" alt="Logo" style="width:120px; height:120px; border-radius:20px; object-fit:cover;">
    </div>

    <ul style="list-style:none; margin:6px 0 0; padding:0; display:flex; flex-direction:column; gap:4px; flex:1;">
        <li><a href="dashboard.php" style="display:flex; padding:12px 13px; border-radius:9px; color:rgba(255,255,255,.82); text-decoration:none; font-size:14px;">Home</a></li>
        <li><a href="borrowers.php" style="display:flex; padding:12px 13px; border-radius:9px; color:rgba(255,255,255,.82); text-decoration:none; font-size:14px;">Borrowers</a></li>
        <li><a href="loan.php" style="display:flex; padding:12px 13px; border-radius:9px; color:rgba(255,255,255,.82); text-decoration:none; font-size:14px;">Loans</a></li>
        <li><a href="payment.php" style="display:flex; padding:12px 13px; border-radius:9px; color:rgba(255,255,255,.82); text-decoration:none; font-size:14px;">Payments</a></li>
        <li><a href="report.php" style="display:flex; padding:12px 13px; border-radius:9px; color:rgba(255,255,255,.82); text-decoration:none; font-size:14px;">Reports</a></li>
        <li><a href="scanner.php" style="display:flex; padding:12px 13px; border-radius:9px; background:#1f9e6f; color:#fff; text-decoration:none; font-size:14px; font-weight:700;">RFID Scanner</a></li>
        <li><a href="logout.php" style="display:flex; padding:12px 13px; border-radius:9px; color:rgba(255,255,255,.82); text-decoration:none; font-size:14px;">Logout</a></li>
    </ul>
</aside>

<!-- OVERLAY -->
<button id="overlay" type="button" style="position:fixed; top:0; right:0; bottom:0; left:0; width:100%; height:100%; border:none; background:rgba(13,59,46,.45); z-index:150; opacity:0; visibility:hidden; cursor:pointer;"></button>

<!-- TOP BAR -->
<div style="background:#fff; border-bottom:1px solid #e6ece9; display:flex; align-items:center; gap:14px; padding:14px 16px; height:64px;">
    <button id="menuButton" type="button" style="background:none; border:none; color:#3c4b47; font-size:20px; cursor:pointer;">☰</button>
    <h1 style="font-size:17px; margin:0; font-weight:700; flex:1;">RFID Scanner</h1>
</div>

<!-- CONTENT -->
<div style="padding:16px 14px 40px; width:100%;">

    <!-- SCAN CARD -->
    <section style="background:#fff; border-radius:10px; box-shadow:0 1px 3px rgba(16,44,34,.06); padding:16px; margin-bottom:16px;">

        <div style="text-align:center; padding-bottom:8px;">
            <div style="font-size:18px; font-weight:700; color:#0f4a38;">Scan RFID Card</div>
            <div style="font-size:13px; color:#6b7a76; margin-top:5px;">Hold the card near the reader</div>
        </div>

        <div style="max-width:420px; margin:10px auto 15px;">
            <input type="text" id="rfidInput" placeholder="Tap RFID card here" autocomplete="off" inputmode="none">
            <div style="text-align:center; font-size:11px; color:#8a9793; margin-top:6px;">The RFID reader will enter the card number automatically.</div>
        </div>

        <div style="background:#f0f8f3; border-radius:8px; padding:12px; display:flex; align-items:center; gap:10px; color:#146c4f;">
            <div style="width:34px; height:34px; border-radius:50%; background:#e3f3ea; display:flex; align-items:center; justify-content:center; flex-shrink:0;">💡</div>
            <div style="font-size:12px; line-height:1.45;">
                <strong>Tip:</strong> Place the RFID card near the reader and hold it steady until the card number appears.
            </div>
        </div>

    </section>

    <!-- SCAN RESULT -->
    <section style="background:#eaf6ef; border:1px solid #d4e9de; border-radius:10px; box-shadow:0 1px 3px rgba(16,44,34,.06); padding:16px; margin-bottom:16px;">
        <div style="padding-bottom:12px; border-bottom:2px solid #1a8a63; margin-bottom:16px; font-size:15px; font-weight:700; color:#0f4a38;">Scan Result</div>

        <!-- BORROWER -->
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
            <div id="borrowerInitials" style="width:50px; height:50px; border-radius:50%; background:#e3f3ea; display:flex; align-items:center; justify-content:center; color:#146c4f; font-size:15px; font-weight:700; flex-shrink:0;">--</div>
            <div style="flex:1; min-width:0;">
                <div id="borrowerName" style="font-size:15px; font-weight:700;">No borrower scanned</div>
                <div style="font-size:12px; color:#6b7a76; margin-top:2px;">
                    RFID Card No.: <strong id="rfidNumber" style="color:#1c2b27;">--</strong>
                </div>
            </div>
            <span id="authStatus" style="padding:6px 10px; border-radius:20px; background:#f0f2f1; color:#6b7a76; font-size:10px; font-weight:700;">Waiting</span>
        </div>

        <!-- AMOUNT DUE THIS MONTH -->
        <div style="background:#e3f3ea; border-radius:9px; padding:14px 16px; margin-bottom:16px; border-left:4px solid #1f9e6f;">
            <div style="font-size:11px; font-weight:700; color:#146c4f; text-transform:uppercase; letter-spacing:.4px; margin-bottom:4px;">Amount Due This Month</div>
            <div id="amountDueThisMonth" style="font-size:22px; font-weight:800; color:#0f4a38;">₱0.00</div>
            <div id="amountDueNote" style="font-size:11px; color:#6b7a76; margin-top:4px;">Scan a card to see the amount due.</div>
        </div>

        <!-- KEY NUMBERS GRID -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:16px;">

            <div style="background:#f4f7f5; border-radius:9px; padding:10px 12px;">
                <div style="font-size:10px; color:#7a8985; font-weight:700; text-transform:uppercase; letter-spacing:.3px;">Total Loan Amount</div>
                <div id="totalLoanAmount" style="font-size:14px; font-weight:800; color:#1c2b27; margin-top:3px;">₱0.00</div>
            </div>

            <div style="background:#f4f7f5; border-radius:9px; padding:10px 12px;">
                <div style="font-size:10px; color:#7a8985; font-weight:700; text-transform:uppercase; letter-spacing:.3px;">Total Paid</div>
                <div id="totalPaid" style="font-size:14px; font-weight:800; color:#146c4f; margin-top:3px;">₱0.00</div>
            </div>

            <div style="background:#f4f7f5; border-radius:9px; padding:10px 12px;">
                <div style="font-size:10px; color:#7a8985; font-weight:700; text-transform:uppercase; letter-spacing:.3px;">Outstanding Balance</div>
                <div id="outstandingBalance" style="font-size:14px; font-weight:800; color:#e0722e; margin-top:3px;">₱0.00</div>
            </div>

            <div style="background:#f4f7f5; border-radius:9px; padding:10px 12px;">
                <div style="font-size:10px; color:#7a8985; font-weight:700; text-transform:uppercase; letter-spacing:.3px;">Monthly Payment</div>
                <div id="monthlyPayment" style="font-size:14px; font-weight:800; color:#1c2b27; margin-top:3px;">₱0.00</div>
            </div>

            <div style="background:#f4f7f5; border-radius:9px; padding:10px 12px;">
                <div style="font-size:10px; color:#7a8985; font-weight:700; text-transform:uppercase; letter-spacing:.3px;">Balance After This Month</div>
                <div id="balanceAfterThisMonth" style="font-size:14px; font-weight:800; color:#1c2b27; margin-top:3px;">₱0.00</div>
            </div>

            <div style="background:#f4f7f5; border-radius:9px; padding:10px 12px;">
                <div style="font-size:10px; color:#7a8985; font-weight:700; text-transform:uppercase; letter-spacing:.3px;">Payments Made</div>
                <div id="paymentsMade" style="font-size:14px; font-weight:800; color:#1c2b27; margin-top:3px;">0</div>
            </div>

        </div>

        <!-- OTHER DETAILS -->
        <div style="background:#f4f7f5; border-radius:9px; padding:12px 14px; margin-bottom:16px;">
            <div style="display:flex; justify-content:space-between; padding:5px 0; font-size:12.5px;">
                <span style="color:#7a8985;">Loan Term</span>
                <strong id="loanTerm" style="color:#1c2b27;">--</strong>
            </div>
            <div style="display:flex; justify-content:space-between; padding:5px 0; font-size:12.5px;">
                <span style="color:#7a8985;">Next Due</span>
                <strong id="nextDue" style="color:#1c2b27;">--</strong>
            </div>
            <div style="display:flex; justify-content:space-between; padding:5px 0; font-size:12.5px;">
                <span style="color:#7a8985;">Loan Status</span>
                <strong id="loanStatus" style="color:#146c4f;">--</strong>
            </div>
        </div>

        <!-- PAYMENT CONFIRMATION - appears after a successful scan; amount is automatic, only the payment date is editable -->
        <div id="paymentFormSection" style="display:none; padding-top:16px; border-top:2px solid #e6ece9;">

            <div style="font-size:14px; font-weight:700; color:#0f4a38; margin-bottom:14px;">Confirm Payment</div>

            <div style="background:#f4faf7; border:1px solid #e3f3ea; border-radius:9px; padding:12px 14px; margin-bottom:14px; display:flex; justify-content:space-between; align-items:center;">
                <span style="font-size:12.5px; color:#52625d; font-weight:700;">Amount to Pay</span>
                <strong id="confirmAmount" style="font-size:16px; color:#0f4a38; font-weight:800;">₱0.00</strong>
            </div>

            <div class="field" style="margin-bottom:18px;">
                <label class="field-label" for="paymentDate">Payment Date</label>
                <input id="paymentDate" type="date" class="input-control">
            </div>

            <button type="button" id="savePaymentButton" style="width:100%; background:#146c4f; color:#fff; border:none; padding:14px; border-radius:9px; font-size:14.5px; font-weight:700; cursor:pointer; margin-bottom:10px;">Save Payment</button>

        </div>

        <!-- VIEW BORROWER PROFILE -->
        <a href="borrowers.php" style="display:block; text-align:center; background:#fff; color:#3c4b47; border:1px solid #e6ece9; text-decoration:none; font-size:13.5px; font-weight:600; padding:11px; border-radius:9px; margin-top:10px;">View Borrower Profile</a>

    </section>

    <!-- RECENT SCANS -->
    <section style="background:#fff; border-radius:10px; box-shadow:0 1px 3px rgba(16,44,34,.06); padding:16px;">

        <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:2px solid #1a8a63;">
            <div style="font-size:15px; font-weight:700; color:#0f4a38;">Recent Scans</div>
        </div>

        <div id="recentScans"></div>

    </section>

</div>

<!-- SUCCESS MODAL -->
<div id="successModal">
    <div class="success-card">
        <div class="success-icon">✓</div>
        <div class="success-title">Payment Successful!</div>
        <div class="success-message" id="successMessage">The payment has been recorded successfully.</div>
        <button id="successDone" type="button" class="success-button">Done</button>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    /* SIDEBAR */
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("overlay");
    const menu = document.getElementById("menuButton");
    const close = document.getElementById("closeMenu");

    menu.onclick = function () { sidebar.classList.add("open"); overlay.classList.add("open"); };
    function shut() { sidebar.classList.remove("open"); overlay.classList.remove("open"); }

    close.onclick = shut;
    overlay.onclick = shut;

    /* ELEMENTS */
    const rfidInput = document.getElementById("rfidInput");
    const recent = document.getElementById("recentScans");
    const paymentFormSection = document.getElementById("paymentFormSection");
    const paymentDate = document.getElementById("paymentDate");
    const confirmAmount = document.getElementById("confirmAmount");
    const savePaymentButton = document.getElementById("savePaymentButton");
    const successModal = document.getElementById("successModal");

    /* STATE */
    let currentLoanId = null;
    let currentAmountDue = 0;
    let currentOutstanding = 0;
    let isSubmitting = false;

    /* RFID -> borrower name cache (so recent scans can show names even when the scans API does not return them) */
       /* RFID -> borrower name cache.
       Persisted in localStorage so names survive page refreshes and re-opens. */
    const NAME_CACHE_KEY = "rfidNameCache_v1";

    function loadNameCache() {
        try {
            const parsed = JSON.parse(localStorage.getItem(NAME_CACHE_KEY) || "{}");
            return (parsed && typeof parsed === "object" && !Array.isArray(parsed)) ? parsed : {};
        } catch (e) {
            return {};
        }
    }

    const nameCache = loadNameCache();

    function saveNameCache() {
        try {
            /* Cap the cache so it can never grow without bound */
            const keys = Object.keys(nameCache);
            if (keys.length > 500) {
                keys.slice(0, keys.length - 500).forEach(function (k) { delete nameCache[k]; });
            }
            localStorage.setItem(NAME_CACHE_KEY, JSON.stringify(nameCache));
        } catch (e) {
            /* localStorage may be disabled or full - scanning still works, names just won't persist */
        }
    }

    function cacheName(rfid, name) {
        rfid = String(rfid || "").trim();
        name = String(name || "").trim();
        if (!rfid || !name) return;
        if (nameCache[rfid] === name) return;
        nameCache[rfid] = name;
        saveNameCache();
    }

    /* HELPERS */
    function esc(value) {
        return String(value ?? "").replace(/[&<>"']/g, function (m) {
            return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[m];
        });
    }

    function money(value) {
        return Number(value || 0).toLocaleString("en-PH", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function setMoney(id, value) {
        document.getElementById(id).textContent = "₱" + money(value);
    }

    function todayISO() { return new Date().toISOString().slice(0, 10); }

    function newRequestId() {
        return Date.now().toString(36) + "-" + Math.random().toString(36).slice(2);
    }

    function initialsOf(name) {
        return String(name || "").trim().split(/\s+/).filter(Boolean).map(function (word) {
            return word[0];
        }).slice(0, 2).join("").toUpperCase();
    }

    /* RESET PAYMENT FORM */
    function resetPaymentForm() {
        paymentFormSection.style.display = "none";
        paymentDate.value = todayISO();
        confirmAmount.textContent = "₱0.00";
        currentLoanId = null;
        currentAmountDue = 0;
        currentOutstanding = 0;
        isSubmitting = false;
        savePaymentButton.disabled = false;
        savePaymentButton.textContent = "Save Payment";
        savePaymentButton.style.opacity = "1";
        savePaymentButton.style.cursor = "pointer";
    }

    /* RESET SCAN RESULT */
    function resetScanResult() {
        document.getElementById("authStatus").textContent = "Waiting";
        document.getElementById("authStatus").style.color = "#6b7a76";
        document.getElementById("borrowerName").textContent = "No borrower scanned";
        document.getElementById("borrowerInitials").textContent = "--";
        document.getElementById("rfidNumber").textContent = "--";
        document.getElementById("amountDueThisMonth").textContent = "₱0.00";
        document.getElementById("amountDueNote").textContent = "Scan a card to see the amount due.";
        setMoney("totalLoanAmount", 0);
        setMoney("totalPaid", 0);
        setMoney("outstandingBalance", 0);
        setMoney("monthlyPayment", 0);
        setMoney("balanceAfterThisMonth", 0);
        document.getElementById("paymentsMade").textContent = "0";
        document.getElementById("loanTerm").textContent = "--";
        document.getElementById("nextDue").textContent = "--";
        document.getElementById("loanStatus").textContent = "--";
        resetPaymentForm();
    }

    /* SCAN ACTUAL RFID */
    function scan(rfid) {
        rfid = String(rfid || "").trim();
        if (!rfid) return;

        rfid = rfid.replace(/\s+/g, "");
        document.getElementById("rfidNumber").textContent = rfid;
        document.getElementById("authStatus").textContent = "Checking...";
        document.getElementById("authStatus").style.color = "#6b7a76";

        fetch("api.php?action=scan&rfid=" + encodeURIComponent(rfid) + "&_=" + Date.now(), { cache: "no-store" })
            .then(function (response) {
                if (!response.ok) throw new Error("Server error");
                return response.json();
            })
            .then(function (data) {
                if (data.authorized) {
                    const x = data.data || {};

                    /* Remember this card's owner for Recent Scans */
                    cacheName(rfid, x.full_name);

                    document.getElementById("authStatus").textContent = "Authorized";
                    document.getElementById("authStatus").style.color = "#1f9e6f";
                    document.getElementById("borrowerName").textContent = x.full_name || "Unknown";
                    document.getElementById("borrowerInitials").textContent = initialsOf(x.full_name) || "?";

                    /* MONTHLY DUE */
                    const monthlyDue = Number(x.amount_due_this_month || 0);
                    document.getElementById("amountDueThisMonth").textContent = "₱" + money(monthlyDue);
                    document.getElementById("amountDueNote").textContent = monthlyDue > 0
                        ? "Due on " + (x.next_due || "the next scheduled date")
                        : "This loan is fully paid.";

                    /* KEY NUMBERS */
                    setMoney("totalLoanAmount", x.loan_amount);
                    setMoney("totalPaid", x.amount_paid);
                    setMoney("outstandingBalance", x.outstanding_balance);
                    setMoney("monthlyPayment", x.monthly_payment);
                    setMoney("balanceAfterThisMonth", x.balance_after_this_month);
                    document.getElementById("paymentsMade").textContent = x.payments_made || "0";

                    /* OTHER DETAILS */
                    document.getElementById("loanTerm").textContent = x.loan_term || "--";
                    document.getElementById("nextDue").textContent = x.next_due || "--";
                    document.getElementById("loanStatus").textContent = x.loan_status || "No active loan";

                    /* PAYMENT CONFIRMATION - amount is automatic */
                    currentLoanId = x.loan_id || null;
                    currentOutstanding = Number(x.outstanding_balance || 0);
                    currentAmountDue = monthlyDue;

                    if (currentLoanId && currentOutstanding > 0 && currentAmountDue > 0) {
                        paymentFormSection.style.display = "block";
                        confirmAmount.textContent = "₱" + money(currentAmountDue);
                        paymentDate.value = todayISO();
                    } else {
                        resetPaymentForm();
                    }
                } else {
                    /* UNAUTHORIZED */
                    document.getElementById("authStatus").textContent = "Unauthorized";
                    document.getElementById("authStatus").style.color = "#e14b4b";
                    document.getElementById("borrowerName").textContent = "Unknown RFID Card";
                    document.getElementById("borrowerInitials").textContent = "?";
                    document.getElementById("amountDueThisMonth").textContent = "₱0.00";
                    document.getElementById("amountDueNote").textContent = "Card not registered.";
                    setMoney("totalLoanAmount", 0);
                    setMoney("totalPaid", 0);
                    setMoney("outstandingBalance", 0);
                    setMoney("monthlyPayment", 0);
                    setMoney("balanceAfterThisMonth", 0);
                    document.getElementById("paymentsMade").textContent = "0";
                    document.getElementById("loanTerm").textContent = "--";
                    document.getElementById("nextDue").textContent = "--";
                    document.getElementById("loanStatus").textContent = "Unauthorized";
                    resetPaymentForm();
                }
                loadRecent();
            })
            .catch(function (error) {
                console.error(error);
                document.getElementById("authStatus").textContent = "Error";
                document.getElementById("authStatus").style.color = "#e14b4b";
            });
    }

    /* RFID READER INPUT - Enter key */
    rfidInput.addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            const rfid = rfidInput.value.trim();
            if (rfid !== "") {
                scan(rfid);
                rfidInput.value = "";
            }
        }
    });

    /* RFID READER INPUT - auto-scan after short pause */
    let scanTimer = null;
    rfidInput.addEventListener("input", function () {
        clearTimeout(scanTimer);
        scanTimer = setTimeout(function () {
            const rfid = rfidInput.value.trim();
            if (rfid.length >= 4) {
                scan(rfid);
                rfidInput.value = "";
            }
        }, 150);
    });

    setTimeout(function () { rfidInput.focus(); }, 300);

    /* SAVE PAYMENT - amount and method are automatic; only the date is collected from the user */
    savePaymentButton.onclick = function () {
        if (isSubmitting) return;

        if (!currentLoanId) {
            alert("No active loan was found for this borrower.");
            return;
        }

        if (currentAmountDue <= 0) {
            alert("There is no amount due for this loan.");
            return;
        }

        if (currentAmountDue > currentOutstanding) {
            alert("The amount due is greater than the outstanding balance.");
            return;
        }

        const date = paymentDate.value || todayISO();

        /* LOCK */
        isSubmitting = true;
        savePaymentButton.disabled = true;
        savePaymentButton.style.opacity = "0.7";
        savePaymentButton.style.cursor = "not-allowed";
        savePaymentButton.textContent = "Saving...";

        /* BUILD FORM DATA */
        const fd = new FormData();
        fd.append("loan_id", currentLoanId);
        fd.append("payment_amount", currentAmountDue.toFixed(2));
        fd.append("payment_date", date);
        fd.append("payment_method", "cash");
        fd.append("notes", "");
        fd.append("payment_request_id", newRequestId());

        fetch("api.php?action=save_payment", { method: "POST", body: fd })
            .then(function (response) { return response.json(); })
            .then(function (d) {
                if (!d.success) {
                    alert((d.message || "Unable to save payment.") + (d.error ? "\n\nDetails: " + d.error : ""));
                    isSubmitting = false;
                    savePaymentButton.disabled = false;
                    savePaymentButton.style.opacity = "1";
                    savePaymentButton.style.cursor = "pointer";
                    savePaymentButton.textContent = "Save Payment";
                    return;
                }

                /* SUCCESS */
                document.getElementById("successMessage").textContent =
                    "Payment of ₱" + money(currentAmountDue) +
                    " recorded successfully. New balance: ₱" + money(d.balance);
                successModal.classList.add("open");
            })
            .catch(function (error) {
                console.error(error);
                alert("Unable to connect to the database.");
                isSubmitting = false;
                savePaymentButton.disabled = false;
                savePaymentButton.style.opacity = "1";
                savePaymentButton.style.cursor = "pointer";
                savePaymentButton.textContent = "Save Payment";
            });
    };

    /* SUCCESS MODAL - DONE */
    document.getElementById("successDone").onclick = function () {
        successModal.classList.remove("open");
        resetScanResult();
        loadRecent();
        setTimeout(function () { rfidInput.focus(); }, 100);
    };

    /* LOAD RECENT SCANS - shows the borrower's name for every scan */
    function loadRecent() {
        fetch("api.php?action=scans&_=" + Date.now(), { cache: "no-store" })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                const scans = data.scans || [];

                if (scans.length === 0) {
                    recent.innerHTML = '<div style="padding:20px 0; text-align:center; color:#8a9793; font-size:12px;">No scans yet</div>';
                    return;
                }

                recent.innerHTML = scans.map(function (x) {
                    const rfid = String(x.rfid_no ?? x.rfid ?? "").trim();
                    const authorized = String(x.result || "").toLowerCase() === "authorized";

                    /* Try every possible field name the API might use, then fall back to the local cache */
                    const name = String(
                        x.full_name || x.borrower_name || x.borrower || x.name || x.account_name || nameCache[rfid] || ""
                    ).trim();

                    const displayName = name || (authorized ? "Unknown Borrower" : "Unknown Card");
                    const initials = initialsOf(name) || "RF";
                    const avatarBg = name ? "#eaf6ef" : "#f2f4f3";
                    const avatarColor = name ? "#146c4f" : "#8a9793";

                    return `
                    <div style="display:flex; align-items:center; gap:10px; padding:13px 0; border-bottom:1px solid #e6ece9;">
                        <div style="width:42px; height:42px; border-radius:50%; background:${avatarBg}; display:flex; align-items:center; justify-content:center; color:${avatarColor}; font-size:13px; font-weight:700; flex-shrink:0;">
                            ${esc(initials)}
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="font-size:13px; font-weight:700; color:#1c2b27; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                ${esc(displayName)}
                            </div>
                            <div style="font-size:11px; color:#6b7a76; margin-top:2px;">
                                RFID: ${esc(rfid)}
                            </div>
                            <div style="font-size:11px; color:#8a9793; margin-top:1px;">
                                ${esc(x.scanned_at)}
                            </div>
                        </div>
                        <div style="font-size:11px; font-weight:700; color:${authorized ? "#1f9e6f" : "#e14b4b"};">
                            ${esc(x.result)}
                        </div>
                    </div>
                    `;
                }).join("");
            })
            .catch(function (error) { console.error("Could not load recent scans:", error); });
    }

    paymentDate.value = todayISO();
    loadRecent();

});
</script>

</body>
</html>