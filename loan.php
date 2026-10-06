<!DOCTYPE html>
<html lang="en">

<head>

<?php
require_once __DIR__ . "/auth.php";
requireLogin();
?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>RFID Money Lending Management System - Loans</title>

<style>

* {
    box-sizing: border-box;
}

html,
body {
    margin: 0;
    padding: 0;
    width: 100%;
    min-height: 100%;
    background: #f4f7f5;
}

body {
    font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    color: #1c2b27;
    min-height: 100vh;
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
}

/* SIDEBAR */

#sidebar {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 250px;
    background: linear-gradient(180deg, #0d3b2e 0%, #0f4a38 100%);
    transform: translateX(-100%);
    transition: transform .25s ease;
    z-index: 200;
    display: flex;
    flex-direction: column;
    padding: 20px 14px;
}

#sidebar.open {
    transform: translateX(0);
}

.sidebar-close {
    display: flex;
    justify-content: flex-end;
}

.sidebar-close button {
    background: none;
    border: none;
    color: white;
    font-size: 18px;
    cursor: pointer;
    padding: 4px 6px;
}

.sidebar-logo {
    display: flex;
    justify-content: center;
    padding: 4px 6px 24px;
}

.sidebar-logo img {
    width: 120px;
    height: 120px;
    border-radius: 20px;
    object-fit: cover;
}

.sidebar-menu {
    list-style: none;
    margin: 6px 0 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex: 1;
}

.sidebar-menu a {
    display: flex;
    align-items: center;
    padding: 12px 13px;
    border-radius: 9px;
    color: rgba(255,255,255,.82);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: .2s;
}

.sidebar-menu a:hover,
.sidebar-menu a.active {
    background: #1f9e6f;
    color: white;
}

.sidebar-menu a.active {
    font-weight: 700;
}

/* OVERLAY */

#overlay {
    position: fixed;
    inset: 0;
    background: rgba(13,59,46,.45);
    z-index: 150;
    opacity: 0;
    visibility: hidden;
    transition: opacity .25s ease, visibility .25s ease;
}

#overlay.show {
    opacity: 1;
    visibility: visible;
}

/* TOP BAR */

.topbar {
    width: 100%;
    height: 64px;
    background: white;
    border-bottom: 1px solid #e6ece9;
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 16px;
    position: sticky;
    top: 0;
    z-index: 100;
}

.menu-button {
    background: none;
    border: none;
    color: #3c4b47;
    font-size: 20px;
    cursor: pointer;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.topbar h1 {
    font-size: 17px;
    margin: 0;
    font-weight: 700;
    flex: 1;
    color: #1c2b27;
}

.notification {
    position: relative;
    font-size: 18px;
    color: #3c4b47;
}

.notification-count {
    position: absolute;
    top: -8px;
    right: -9px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #e14b4b;
    color: white;
    font-size: 9px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
}

/* CONTENT */

.content {
    width: 100%;
    padding: 16px 12px 40px;
    overflow-x: hidden;
}

/* CARD */

.card {
    width: 100%;
    background: white;
    border-radius: 10px;
    box-shadow:
        0 1px 3px rgba(16,44,34,.06),
        0 1px 2px rgba(16,44,34,.04);
    padding: 16px;
    margin-bottom: 18px;
    overflow: visible;
}

#loanFormCard {
    background: #eaf6ef;
    border: 1px solid #d4e9de;
}

/* SECTION TITLE */

.section-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f4a38;
    padding-bottom: 12px;
    border-bottom: 2px solid #1a8a63;
    margin-bottom: 16px;
}

/* FORM */

.form-group {
    margin-bottom: 14px;
}

.form-group label {
    display: block;
    font-size: 12.5px;
    font-weight: 700;
    margin-bottom: 6px;
    color: #1c2b27;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 12px 13px;
    border: 1px solid #e6ece9;
    border-radius: 8px;
    background: #fbfdfc;
    font-family: inherit;
    font-size: 16px;
    color: #1c2b27;
    outline: none;
    transition: .2s;
}

