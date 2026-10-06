<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once __DIR__ . "/auth.php"; requireLogin(); ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RFID Money Lending Management System - Reports</title>

<style>
* { box-sizing: border-box; }

html, body { margin:0; padding:0; width:100%; min-height:100%; background:#f4f7f5; }

body {
    font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    color: #1c2b27;
    min-height: 100vh;
    overflow-x: hidden;
}

/* SIDEBAR */
#sidebar {
    position: fixed; top:0; left:0; bottom:0;
    width: 250px;
    background: linear-gradient(180deg, #0d3b2e 0%, #0f4a38 100%);
    transform: translateX(-100%);
    transition: transform .25s ease;
    z-index: 200;
    display: flex; flex-direction: column;
    padding: 20px 14px;
}
#sidebar.open { transform: translateX(0); }
.sidebar-close { display:flex; justify-content:flex-end; }
.sidebar-close button {
    background:none; border:none; color:white; font-size:18px; cursor:pointer; padding:4px 6px;
}
.sidebar-logo { display:flex; justify-content:center; padding:4px 6px 24px; }
.sidebar-logo img { width:130px; height:130px; border-radius:30px; object-fit:cover; }
.sidebar-menu {
    list-style:none; margin:6px 0 0; padding:0;
    display:flex; flex-direction:column; gap:4px; flex:1;
}
.sidebar-menu li a {
    display:flex; align-items:center;
    padding:12px 13px; border-radius:9px;
    color:rgba(255,255,255,.82); text-decoration:none;
    font-size:14px; font-weight:500; transition:.2s;
}
.sidebar-menu li a:hover,
.sidebar-menu li a.active { background:#1f9e6f; color:white; }
.sidebar-menu li a.active { font-weight:700; }

/* OVERLAY */
#overlay {
    position:fixed; inset:0;
    background:rgba(13,59,46,.45);
    z-index:150; opacity:0; visibility:hidden;
    transition:opacity .25s ease, visibility .25s ease;
}
#overlay.show { opacity:1; visibility:visible; }

/* TOP BAR */
.topbar {
    width:100%; height:64px; background:white;
    border-bottom:1px solid #e6ece9;
    display:flex; align-items:center; gap:14px;
    padding:14px 16px; position:sticky; top:0; z-index:100;
}
.menu-button {
    background:none; border:none; color:#3c4b47;
    font-size:20px; cursor:pointer; padding:0;
    display:flex; align-items:center; justify-content:center;
}
.topbar h1 {
    font-size:17px; margin:0; font-weight:700;
    flex:1; color:#1c2b27;
}

/* CONTENT */
.content { width:100%; padding:16px 12px 40px; overflow-x:hidden; }

/* CARD */
.card {
    width:100%; background:white;
    border-radius:10px;
    box-shadow:0 1px 3px rgba(16,44,34,.06), 0 1px 2px rgba(16,44,34,.04);
    padding:16px; margin-bottom:16px; overflow:hidden;
}

.section-title {
    font-size:15px; font-weight:700; color:#0f4a38;
    padding-bottom:12px; border-bottom:2px solid #1a8a63;
    margin-bottom:16px;
}

/* FILTERS */
.filter-grid {
    display:grid;
    grid-template-columns:1fr;
    gap:12px;
}
@media (min-width: 700px) {
    .filter-grid { grid-template-columns: 1fr 1fr 1fr; }
}

.form-group { margin-bottom: 0; }
.form-group label {
    display:block; font-size:12.5px; font-weight:700;
    margin-bottom:6px; color:#1c2b27;
}
.form-group input,
.form-group select {
    width:100%; padding:12px 13px;
    border:1px solid #e6ece9; border-radius:8px;
    background:#fbfdfc; font-family:inherit; font-size:16px;
    color:#1c2b27; outline:none; transition:.2s;
}
.form-group input:focus,
.form-group select:focus {
    border-color:#1a8a63;
    box-shadow:0 0 0 2px rgba(26,138,99,.10);
}
.form-group input:disabled { background:#f2f4f3; color:#9aa8a4; }

/* ACTION BUTTONS */
.report-actions {
    display:flex; flex-wrap:wrap; gap:10px; margin-top:18px;
}
.btn {
    padding:12px 20px; border:none; border-radius:8px;
    color:white; font-family:inherit; font-size:14px;
    font-weight:700; cursor:pointer; transition:.2s;
    display:inline-flex; align-items:center; gap:7px;
    min-height:44px; justify-content:center;
}
.btn:active { transform: scale(.98); }
.btn:disabled { opacity:.6; cursor:not-allowed; }
.btn-generate { background:#146c4f; flex:1 1 180px; }
.btn-generate:hover:not(:disabled) { background:#0f573f; }
.btn-print    { background:#4388d5; flex:0 0 auto; }
.btn-print:hover:not(:disabled)    { background:#2f6fb3; }
.btn-csv      { background:#e89b32; flex:0 0 auto; }
.btn-csv:hover:not(:disabled)      { background:#c9821f; }

/* SUMMARY BOXES */
.summary-row {
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap:10px;
    margin-bottom:16px;
}
.summary-box {
    background:#f4faf7; border:1px solid #e6ece9;
    border-radius:9px; padding:12px 14px;
}
.summary-label {
    font-size:10.5px; color:#6b7a76; font-weight:700;
    text-transform:uppercase; letter-spacing:.3px; margin-bottom:5px;
}
.summary-value {
    font-size:16px; font-weight:800; color:#0f4a38;
    word-break: break-word;
}

/* TABLE */
.table-container {
    width:100%; overflow-x:auto; overflow-y:hidden;
    -webkit-overflow-scrolling:touch;
    scrollbar-width:thin; scrollbar-color:#146c4f #e6ece9;
    padding-bottom:5px; max-width:100%;
}
.table-container::-webkit-scrollbar { height:8px; }
.table-container::-webkit-scrollbar-track { background:#e6ece9; border-radius:10px; }
.table-container::-webkit-scrollbar-thumb { background:#146c4f; border-radius:10px; }

table {
    width:100%; min-width:600px;
    border-collapse:collapse; font-size:12.5px;
}
thead tr { background:#146c4f; }
th {
    color:white; text-align:left;
    padding:12px 14px; font-weight:700; white-space:nowrap;
}
th:first-child { border-top-left-radius:8px; }
th:last-child  { border-top-right-radius:8px; }
td {
    padding:11px 14px;
    border-bottom:1px solid #e6ece9;
    white-space:nowrap;
}
tbody tr:hover { background:#f8fbf9; }
.num { text-align:right; font-variant-numeric: tabular-nums; }

.empty-row {
    text-align:center; padding:30px !important; color:#7b8783;
}

/* STATUS BADGES */
.status {
    display:inline-flex; align-items:center; justify-content:center;
    padding:5px 11px; border-radius:20px;
    font-size:11px; font-weight:700;
}
.status-active     { background:#e3f3ea; color:#146c4f; }
.status-paid       { background:#eaf3fc; color:#4388d5; }
.status-completed  { background:#eaf3fc; color:#4388d5; }
.status-overdue    { background:#fdeaea; color:#e14b4b; }
.status-pending    { background:#fff3df; color:#e89b32; }

/* STATEMENT VIEW */
.statement-section { margin-bottom:18px; }
.statement-label {
    font-size:12px; font-weight:700; color:#52625d;
    text-transform:uppercase; letter-spacing:.4px;
    margin-bottom:8px;
}
.statement-card {
    background:#f4faf7; border:1px solid #e3f3ea;
    border-radius:9px; padding:14px 16px; margin-bottom:14px;
}
.statement-name {
    font-size:17px; font-weight:800; color:#0f4a38;
}
.statement-detail {
    font-size:12.5px; color:#52625d; margin-top:5px; line-height:1.6;
}

/* MISC */
.report-meta {
    font-size:12.5px; color:#6b7a76;
    margin-bottom:14px; padding-bottom:12px;
    border-bottom:1px dashed #e6ece9;
}

/* PRINT */
@media print {
    #sidebar, #overlay, .topbar, .no-print { display:none !important; }
    body { background:#fff; }
    .content { padding:0; }
    .card {
        box-shadow:none; border:none;
        padding:0; margin:0 0 18px 0;
    }
    table { font-size:11px; }
    th { background:#146c4f !important; color:#fff !important;
         -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .summary-box { background:#f4faf7 !important;
                   -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .report-meta { border-bottom:1px solid #ccc; }
}

/* MOBILE */
@media (max-width: 600px) {
    .topbar { height:58px; padding:12px 14px; }
    .topbar h1 { font-size:16px; }
    .content { padding:12px 10px 30px; }
    .card { padding:13px; border-radius:9px; }
    .section-title { font-size:14px; }
    .summary-value { font-size:14px; }
    .btn { padding:11px 14px; font-size:13px; }
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<aside id="sidebar">
    <div class="sidebar-close">
        <button id="closeMenu" type="button">✕</button>
    </div>
    <div class="sidebar-logo">
        <img src="image/logo.png" alt="Logo">
    </div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php">Home</a></li>
        <li><a href="borrowers.php">Borrowers</a></li>
        <li><a href="loan.php">Loans</a></li>
        <li><a href="payment.php">Payments</a></li>
        <li><a href="report.php" class="active">Reports</a></li>
        <li><a href="scanner.php">RFID Scanner</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</aside>

<!-- OVERLAY -->
<div id="overlay"></div>

<!-- TOP BAR -->
<div class="topbar">
    <button id="menuButton" class="menu-button" type="button">☰</button>
    <h1>Reports</h1>
</div>

<!-- CONTENT -->
<div class="content">

    <!-- FILTERS -->
    <section class="card no-print">
        <div class="section-title">Generate Report</div>

        <div class="filter-grid">

            <div class="form-group">
                <label for="reportType">Report Type</label>
                <select id="reportType">
                    <option value="collections">Collections (payments received)</option>
                    <option value="loans">Loans Released</option>
                    <option value="interest">Interest Earned</option>
                    <option value="outstanding">Outstanding Balances</option>
                    <option value="overdue">Overdue Accounts</option>
                    <option value="statement">Borrower Statement</option>
                </select>
            </div>

            <div class="form-group">
                <label for="dateFrom">Date From</label>
                <input type="date" id="dateFrom">
            </div>

            <div class="form-group">
                <label for="dateTo">Date To</label>
                <input type="date" id="dateTo">
            </div>

        </div>

        <!-- RFID field (only for statement) -->
        <div class="form-group" id="rfidGroup" style="margin-top:12px; display:none;">
            <label for="statementRfid">Borrower RFID</label>
            <input type="text" id="statementRfid"
                   placeholder="Scan or type the RFID number"
                   autocomplete="off">
        </div>

        <div class="report-actions">
            <button type="button" class="btn btn-generate" id="generateBtn">
                📊 Generate
            </button>
            <button type="button" class="btn btn-print" id="printBtn" disabled>
                🖨 Print
            </button>
            <button type="button" class="btn btn-csv" id="csvBtn" disabled>
                ⬇ CSV
            </button>
        </div>

    </section>

    <!-- REPORT OUTPUT -->
    <section class="card" id="outputCard">

        <div class="section-title" id="reportTitle">Report</div>

        <div class="report-meta" id="reportMeta" style="display:none;"></div>

        <div class="summary-row" id="summaryRow"></div>

        <div id="reportBody">
            <div style="text-align:center; padding:30px 10px; color:#7b8783; font-size:13px;">
                Choose a report type and click <strong>Generate</strong>.
            </div>
        </div>

    </section>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    /* SIDEBAR */
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("overlay");
    const menu    = document.getElementById("menuButton");
    const close   = document.getElementById("closeMenu");

    menu.onclick = () => { sidebar.classList.add("open"); overlay.classList.add("show"); };
    function shut() { sidebar.classList.remove("open"); overlay.classList.remove("show"); }
    close.onclick = shut;
    overlay.onclick = shut;

    /* ELEMENTS */
    const reportType    = document.getElementById("reportType");
    const dateFrom      = document.getElementById("dateFrom");
    const dateTo        = document.getElementById("dateTo");
    const rfidGroup     = document.getElementById("rfidGroup");
    const statementRfid = document.getElementById("statementRfid");
    const generateBtn   = document.getElementById("generateBtn");
    const printBtn      = document.getElementById("printBtn");
    const csvBtn        = document.getElementById("csvBtn");
    const reportTitle   = document.getElementById("reportTitle");
    const reportMeta    = document.getElementById("reportMeta");
    const summaryRow    = document.getElementById("summaryRow");
    const reportBody    = document.getElementById("reportBody");

    /* STATE */
    let lastRows   = [];
    let lastConfig = null;

    /* DEFAULTS */
    const today = new Date();
    const firstOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

    dateTo.value   = today.toISOString().slice(0,10);
    dateFrom.value = firstOfMonth.toISOString().slice(0,10);

    /* COLUMN CONFIGS PER REPORT TYPE */
    const REPORT_CONFIG = {
        collections: {
            title: "Collections",
            endpoint: "report_collections",
            columns: [
                { key:"payment_date",       label:"Date" },
                { key:"full_name",          label:"Borrower" },
                { key:"rfid_no",            label:"RFID" },
                { key:"loan_id",            label:"Loan #" },
                { key:"amount",             label:"Amount", money:true, align:"right" },
                { key:"payment_method",     label:"Method" },
                { key:"outstanding_after",  label:"Balance After", money:true, align:"right" },
                { key:"notes",              label:"Notes" }
            ]
        },
        loans: {
            title: "Loans Released",
            endpoint: "report_loans",
            columns: [
                { key:"start_date",     label:"Start Date" },
                { key:"full_name",      label:"Borrower" },
                { key:"rfid_no",        label:"RFID" },
                { key:"loan_amount",    label:"Loan Amount", money:true, align:"right" },
                { key:"interest_rate",  label:"Rate %", align:"right" },
                { key:"total_interest", label:"Interest", money:true, align:"right" },
                { key:"total_payable",  label:"Total Payable", money:true, align:"right" },
                { key:"amount_paid",    label:"Paid", money:true, align:"right" },
                { key:"balance",        label:"Balance", money:true, align:"right" },
                { key:"status",         label:"Status", status:true }
            ]
        },
        interest: {
            title: "Interest Earned",
            endpoint: "report_interest",
            columns: [
                { key:"payment_date", label:"Date" },
                { key:"full_name",    label:"Borrower" },
                { key:"rfid_no",      label:"RFID" },
                { key:"loan_id",      label:"Loan #" },
                { key:"amount",       label:"Payment", money:true, align:"right" },
                { key:"interest",     label:"Interest", money:true, align:"right" },
                { key:"principal",    label:"Principal", money:true, align:"right" }
            ]
        },
        outstanding: {
            title: "Outstanding Balances",
            endpoint: "report_outstanding",
            columns: [
                { key:"full_name",      label:"Borrower" },
                { key:"rfid_no",        label:"RFID" },
                { key:"loan_amount",    label:"Loan Amount", money:true, align:"right" },
                { key:"total_payable",  label:"Total Payable", money:true, align:"right" },
                { key:"amount_paid",    label:"Paid", money:true, align:"right" },
                { key:"balance",        label:"Balance", money:true, align:"right" },
                { key:"start_date",     label:"Start Date" },
                { key:"status",         label:"Status", status:true }
            ]
        },
        overdue: {
            title: "Overdue Accounts",
            endpoint: "report_overdue",
            columns: [
                { key:"full_name",     label:"Borrower" },
                { key:"rfid_no",       label:"RFID" },
                { key:"next_due",      label:"Next Due" },
                { key:"days_overdue",  label:"Days Overdue", align:"right" },
                { key:"total_payable", label:"Total Payable", money:true, align:"right" },
                { key:"amount_paid",   label:"Paid", money:true, align:"right" },
                { key:"balance",       label:"Balance", money:true, align:"right" }
            ]
        }
    };

    /* SUMMARY LABELS */
    const SUMMARY_LABELS = {
        count:          "Records",
        total:          "Total",
        total_amount:   "Total Released",
        total_interest: "Total Interest",
        total_borrowed: "Total Borrowed",
        total_paid:     "Total Paid",
        total_balance:  "Total Balance",
        loans:          "Number of Loans"
    };

    const SUMMARY_MONEY = [
        "total", "total_amount", "total_interest",
        "total_borrowed", "total_paid", "total_balance"
    ];

    /* HELPERS */
    function esc(v) {
        return String(v ?? "").replace(/[&<>"']/g, m => ({
            "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"
        }[m]));
    }
    function money(v) {
        return "₱" + Number(v || 0).toLocaleString("en-PH", {
            minimumFractionDigits: 2, maximumFractionDigits: 2
        });
    }
    function statusClass(s) {
        const k = String(s || "").toLowerCase().trim();
        if (k === "active")    return "status-active";
        if (k === "paid")      return "status-paid";
        if (k === "completed") return "status-completed";
        if (k === "overdue")   return "status-overdue";
        if (k === "pending")   return "status-pending";
        return "";
    }
    function formatDate(v) {
        if (!v) return "--";
        const d = new Date(String(v).length <= 10 ? v + "T00:00:00" : v);
        if (isNaN(d.getTime())) return v;
        return d.toLocaleDateString("en-PH", { year:"numeric", month:"short", day:"numeric" });
    }

    /* SHOW/HIDE RFID FIELD */
    reportType.addEventListener("change", function () {
        if (reportType.value === "statement") {
            rfidGroup.style.display = "block";
            dateFrom.disabled = true;
            dateTo.disabled   = true;
            statementRfid.focus();
        } else {
            rfidGroup.style.display = "none";
            dateFrom.disabled = false;
            dateTo.disabled   = false;
        }
    });

    /* GENERATE */
    generateBtn.addEventListener("click", function () {
        const type = reportType.value;

        if (type === "statement") {
            const rfid = statementRfid.value.trim();
            if (!rfid) {
                alert("Please enter or scan the borrower's RFID.");
                statementRfid.focus();
                return;
            }
            loadStatement(rfid);
            return;
        }

        const cfg = REPORT_CONFIG[type];
        if (!cfg) return;

        const params = new URLSearchParams({
            action: cfg.endpoint,
            from:   dateFrom.value || "",
            to:     dateTo.value   || "",
            _:      Date.now().toString()
        });

        generateBtn.disabled = true;
        generateBtn.textContent = "Loading...";

        fetch("api.php?" + params.toString(), { cache: "no-store" })
            .then(r => {
                if (!r.ok) throw new Error("Server error");
                return r.json();
            })
            .then(data => {
                if (!data.success) {
                    alert(data.message || "Unable to load report.");
                    return;
                }

                lastConfig = cfg;
                lastRows   = Array.isArray(data.rows) ? data.rows : [];

                reportTitle.textContent = cfg.title;

                if (data.from && data.to) {
                    reportMeta.style.display = "block";
                    reportMeta.textContent =
                        "Period: " + formatDate(data.from) + "  →  " + formatDate(data.to);
                } else {
                    reportMeta.style.display = "none";
                }

                renderSummary(data.summary || {});
                renderTable(cfg, lastRows);

                printBtn.disabled = false;
                csvBtn.disabled   = false;
            })
            .catch(err => {
                console.error(err);
                alert("Unable to connect to the server.");
            })
            .finally(() => {
                generateBtn.disabled = false;
                generateBtn.textContent = "📊 Generate";
            });
    });

    /* STATEMENT */
    function loadStatement(rfid) {
        generateBtn.disabled = true;
        generateBtn.textContent = "Loading...";

        fetch("api.php?action=report_statement&rfid=" + encodeURIComponent(rfid) + "&_=" + Date.now(), { cache: "no-store" })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    alert(data.message || "Borrower not found.");
                    return;
                }

                lastConfig = null;
                lastRows   = [];

                reportTitle.textContent = "Borrower Statement";
                reportMeta.style.display = "block";
                reportMeta.textContent =
                    "Generated: " + new Date().toLocaleString("en-PH");

                renderSummary(data.summary || {});
                renderStatement(data.borrower || {}, data.loans || [], data.payments || []);

                printBtn.disabled = false;
                csvBtn.disabled   = true;
            })
            .catch(err => {
                console.error(err);
                alert("Unable to connect to the server.");
            })
            .finally(() => {
                generateBtn.disabled = false;
                generateBtn.textContent = "📊 Generate";
            });
    }

    /* SUMMARY */
    function renderSummary(summary) {
        const keys = Object.keys(summary);
        if (keys.length === 0) {
            summaryRow.innerHTML = "";
            return;
        }

        summaryRow.innerHTML = keys.map(k => {
            const label = SUMMARY_LABELS[k] || k.replace(/_/g, " ");
            let value;
            if (SUMMARY_MONEY.indexOf(k) !== -1) {
                value = money(summary[k]);
            } else {
                value = String(summary[k] ?? "0");
            }
            return `
                <div class="summary-box">
                    <div class="summary-label">${esc(label)}</div>
                    <div class="summary-value">${esc(value)}</div>
                </div>`;
        }).join("");
    }

    /* TABLE */
    function renderTable(cfg, rows) {
        if (!rows.length) {
            reportBody.innerHTML = `<div class="empty-row">No records found for this report.</div>`;
            return;
        }

        const head = cfg.columns.map(c => `<th>${esc(c.label)}</th>`).join("");

        const body = rows.map(row => {
            const tds = cfg.columns.map(c => {
                const raw = row[c.key];

                if (c.status) {
                    return `<td><span class="status ${statusClass(raw)}">${esc(raw || "--")}</span></td>`;
                }

                if (c.money) {
                    return `<td class="num">${money(raw)}</td>`;
                }

                if (c.key.indexOf("date") !== -1 || c.key === "next_due" || c.key === "start_date") {
                    return `<td>${esc(formatDate(raw))}</td>`;
                }

                return `<td>${esc(raw ?? "--")}</td>`;
            }).join("");
            return `<tr>${tds}</tr>`;
        }).join("");

        reportBody.innerHTML = `
            <div class="table-container">
                <table>
                    <thead><tr>${head}</tr></thead>
                    <tbody>${body}</tbody>
                </table>
            </div>`;
    }

    /* STATEMENT VIEW */
    function renderStatement(borrower, loans, payments) {
        let html = "";

        /* BORROWER INFO */
        html += `
            <div class="statement-card">
                <div class="statement-name">${esc(borrower["full name"] || "Unknown")}</div>
                <div class="statement-detail">
                    <strong>RFID:</strong> ${esc(borrower["Rfid no"] || "--")}<br>
                    <strong>Address:</strong> ${esc(borrower["adres"] || "--")}<br>
                    <strong>Contact:</strong> ${esc(borrower["cont no"] || "--")}<br>
                    <strong>Occupation:</strong> ${esc(borrower["occup"] || "--")}<br>
                    <strong>Registered:</strong> ${esc(borrower["dtreg"] || "--")}
                </div>
            </div>`;

        /* LOANS */
        html += `<div class="statement-section">
            <div class="statement-label">Loans (${loans.length})</div>`;

        if (loans.length === 0) {
            html += `<div class="empty-row">No loans on record.</div>`;
        } else {
            html += `<div class="table-container"><table>
                <thead><tr>
                    <th>Loan #</th>
                    <th>Start</th>
                    <th>Amount</th>
                    <th class="num">Interest</th>
                    <th class="num">Total Payable</th>
                    <th class="num">Paid</th>
                    <th class="num">Balance</th>
                    <th>Status</th>
                </tr></thead><tbody>`;

            loans.forEach(l => {
                html += `<tr>
                    <td>${esc(l.id)}</td>
                    <td>${esc(formatDate(l.start_date))}</td>
                    <td class="num">${money(l.loan_amount)}</td>
                    <td class="num">${money(l.total_interest)}</td>
                    <td class="num">${money(l.total_payable)}</td>
                    <td class="num">${money(l.amount_paid)}</td>
                    <td class="num">${money(l.balance)}</td>
                    <td><span class="status ${statusClass(l.status)}">${esc(l.status)}</span></td>
                </tr>`;
            });

            html += `</tbody></table></div>`;
        }
        html += `</div>`;

        /* PAYMENTS */
        html += `<div class="statement-section">
            <div class="statement-label">Payments (${payments.length})</div>`;

        if (payments.length === 0) {
            html += `<div class="empty-row">No payments on record.</div>`;
        } else {
            html += `<div class="table-container"><table>
                <thead><tr>
                    <th>Date</th>
                    <th>Loan #</th>
                    <th class="num">Amount</th>
                    <th>Method</th>
                    <th class="num">Balance Before</th>
                    <th class="num">Balance After</th>
                    <th>Notes</th>
                </tr></thead><tbody>`;

            payments.forEach(p => {
                html += `<tr>
                    <td>${esc(formatDate(p.payment_date))}</td>
                    <td>${esc(p.loan_id)}</td>
                    <td class="num">${money(p.amount)}</td>
                    <td>${esc(p.payment_method || "--")}</td>
                    <td class="num">${money(p.outstanding_before)}</td>
                    <td class="num">${money(p.outstanding_after)}</td>
                    <td>${esc(p.notes || "--")}</td>
                </tr>`;
            });

            html += `</tbody></table></div>`;
        }
        html += `</div>`;

        reportBody.innerHTML = html;
    }

    /* PRINT */
    printBtn.addEventListener("click", function () {
        window.print();
    });

    /* CSV */
    csvBtn.addEventListener("click", function () {
        if (!lastConfig || !lastRows.length) {
            alert("Generate a report first.");
            return;
        }

        const cfg = lastConfig;
        const head = cfg.columns.map(c => '"' + c.label.replace(/"/g, '""') + '"').join(",");

        const body = lastRows.map(row => {
            return cfg.columns.map(c => {
                let v = row[c.key];
                if (v === null || v === undefined) v = "";
                v = String(v).replace(/"/g, '""');
                return '"' + v + '"';
            }).join(",");
        }).join("\r\n");

        const csv = head + "\r\n" + body;

        const blob = new Blob(["\uFEFF" + csv], { type: "text/csv;charset=utf-8;" });
        const url  = URL.createObjectURL(blob);

        const a = document.createElement("a");
        a.href = url;
        a.download = cfg.endpoint + "_" + dateFrom.value + "_to_" + dateTo.value + ".csv";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });

});
</script>

</body>
</html>