<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once __DIR__."/auth.php"; requireLogin(); ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RFID Money Lending Management System - Payments</title>

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

#sidebar.open { transform: translateX(0); }

.sidebar-close { display: flex; justify-content: flex-end; }

.sidebar-close button {
    background: none;
    border: none;
    color: white;
    font-size: 18px;
    cursor: pointer;
    padding: 4px 6px;
}

.sidebar-logo { display: flex; justify-content: center; padding: 4px 6px 24px; }

.sidebar-logo img { width: 130px; height: 130px; border-radius: 30px; object-fit: cover; }

.sidebar-menu {
    list-style: none;
    margin: 6px 0 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex: 1;
}

.sidebar-menu li a {
    display: flex;
    align-items: center;
    padding: 12px 13px;
    border-radius: 9px;
    color: rgba(255, 255, 255, .82);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: .2s;
}

.sidebar-menu li a:hover,
.sidebar-menu li a.active { background: #1f9e6f; color: white; }

.sidebar-menu li a.active { font-weight: 700; }

/* OVERLAY */
#overlay {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: rgba(13, 59, 46, .45);
    z-index: 150;
    opacity: 0;
    visibility: hidden;
    transition: opacity .25s ease, visibility .25s ease;
}

#overlay.show { opacity: 1; visibility: visible; }

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