.form-group input:focus,
.form-group select:focus {
    border-color: #1a8a63;
    box-shadow: 0 0 0 2px rgba(26,138,99,.10);
}

.form-group input[readonly] {
    background: #f2f8f4;
    color: #0f4a38;
    font-weight: 700;
    border-color: #e3f3ea;
}

/*
 * Auto-filled interest rate — subtle visual cue so the
 * user knows the value was set automatically.
 */

.form-group input.auto-filled {
    background: #f2f8f4;
    border-color: #c9e6d8;
    color: #0f4a38;
    font-weight: 700;
}

/* BORROWER AUTOCOMPLETE */

.borrower-autocomplete {
    position: relative;
    width: 100%;
}

.borrower-dropdown {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #dce7e2;
    border-radius: 8px;
    max-height: 220px;
    overflow-y: auto;
    box-shadow: 0 5px 15px rgba(0,0,0,.12);
    z-index: 999;
    display: none;
}

.borrower-dropdown.show {
    display: block;
}

.borrower-option {
    padding: 12px 14px;
    cursor: pointer;
    font-size: 14px;
    color: #1c2b27;
    border-bottom: 1px solid #edf2ef;
}

.borrower-option:last-child {
    border-bottom: none;
}

.borrower-option:hover {
    background: #eaf6f0;
    color: #0f4a38;
}

.no-borrower {
    padding: 12px 14px;
    color: #7b8783;
    font-size: 13px;
}

/* BUTTONS */

.form-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 18px;
}

.btn {
    width: 100%;
    padding: 13px 20px;
    border: none;
    border-radius: 8px;
    color: white;
    font-family: inherit;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: .2s;
}

.btn:active {
    transform: scale(.98);
}

.btn-save {
    background: #146c4f;
}

.btn-save:hover {
    background: #0f573f;
}

.btn-clear {
    background: #e14b4b;
}

.btn-clear:hover {
    background: #c83b3b;
}

/* SEARCH */

.search-container {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f4f7f5;
    border: 1px solid #e6ece9;
    border-radius: 8px;
    padding: 9px 14px;
    color: #6b7a76;
    margin-bottom: 16px;
}

.search-container input {
    border: none;
    outline: none;
    background: transparent;
    width: 100%;
    font-family: inherit;
    font-size: 14px;
    color: #1c2b27;
}

/* TABLE */

.table-container {
    width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 5px;
    max-width: 100%;
}

table {
    width: 100%;
    min-width: 1050px;
    border-collapse: collapse;
    font-size: 12.5px;
}

thead tr {
    background: #146c4f;
}

th {
    color: white;
    text-align: left;
    padding: 12px 14px;
    font-weight: 700;
    white-space: nowrap;
}

th:first-child {
    border-top-left-radius: 8px;
}

th:last-child {
    border-top-right-radius: 8px;
}

td {
    padding: 13px 14px;
    border-bottom: 1px solid #e6ece9;
    white-space: nowrap;
}

/* STATUS */

.status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 5px 11px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
}

.status-active {
    background: #e3f3ea;
    color: #146c4f;
}

.status-completed {
    background: #eaf3fc;
    color: #4388d5;
}

.status-overdue {
    background: #fdeaea;
    color: #e14b4b;
}

.status-pending {
    background: #fff3df;
    color: #e89b32;
}

/* ACTION BUTTON */

.action-btn {
    width: 30px;
    height: 30px;
    border: none;
    border-radius: 6px;
    color: white;
    cursor: pointer;
    font-size: 14px;
    margin-right: 5px;
}

.view-btn {
    background: #1a8a63;
}

.view-btn:hover {
    background: #147451;
}

.empty-row {
    text-align: center;
    padding: 30px !important;
    color: #7b8783;
}

/* FOOTER */

.table-footer {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 16px;
    color: #6b7a76;
    font-size: 13px;
}

/* MODAL */

.modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    z-index: 500;
}

.modal.show {
    display: flex;
}

.modal-box {
    width: 100%;
    max-width: 430px;
    max-height: 90vh;
    overflow-y: auto;
    background: white;
    border-radius: 12px;
    padding: 20px;
}