.topbar h1 { font-size: 17px; margin: 0; font-weight: 700; flex: 1; color: #1c2b27; }

.notification { position: relative; font-size: 18px; color: #3c4b47; }

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
.content { width: 100%; padding: 16px 12px 40px; overflow-x: hidden; }

/* CARD */
.card {
    width: 100%;
    background: white;
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(16, 44, 34, .06), 0 1px 2px rgba(16, 44, 34, .04);
    padding: 16px;
    margin-bottom: 16px;
    overflow: hidden;
}

/* SECTION HEADER */
.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding-bottom: 12px;
    border-bottom: 2px solid #1a8a63;
    margin-bottom: 16px;
}

.section-title { font-size: 15px; font-weight: 700; color: #0f4a38; }

.btn-record {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #146c4f;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 9px 14px;
    font-family: inherit;
    font-size: 12.5px;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    white-space: nowrap;
    transition: .2s;
}

.btn-record:hover { background: #0f573f; }
.btn-record:active { transform: scale(.97); }

/* SUMMARY STATS */
.stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 16px;
}

.stat-box {
    background: #f4faf7;
    border: 1px solid #e6ece9;
    border-radius: 9px;
    padding: 12px;
}

.stat-label {
    font-size: 10.5px;
    color: #6b7a76;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    margin-bottom: 5px;
}

.stat-value { font-size: 15px; font-weight: 800; color: #0f4a38; }

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
    min-width: 0;
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
    scrollbar-width: thin;
    scrollbar-color: #146c4f #e6ece9;
    padding-bottom: 5px;
    max-width: 100%;
}

.table-container::-webkit-scrollbar { height: 8px; }
.table-container::-webkit-scrollbar-track { background: #e6ece9; border-radius: 10px; }
.table-container::-webkit-scrollbar-thumb { background: #146c4f; border-radius: 10px; }
.table-container::-webkit-scrollbar-thumb:hover { background: #0f573f; }

table { width: 100%; min-width: 900px; border-collapse: collapse; font-size: 12.5px; }

thead tr { background: #146c4f; }

th {
    color: white;
    text-align: left;
    padding: 12px 14px;
    font-weight: 700;
    white-space: nowrap;
}

th:first-child { border-top-left-radius: 8px; }
th:last-child { border-top-right-radius: 8px; }

td { padding: 13px 14px; border-bottom: 1px solid #e6ece9; white-space: nowrap; }

tbody tr:hover { background: #f8fbf9; }

.amount-cell { font-weight: 700; color: #146c4f; }

.method-badge {
    display: inline-block;
    padding: 4px 9px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    background: #eaf6ef;
    color: #146c4f;
    text-transform: capitalize;
}

/* TABLE FOOTER */
.table-footer {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 16px;
    color: #6b7a76;
    font-size: 13px;
    width: 100%;
}

/* EMPTY / LOADING */
.empty-row { text-align: center; padding: 30px !important; color: #7b8783; }
.loading-row { text-align: center; padding: 30px !important; color: #6b7a76; }

/* MOBILE */
@media (max-width: 600px) {
    .topbar { height: 58px; padding: 12px 14px; }
    .topbar h1 { font-size: 16px; }
    .content { padding: 12px 10px 30px; }
    .card { padding: 13px; border-radius: 9px; }
    .section-title { font-size: 14px; }
    .stats-row { grid-template-columns: repeat(3, 1fr); gap: 8px; }
    .stat-box { padding: 10px; }
    .stat-value { font-size: 13px; }
    .table-container { width: 100%; overflow-x: auto; overflow-y: hidden; }
    table { min-width: 900px; }
    th, td { padding: 11px 12px; }
}

@media (max-width: 360px) {
    .content { padding-left: 8px; padding-right: 8px; }
    .card { padding: 11px; }
    .topbar { padding-left: 12px; padding-right: 12px; }
    .btn-record { padding: 8px 11px; font-size: 11.5px; }
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
        <li><a href="payment.php" class="active">Payments</a></li>
        <li><a href="report.php">Reports</a></li>
        <li><a href="scanner.php">RFID Scanner</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</aside>

<!-- OVERLAY -->
<div id="overlay"></div>

<!-- TOP BAR -->
<div class="topbar">
    <button id="menuButton" class="menu-button" type="button">☰</button>
    <h1>Payments</h1>
</div>

<!-- MAIN CONTENT -->
<div class="content">

    <!-- PAYMENT HISTORY -->
    <section class="card">
        <div class="section-header">
            <div class="section-title">Payment History</div>
            <!-- Record Payment opens the RFID Scanner; the scanner reads the card, shows the monthly amount due and forwards to recpay.php -->
            <a href="scanner.php" class="btn-record">+ Record Payment</a>
        </div>

        <!-- SUMMARY -->
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-label">Payments</div>
                <div class="stat-value" id="statCount">0</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Total Collected</div>
                <div class="stat-value" id="statTotal">₱0.00</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Today</div>
                <div class="stat-value" id="statToday">₱0.00</div>
            </div>
        </div>

        <!-- SEARCH -->
        <div class="search-container">
            🔎
            <input type="text" id="searchInput" placeholder="Search by borrower name or RFID no...">
        </div>

        <!-- TABLE -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Borrower</th>
                        <th>RFID No.</th>
                        <th>Loan ID</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Balance Before</th>
                        <th>Balance After</th>
                        <th>Notes</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody id="paymentsTable">
                    <tr><td colspan="10" class="loading-row">Loading payments...</td></tr>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <span id="resultCount">Showing 0 payments</span>
        </div>
    </section>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* SIDEBAR */
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const menuButton = document.getElementById('menuButton');
    const closeMenu = document.getElementById('closeMenu');

    function openMenu() { sidebar.classList.add('open'); overlay.classList.add('show'); }
    function shut() { sidebar.classList.remove('open'); overlay.classList.remove('show'); }

    menuButton.onclick = openMenu;
    closeMenu.onclick = shut;
    overlay.onclick = shut;

    /* STATE */
    let rows = [];

    const table = document.getElementById('paymentsTable');
    const search = document.getElementById('searchInput');
    const resultCount = document.getElementById('resultCount');
    const statCount = document.getElementById('statCount');
    const statTotal = document.getElementById('statTotal');
    const statToday = document.getElementById('statToday');

    /* HELPERS */
    function money(value) {
        return '₱' + Number(value || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
    }

    function formatDate(value) {
        if (!value) return '--';
        const d = new Date(value + (String(value).length <= 10 ? 'T00:00:00' : ''));
        if (isNaN(d.getTime())) return value;
        return d.toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function methodLabel(method) {
        const map = { cash: 'Cash', gcash: 'GCash', bank_transfer: 'Bank Transfer', check: 'Check' };
        return map[method] || (method || '--');
    }

    /* RENDER */
    function render() {
        const term = search.value.trim().toLowerCase();

        const filtered = rows.filter(function (p) {
            if (!term) return true;
            const name = (p.full_name || '').toLowerCase();
            const rfid = (p.rfid_no || '').toLowerCase();
            return name.includes(term) || rfid.includes(term);
        });

        if (filtered.length === 0) {
            table.innerHTML = '<tr><td colspan="10" class="empty-row">No payments found.</td></tr>';
        } else {
            table.innerHTML = filtered.map(function (p) {
                return '<tr>' +
                    '<td>' + formatDate(p.payment_date) + '</td>' +
                    '<td>' + (p.full_name || 'Unknown') + '</td>' +
                    '<td>' + (p.rfid_no || '--') + '</td>' +
                    '<td>' + (p.loan_id || '--') + '</td>' +
                    '<td class="amount-cell">' + money(p.amount) + '</td>' +
                    '<td><span class="method-badge">' + methodLabel(p.payment_method) + '</span></td>' +
                    '<td>' + money(p.outstanding_before) + '</td>' +
                    '<td>' + money(p.outstanding_after) + '</td>' +
                    '<td>' + (p.notes ? p.notes : '--') + '</td>' +
                    '<td>' + (p.recorded_by_name || p.recorded_by || '--') + '</td>' +
                    '</tr>';
            }).join('');
        }

        resultCount.textContent = 'Showing ' + filtered.length + ' of ' + rows.length + ' payment' + (rows.length === 1 ? '' : 's');
    }

    /* SUMMARY STATS */
    function updateStats() {
        statCount.textContent = rows.length;

        const total = rows.reduce(function (sum, p) {
            return sum + Number(p.amount || 0);
        }, 0);
        statTotal.textContent = money(total);

        const today = new Date().toISOString().slice(0, 10);
        const todayTotal = rows
            .filter(function (p) { return String(p.payment_date).slice(0, 10) === today; })
            .reduce(function (sum, p) { return sum + Number(p.amount || 0); }, 0);
        statToday.textContent = money(todayTotal);
    }

    /* LOAD PAYMENTS */
    function load() {
        fetch('api.php?action=payments')
            .then(function (response) {
                if (!response.ok) throw new Error('Server error.');
                return response.json();
            })
            .then(function (data) {
                rows = Array.isArray(data.payments) ? data.payments : [];
                updateStats();
                render();
            })
            .catch(function (error) {
                console.error('Error loading payments:', error);
                table.innerHTML = '<tr><td colspan="10" class="empty-row">Unable to connect to the database.</td></tr>';
            });
    }

    /* SEARCH */
    search.oninput = render;

    /* INITIAL LOAD */
    load();

});
</script>
</body>
</html>