.modal-title {
    color: #0f4a38;
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 16px;
    border-bottom: 2px solid #1a8a63;
    padding-bottom: 10px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    padding: 10px 0;
    border-bottom: 1px solid #e6ece9;
    font-size: 14px;
}

.detail-label {
    font-weight: 700;
    color: #52625d;
}

.detail-value {
    text-align: right;
    color: #1c2b27;
    font-weight: 600;
    word-break: break-word;
}

.modal-close {
    width: 100%;
    border: none;
    border-radius: 8px;
    background: #146c4f;
    color: white;
    padding: 12px;
    font-weight: 700;
    margin-top: 18px;
    cursor: pointer;
}

/* MOBILE */

@media (max-width: 600px) {

    .topbar {
        height: 58px;
        padding: 12px 14px;
    }

    .topbar h1 {
        font-size: 16px;
    }

    .content {
        padding: 12px 10px 30px;
    }

    .card {
        padding: 13px;
        border-radius: 9px;
    }

    table {
        min-width: 1050px;
    }

    th,
    td {
        padding: 11px 12px;
    }
}

</style>

</head>

<body>

<!-- SIDEBAR -->

<aside id="sidebar">

    <div class="sidebar-close">
        <button type="button" id="closeMenu">✕</button>
    </div>

    <div class="sidebar-logo">
        <img src="image/logo.png" alt="Logo">
    </div>

    <ul class="sidebar-menu">

        <li>
            <a href="dashboard.php">Home</a>
        </li>

        <li>
            <a href="borrowers.php">Borrowers</a>
        </li>

        <li>
            <a href="loan.php" class="active">Loans</a>
        </li>

        <li>
            <a href="payment.php">Payments</a>
        </li>

        <li>
            <a href="report.php">Reports</a>
        </li>
        
        <li>
            <a href="scanner.php">RFID Scanner</a>
        </li>

        <li>
            <a href="logout.php">Logout</a>
        </li>

    </ul>

</aside>

<div id="overlay"></div>

<!-- TOP BAR -->

<div class="topbar">

    <button
        type="button"
        id="menuButton"
        class="menu-button"
        aria-label="Open menu"
    >
        ☰
    </button>

    <h1>Loans</h1>

</div>

<!-- MAIN -->

<div class="content">

<!-- CREATE LOAN -->

<section class="card" id="loanFormCard">

    <div class="section-title">
        <span id="formTitle">Create New Loan</span>
    </div>

    <form id="loanForm">

        <!-- BORROWER NAME -->

        <div class="form-group">

            <label for="borrowerName">
                Borrower Name
            </label>

            <div class="borrower-autocomplete">

                <input
                    type="text"
                    id="borrowerName"
                    name="borrower_name"
                    placeholder="Enter or select borrower name"
                    autocomplete="off"
                    required
                >

                <input
                    type="hidden"
                    id="borrowerId"
                    name="borrower_id"
                >

                <div
                    id="borrowerDropdown"
                    class="borrower-dropdown"
                ></div>

            </div>

        </div>

        <!-- LOAN AMOUNT -->

        <div class="form-group">

            <label for="loanAmount">
                Loan Amount
            </label>

            <input
                type="number"
                id="loanAmount"
                name="loanAmount"
                placeholder="₱ 0.00"
                min="0"
                step="0.01"
                inputmode="decimal"
                required
            >

        </div>

        <!-- LOAN TERM -->

        <div class="form-group">

            <label for="loanTerm">
                Loan Term (Months)
            </label>

            <select
                id="loanTerm"
                name="loanTerm"
                required
            >

                <option value="">
                    Select Term
                </option>

                <option value="2 Months">2 Months</option>
                <option value="3 Months">3 Months</option>
                <option value="4 Months">4 Months</option>
                <option value="5 Months">5 Months</option>
                <option value="6 Months">6 Months</option>
                <option value="7 Months">7 Months</option>
                <option value="8 Months">8 Months</option>
                <option value="9 Months">9 Months</option>
                <option value="10 Months">10 Months</option>
                <option value="11 Months">11 Months</option>
                <option value="12 Months">12 Months</option>

            </select>

        </div>

        <!-- INTEREST RATE (AUTO-FILLED FROM LOAN TERM) -->

        <div class="form-group">

            <label for="interestRate">
                Interest Rate (% per month)
            </label>

            <input
                type="number"
                id="interestRate"
                name="interestRate"
                placeholder="Select a loan term first"
                min="0"
                step="0.01"
                readonly
                required
            >

        </div>

        <!-- INTEREST AMOUNT (PER MONTH) -->

        <div class="form-group">

            <label for="interestAmount">
                Interest Amount (per month)
            </label>

            <input
                type="text"
                id="interestAmount"
                value="₱0.00"
                readonly
            >

            <!--
                Raw, unformatted numeric value that actually
                gets submitted to the server.
            -->

            <input
                type="hidden"
                id="interestAmountRaw"
                name="interestAmount"
                value="0"
            >

        </div>

        <!--
            PAYMENT FREQUENCY
            Removed from the UI. Payments are always monthly.
        -->

        <input
            type="hidden"
            id="paymentFrequency"
            name="paymentFrequency"
            value="Monthly"
        >

        <!--
            LOAN STATUS
            Removed from the UI. New loans always start
            as "Active" immediately after registration.
        -->

        <input
            type="hidden"
            id="loanStatus"
            name="loanStatus"
            value="Active"
        >

        <!-- TOTAL INTEREST (INTEREST × TERM) -->

        <div class="form-group">

            <label for="totalInterest">
                Total Interest (Interest × Loan Term)
            </label>

            <input
                type="text"
                id="totalInterest"
                value="₱0.00"
                readonly
            >

            <input
                type="hidden"
                id="totalInterestRaw"
                name="totalInterest"
                value="0"
            >

        </div>

        <!-- START DATE -->

        <div class="form-group">
            <label for="startDate">Start Date</label>
            <input type="date" id="startDate" name="startDate"
                readonly required
                style="background:#f2f8f4; color:#0f4a38; font-weight:700; cursor:not-allowed;">
            <small style="display:block; margin-top:5px; font-size:11.5px; color:#6b7a76;">
                Automatically set to today's date.
            </small>
        </div>

        <!-- TOTAL PAYABLE -->

        <div class="form-group">

            <label for="totalPayable">
                Total Payable
            </label>

            <input
                type="text"
                id="totalPayable"
                value="₱0.00"
                readonly
            >

            <input
                type="hidden"
                id="totalPayableRaw"
                name="totalPayable"
                value="0"
            >

        </div>

        <!-- BUTTONS -->

        <div class="form-buttons">

            <button
                type="submit"
                class="btn btn-save"
                id="saveLoanButton"
            >
                Save Loan
            </button>

            <button
                type="button"
                class="btn btn-clear"
                id="clearLoanButton"
            >
                Clear Fields
            </button>

        </div>

    </form>

</section>

<!-- LOAN RECORDS -->

<section class="card">

    <div class="section-title">
        Loan Records
    </div>

    <div class="search-container">

        <span>🔍</span>

        <input
            type="text"
            id="searchInput"
            placeholder="Search borrower..."
            autocomplete="off"
        >

    </div>

    <div class="table-container">

        <table>

            <thead>

                <tr>
                    <th>Borrower</th>
                    <th>Loan Amount</th>
                    <th>Interest</th>
                    <th>Total Interest</th>
                    <th>Total Payable</th>
                    <th>Amount Paid</th>
                    <th>Balance</th>
                    <th>Start Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>

            </thead>

            <tbody id="loanTable"></tbody>

        </table>

    </div>

    <div class="table-footer">

        <div id="entryInfo">
            Showing 0 entries
        </div>

    </div>

</section>

</div>

<!-- VIEW MODAL -->

<div class="modal" id="viewModal">

    <div class="modal-box">

        <div class="modal-title">
            Loan Details
        </div>

        <div id="modalContent"></div>

        <button
            type="button"
            class="modal-close"
            id="modalClose"
        >
            Close
        </button>

    </div>

</div>


<script>

document.addEventListener("DOMContentLoaded", () => {

    /* =====================================================
       INTEREST RATE TABLE
       
       Automatically applied when a loan term is chosen.
       Key   = loan term string (must match <option value>)
       Value = interest rate in % per month
       
       EDIT THESE VALUES TO MATCH YOUR ACTUAL RATES.
    ===================================================== */

    const INTEREST_RATES = {

        "2 Months":  5,
        "3 Months":  5,
        "4 Months":  5,
        "5 Months":  5,
        "6 Months":  5,
        "7 Months":  5,
        "8 Months": 5,
        "9 Months": 5,
        "10 Months": 5,
        "11 Months": 5,
        "12 Months": 5

    };


    /* =====================================================
       SIDEBAR
    ===================================================== */

    const sidebar =
        document.getElementById("sidebar");

    const overlay =
        document.getElementById("overlay");

    const menu =
        document.getElementById("menuButton");

    const close =
        document.getElementById("closeMenu");

    menu.onclick = () => {
        sidebar.classList.add("open");
        overlay.classList.add("show");
    };

    function shut() {
        sidebar.classList.remove("open");
        overlay.classList.remove("show");
    }

    close.onclick = shut;
    overlay.onclick = shut;


    /* =====================================================
       ELEMENTS
    ===================================================== */

    const form =
        document.getElementById("loanForm");

    const table =
        document.getElementById("loanTable");

    const search =
        document.getElementById("searchInput");

    const clear =
        document.getElementById("clearLoanButton");

    const borrowerInput =
        document.getElementById("borrowerName");

    const borrowerIdInput =
        document.getElementById("borrowerId");

    const borrowerDropdown =
        document.getElementById("borrowerDropdown");

    const loanAmountInput =
        document.getElementById("loanAmount");

    const interestRateInput =
        document.getElementById("interestRate");

    const interestAmountInput =
        document.getElementById("interestAmount");

    const totalInterestInput =
        document.getElementById("totalInterest");

    const totalPayableInput =
        document.getElementById("totalPayable");

    const interestAmountRawInput =
        document.getElementById("interestAmountRaw");

    const totalInterestRawInput =
        document.getElementById("totalInterestRaw");

    const totalPayableRawInput =
        document.getElementById("totalPayableRaw");

    const loanTermSelect =
        document.getElementById("loanTerm");


    let rows = [];
    let borrowers = [];


    /* =====================================================
       ESCAPE HTML
    ===================================================== */

    function esc(value) {

        return String(value ?? "").replace(
            /[&<>"']/g,
            m => ({
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': "&quot;",
                "'": "&#039;"
            }[m])
        );

    }


    /* =====================================================
       MONEY
    ===================================================== */

    function money(value) {

        return "₱" +
            Number(value || 0).toLocaleString(
                "en-PH",
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            );

    }


    /* =====================================================
       TERM → NUMBER OF MONTHS
    ===================================================== */

    function getTermMonths() {

        const term =
            String(loanTermSelect.value || "");

        const match =
            term.match(/(\d+)/);

        return match
            ? parseInt(match[1], 10)
            : 0;

    }


    /* =====================================================
       STATUS
    ===================================================== */

    function getStatusClass(status) {

        const statusMap = {

            "Active": "status-active",
            "Completed": "status-completed",
            "Paid": "status-completed",
            "Overdue": "status-overdue",
            "Pending": "status-pending"

        };

        return statusMap[status] || "";

    }


    /* =====================================================
       RENDER LOANS
    ===================================================== */

    function render() {

        const q =
            (search.value || "")
            .toLowerCase()
            .trim();

        const filtered =
            rows.filter(x =>
                String(x.full_name || "")
                .toLowerCase()
                .includes(q)
            );


        table.innerHTML = filtered.length

            ? filtered.map(x => `

                <tr>

                    <td>
                        ${esc(x.full_name)}
                    </td>

                    <td>
                        ${money(x.loan_amount)}
                    </td>

                    <td>
                        ${money(x.interest_amount)}
                    </td>

                    <td>
                        ${money(x.total_interest)}
                    </td>

                    <td>
                        ${money(x.total_payable)}
                    </td>

                    <td>
                        ${money(x.amount_paid)}
                    </td>

                    <td>
                        ${money(x.balance)}
                    </td>

                    <td>
                        ${esc(x.start_date)}
                    </td>

                    <td>
                        <span class="status ${getStatusClass(x.status)}">
                            ${esc(x.status)}
                        </span>
                    </td>

                    <td>

                        <button
                            type="button"
                            class="action-btn view-btn viewBtn"
                            data-id="${esc(x.id)}"
                            title="View"
                        >
                            👁
                        </button>

                    </td>

                </tr>

            `).join("")

            : `

                <tr>

                    <td
                        colspan="10"
                        class="empty-row"
                    >
                        No loans found.
                    </td>

                </tr>

            `;


        document.getElementById("entryInfo")
            .textContent =
            `Showing ${filtered.length} entries`;


        document.querySelectorAll(".viewBtn")
            .forEach(button => {

                button.onclick = () =>
                    view(button.dataset.id);

            });

    }


    /* =====================================================
       LOAD BORROWERS
    ===================================================== */

    function loadBorrowers() {

        fetch("api.php?action=borrowers")

            .then(response => {

                if (!response.ok) {

                    throw new Error(
                        "Unable to load borrowers."
                    );

                }

                return response.json();

            })

            .then(data => {

                console.log(
                    "BORROWERS API RESPONSE:",
                    data
                );


                if (!data.success) {

                    console.error(
                        "Borrower API error:",
                        data.message
                    );

                    borrowers = [];

                    return;

                }


                borrowers =
                    Array.isArray(data.borrowers)
                        ? data.borrowers
                        : [];


                console.log(
                    "BORROWERS LOADED:",
                    borrowers
                );

            })

            .catch(error => {

                console.error(
                    "Error loading borrowers:",
                    error
                );

            });

    }


    /* =====================================================
       SHOW BORROWERS
    ===================================================== */

    function showBorrowers(searchText = "") {

        const searchValue =
            searchText
            .trim()
            .toLowerCase();


        borrowerDropdown.innerHTML = "";


        if (searchValue === "") {

            borrowerDropdown.classList.remove(
                "show"
            );

            return;

        }


        const filtered =
            borrowers.filter(borrower => {

                const name =
                    String(
                        borrower["full name"] || ""
                    )
                    .trim()
                    .toLowerCase();


                return name.startsWith(
                    searchValue
                );

            });


        if (filtered.length === 0) {

            borrowerDropdown.innerHTML = `

                <div class="no-borrower">
                    No saved borrowers found.
                </div>

            `;

            borrowerDropdown.classList.add(
                "show"
            );

            return;

        }


        filtered.forEach(borrower => {

            const option =
                document.createElement("div");


            option.className =
                "borrower-option";


            option.textContent =
                borrower["full name"];


            option.addEventListener(
                "mousedown",
                event => {

                    event.preventDefault();

                    selectBorrower(
                        borrower
                    );

                }
            );


            borrowerDropdown.appendChild(
                option
            );

        });


        borrowerDropdown.classList.add(
            "show"
        );

    }


    /* =====================================================
       SELECT BORROWER
    ===================================================== */

    function selectBorrower(borrower) {

        borrowerInput.value =
            borrower["full name"];


        borrowerIdInput.value =
            borrower["Rfid no"];


        console.log(
            "SELECTED BORROWER:",
            borrower["full name"]
        );


        console.log(
            "BORROWER RFID:",
            borrower["Rfid no"]
        );


        borrowerDropdown.classList.remove(
            "show"
        );

    }


    /* =====================================================
       BORROWER INPUT
    ===================================================== */

    borrowerInput.addEventListener(
        "input",
        () => {

            borrowerIdInput.value = "";


            showBorrowers(
                borrowerInput.value
            );

        }
    );


    borrowerInput.addEventListener(
        "focus",
        () => {

            if (
                borrowerInput.value.trim() !== ""
            ) {

                showBorrowers(
                    borrowerInput.value
                );

            }

        }
    );


    /* =====================================================
       CLOSE BORROWER DROPDOWN
    ===================================================== */

    document.addEventListener(
        "click",
        event => {

            if (
                !event.target.closest(
                    ".borrower-autocomplete"
                )
            ) {

                borrowerDropdown.classList.remove(
                    "show"
                );

            }

        }
    );


    /* =====================================================
       LOAD LOANS
    ===================================================== */

    function load() {

        fetch("api.php?action=loans")

            .then(response => {

                if (!response.ok) {

                    throw new Error(
                        "Unable to load loans."
                    );

                }

                return response.json();

            })

            .then(data => {

                console.log(
                    "LOANS API RESPONSE:",
                    data
                );


                if (!data.success) {

                    console.error(
                        "Loan API error:",
                        data.message
                    );

                    rows = [];

                    render();

                    return;

                }


                rows =
                    Array.isArray(data.loans)
                        ? data.loans
                        : [];


                render();

            })

            .catch(error => {

                console.error(
                    "Error loading loans:",
                    error
                );

            });

    }


    /* =====================================================
       VIEW LOAN
    ===================================================== */

    function view(id) {

        const loan =
            rows.find(
                x =>
                    String(x.id) ===
                    String(id)
            );


        if (!loan) return;


        document.getElementById(
            "modalContent"
        ).innerHTML = `

            <div class="detail-row">
                <span class="detail-label">
                    Borrower
                </span>

                <span class="detail-value">
                    ${esc(loan.full_name)}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">
                    Loan Amount
                </span>

                <span class="detail-value">
                    ${money(loan.loan_amount)}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">
                    Interest Rate
                </span>

                <span class="detail-value">
                    ${esc(loan.interest_rate)}%
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">
                    Interest Amount (per month)
                </span>

                <span class="detail-value">
                    ${money(loan.interest_amount)}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">
                    Total Interest
                </span>

                <span class="detail-value">
                    ${money(loan.total_interest)}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">
                    Total Payable
                </span>

                <span class="detail-value">
                    ${money(loan.total_payable)}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">
                    Amount Paid
                </span>

                <span class="detail-value">
                    ${money(loan.amount_paid)}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">
                    Balance
                </span>

                <span class="detail-value">
                    ${money(loan.balance)}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">
                    Loan Term
                </span>

                <span class="detail-value">
                    ${esc(loan.loan_term)}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">
                    Start Date
                </span>

                <span class="detail-value">
                    ${esc(loan.start_date)}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">
                    Status
                </span>

                <span class="detail-value">
                    ${esc(loan.status)}
                </span>
            </div>

        `;


        document.getElementById(
            "viewModal"
        ).classList.add("show");

    }


    /* =====================================================
       MODAL
    ===================================================== */

    const modalClose =
        document.getElementById("modalClose");

    const viewModal =
        document.getElementById("viewModal");


    modalClose.onclick = () => {

        viewModal.classList.remove(
            "show"
        );

    };


    viewModal.onclick = event => {

        if (
            event.target === viewModal
        ) {

            viewModal.classList.remove(
                "show"
            );

        }

    };


    /* =====================================================
       AUTO-FILL INTEREST RATE FROM LOAN TERM
    ===================================================== */

    function applyTermRate() {

        const term =
            loanTermSelect.value;

        const rate =
            INTEREST_RATES[term];


        if (rate !== undefined && rate !== null) {

            interestRateInput.value =
                rate;

            interestRateInput.classList.add(
                "auto-filled"
            );

        } else {

            interestRateInput.value =
                "";

            interestRateInput.classList.remove(
                "auto-filled"
            );

        }


        calculateTotals();

    }


    /* =====================================================
       CALCULATE TOTALS

       Every peso amount is ROUNDED UP to the nearest peso
       so the borrower never underpays and every figure
       in the form is a whole number.

       - Interest Amount = CEIL( Loan Amount × Rate / 100 )
       - Total Interest  = CEIL( Interest Amount × Term )
       - Total Payable   = Loan Amount + Total Interest
    ===================================================== */

    function calculateTotals() {

        const amount =
            parseFloat(
                loanAmountInput.value
            ) || 0;


        const rate =
            parseFloat(
                interestRateInput.value
            ) || 0;


        const months =
            getTermMonths();


        /* =====================================================
           ROUNDED UP — interest per month
           Before: (amount * rate) / 100
           After:  Math.ceil(...) → rounds UP to the nearest peso.
        ===================================================== */
        const interest =
            Math.ceil((amount * rate) / 100);


        /* =====================================================
           ROUNDED UP — total interest across the whole term
           Before: interest * months
           After:  Math.ceil(...) → rounds UP to the nearest peso.
        ===================================================== */
        const totalInterest =
            Math.ceil(interest * months);


        const total =
            amount + totalInterest;


        interestAmountInput.value =
            money(interest);


        totalInterestInput.value =
            money(totalInterest);


        totalPayableInput.value =
            money(total);


        interestAmountRawInput.value =
            interest.toFixed(2);


        totalInterestRawInput.value =
            totalInterest.toFixed(2);


        totalPayableRawInput.value =
            total.toFixed(2);

    }


    loanAmountInput.addEventListener(
        "input",
        calculateTotals
    );


    loanTermSelect.addEventListener(
        "change",
        applyTermRate
    );


    /* =====================================================
       SAVE LOAN
    ===================================================== */

    form.addEventListener(
        "submit",
        event => {

            event.preventDefault();


            const borrowerId =
                borrowerIdInput.value.trim();


            if (!borrowerId) {

                alert(
                    "Please select a borrower from the saved borrower list."
                );

                borrowerInput.focus();

                showBorrowers(
                    borrowerInput.value
                );

                return;

            }


            if (!loanTermSelect.value) {

                alert(
                    "Please select a loan term so the interest rate can be applied."
                );

                loanTermSelect.focus();

                return;

            }


            const formData =
                new FormData(form);


            formData.set(
                "borrower_id",
                borrowerId
            );


            formData.delete("rfid");
            formData.delete("rfid_no");
            formData.delete("rfid_uid");


            console.log(
                "SENDING LOAN DATA:"
            );


            for (
                const [key, value]
                of formData.entries()
            ) {

                console.log(
                    key,
                    value
                );

            }


            fetch(
                "api.php?action=save_loan",
                {
                    method: "POST",
                    body: formData
                }
            )

            .then(response => {

                if (!response.ok) {

                    throw new Error(
                        "Server error."
                    );

                }

                return response.json();

            })

            .then(data => {

                console.log(
                    "SAVE LOAN RESPONSE:",
                    data
                );


                if (!data.success) {

                    alert(
                        data.message ||
                        "Unable to save loan."
                    );

                    return;

                }


                alert(
                    "Loan saved successfully!"
                );


                form.reset();


                borrowerIdInput.value =
                    "";


                borrowerDropdown.classList.remove(
                    "show"
                );


                interestRateInput.classList.remove(
                    "auto-filled"
                );


                document.getElementById(
                    "formTitle"
                ).textContent =
                    "Create New Loan";


                setToday();


                calculateTotals();


                load();

            })

            .catch(error => {

                console.error(
                    "Error saving loan:",
                    error
                );


                alert(
                    "An error occurred while saving the loan. Check the browser console for details."
                );

            });

        }
    );


    /* =====================================================
       SET TODAY
    ===================================================== */

    function setToday() {

        const today =
            new Date()
            .toISOString()
            .slice(0, 10);


        document.getElementById(
            "startDate"
        ).value = today;

    }


    /* =====================================================
       CLEAR FORM
    ===================================================== */

    clear.onclick = () => {

        form.reset();


        borrowerIdInput.value =
            "";


        borrowerDropdown.classList.remove(
            "show"
        );


        interestRateInput.classList.remove(
            "auto-filled"
        );


        setToday();


        calculateTotals();

    };


    /* =====================================================
       SEARCH
    ===================================================== */

    search.oninput =
        render;


    /* =====================================================
       INITIALIZE
    ===================================================== */

    setToday();

    calculateTotals();

    loadBorrowers();

    load();

});


    /* Keep loan values synchronized with payments made on other pages. */
    setInterval(function () {
        if (document.visibilityState === "visible") {
            load();
        }
    }, 3000);

</script>

</body>
</html>