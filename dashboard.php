<!DOCTYPE html>
<html lang="en" style="margin:0; padding:0; background:#f4f7f5; width:100%; height:100%; overflow:hidden;">
<head>
<?php require_once __DIR__."/auth.php"; requireLogin(); ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RFID Money Lending Management System - Dashboard</title>
<style>
  #sidebar.open{ transform:translateX(0) !important; }
  #overlay.open{ opacity:1 !important; visibility:visible !important; }
</style>
</head>
<body style="box-sizing:border-box; margin:0; font-family:'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#1c2b27; width:100%; height:100dvh; background:#f4f7f5; position:relative; overflow-y:auto; overflow-x:hidden; -webkit-font-smoothing:antialiased; scrollbar-width:thin; scrollbar-color:#b7c5c0 #f4f7f5;">

<style>
  html, body {
    scrollbar-gutter: stable;
  }

  body::-webkit-scrollbar {
    width: 7px;
  }

  body::-webkit-scrollbar-track {
    background: #f4f7f5;
  }

  body::-webkit-scrollbar-thumb {
    background: #b7c5c0;
    border-radius: 10px;
  }

  body::-webkit-scrollbar-thumb:hover {
    background: #8fa39b;
  }

</style>

  <!-- Sidebar -->
  <aside id="sidebar" style="box-sizing:border-box; position:fixed; top:0; left:0; bottom:0; width:min(250px,82vw); background:linear-gradient(180deg,#0d3b2e 0%, #0f4a38 100%); transform:translateX(-100%); transition:transform .25s; z-index:200; display:flex; flex-direction:column; padding:20px 14px;">

    <div style="display:flex; justify-content:flex-end;">
      <button id="closeMenu" type="button" aria-label="Close menu" style="background:none; border:none; color:#fff; font-size:18px; cursor:pointer; opacity:.8; padding:2px 4px;">✕</button>
    </div>

    <div style="display:flex; justify-content:center; padding:4px 6px 24px;">
      <img src="image/logo.png" alt="Logo" style="width:120px; height:120px; border-radius:20px; object-fit:cover;">
    </div>

    <ul style="list-style:none; margin:6px 0 0 0; padding:0; display:flex; flex-direction:column; gap:4px; flex:1;">
      <li><a href="dashboard.php" style="box-sizing:border-box; display:flex; align-items:center; gap:12px; padding:12px 13px; border-radius:9px; background:#1f9e6f; color:#fff; text-decoration:none; font-size:14px; font-weight:700; box-shadow:0 1px 3px rgba(16,44,34,.06), 0 1px 2px rgba(16,44,34,.04);">Home</a></li>
      <li><a href="borrowers.php" style="box-sizing:border-box; display:flex; align-items:center; gap:12px; padding:12px 13px; border-radius:9px; color:rgba(255,255,255,.82); text-decoration:none; font-size:14px; font-weight:500;">Borrowers</a></li>
      <li><a href="loan.php" style="box-sizing:border-box; display:flex; align-items:center; gap:12px; padding:12px 13px; border-radius:9px; color:rgba(255,255,255,.82); text-decoration:none; font-size:14px; font-weight:500;">Loans</a></li>
      <li><a href="payment.php" style="box-sizing:border-box; display:flex; align-items:center; gap:12px; padding:12px 13px; border-radius:9px; color:rgba(255,255,255,.82); text-decoration:none; font-size:14px; font-weight:500;">Payments</a></li>
      <li><a href="report.php" style="box-sizing:border-box; display:flex; align-items:center; gap:12px; padding:12px 13px; border-radius:9px; color:rgba(255,255,255,.82); text-decoration:none; font-size:14px; font-weight:500;">Reports</a></li>
      <li><a href="scanner.php" style="box-sizing:border-box; display:flex; align-items:center; gap:12px; padding:12px 13px; border-radius:9px; color:rgba(255,255,255,.82); text-decoration:none; font-size:14px; font-weight:500;">RFID Scanner</a></li>
      <li><a href="logout.php" style="box-sizing:border-box; display:flex; align-items:center; gap:12px; padding:12px 13px; border-radius:9px; color:rgba(255,255,255,.82); text-decoration:none; font-size:14px; font-weight:500;">Logout</a></li>
    </ul>

  </aside>

  <!-- Overlay: clicking it navigates back to # and closes the sidebar -->
  <button id="overlay" type="button" aria-label="Close menu" style="display:block; position:fixed; top:0; right:0; bottom:0; left:0; width:100%; height:100%; border:none; padding:0; background:rgba(13,59,46,.45); z-index:150; opacity:0; visibility:hidden; transition:opacity .25s; cursor:pointer;"></button>

  <!-- Top bar -->
  <div style="box-sizing:border-box; background:#ffffff; border-bottom:1px solid #e6ece9; color:#1c2b27; display:flex; align-items:center; gap:14px; padding:14px 16px; height:64px;">
    <button id="menuButton" type="button" aria-label="Open menu" style="background:none; border:none; color:#3c4b47; font-size:20px; cursor:pointer; padding:0; display:inline-flex;">☰</button>
    <h1 style="font-size:17px; margin:0; font-weight:700; flex:1; color:#1c2b27;">Dashboard</h1>
  </div>

  <!-- Content -->
  <div style="box-sizing:border-box; padding:16px 14px 40px; width:100%;">

    <!-- Greeting -->
    <div style="margin-bottom:16px;">
      <div style="font-size:20px; font-weight:800; color:#1c2b27;">Good morning!</div>
      <div style="font-size:13px; color:#6b7a76; margin-top:4px;">Here's what's happening today.</div>
    </div>

    <!-- Stat cards -->
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">

      <!-- Card 1: Total Loan Amount -->
      <div style="box-sizing:border-box; background:#e3f3ea; border-radius:10px; box-shadow:0 1px 3px rgba(16,44,34,.06), 0 1px 2px rgba(16,44,34,.04); padding:14px; display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
        <div style="min-width:0;">
          <div style="font-size:12px; color:#3c5a50; font-weight:600;">Total Loan Amount</div>
          <div id="totalLoanAmount" style="font-size:20px; font-weight:800; color:#146c4f; margin-top:6px;">₱0</div>
          <div style="font-size:11px; color:#5f7a70; font-weight:500; margin-top:3px;">Total amount of all loans</div>
        </div>
        <div style="width:38px; height:38px; border-radius:50%; background:rgba(255,255,255,.75); color:#146c4f; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0;">👛</div>
      </div>

      <!-- Card 2: Active Loans -->
      <div style="box-sizing:border-box; background:#eaf3fc; border-radius:10px; box-shadow:0 1px 3px rgba(16,44,34,.06), 0 1px 2px rgba(16,44,34,.04); padding:14px; display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
        <div style="min-width:0;">
          <div style="font-size:12px; color:#3c5570; font-weight:600;">Active Loans</div>
          <div id="activeLoans" style="font-size:20px; font-weight:800; color:#4388d5; margin-top:6px;">0</div>
          <div style="font-size:11px; color:#5f7080; font-weight:500; margin-top:3px;">Currently active loans</div>
        </div>
        <div style="width:38px; height:38px; border-radius:50%; background:rgba(255,255,255,.75); color:#4388d5; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0;">📋</div>
      </div>

      <!-- Card 3: Overdue Loans -->
      <div style="box-sizing:border-box; background:#fdeee3; border-radius:10px; box-shadow:0 1px 3px rgba(16,44,34,.06), 0 1px 2px rgba(16,44,34,.04); padding:14px; display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
        <div style="min-width:0;">
          <div style="font-size:12px; color:#7a4f2e; font-weight:600;">Overdue Loans</div>
          <div id="overdueLoans" style="font-size:20px; font-weight:800; color:#e0722e; margin-top:6px;">0</div>
          <div style="font-size:11px; color:#8a6a4f; font-weight:500; margin-top:3px;">Loans past due date</div>
        </div>
        <div style="width:38px; height:38px; border-radius:50%; background:rgba(255,255,255,.75); color:#e0722e; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0;">⚠️</div>
      </div>

      <!-- Card 4: Total Collected -->
      <div style="box-sizing:border-box; background:#e0f2f1; border-radius:10px; box-shadow:0 1px 3px rgba(16,44,34,.06), 0 1px 2px rgba(16,44,34,.04); padding:14px; display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
        <div style="min-width:0;">
          <div style="font-size:12px; color:#2e5a56; font-weight:600;">Total Collected</div>
          <div id="totalCollected" style="font-size:20px; font-weight:800; color:#0f7a6e; margin-top:6px;">₱0</div>
          <div style="font-size:11px; color:#4f7a75; font-weight:500; margin-top:3px;">Total amount collected</div>
        </div>
        <div style="width:38px; height:38px; border-radius:50%; background:rgba(255,255,255,.75); color:#0f7a6e; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0;">💵</div>
      </div>

    </div>

    <!-- Recent Loan Applications -->
    <section style="box-sizing:border-box; background:#ffffff; border-radius:10px; box-shadow:0 1px 3px rgba(16,44,34,.06), 0 1px 2px rgba(16,44,34,.04); padding:16px; margin-bottom:16px;">

      <div style="display:flex; align-items:center; justify-content:space-between; padding-bottom:12px; border-bottom:2px solid #1a8a63; margin-bottom:4px;">
        <div style="font-size:15px; font-weight:700; color:#0f4a38;">Recent Loan Applications</div>
        <a href="loan.php" style="color:#146c4f; font-size:12px; font-weight:700; text-decoration:none;">View all&nbsp;›</a>
      </div>

      <div id="recentLoansContainer" style="text-align:center; padding:30px 10px; color:#7b8783; font-size:13px;">
          No recent loan applications.
      </div>  

    </section>

    <!-- Upcoming Payments -->
    <section style="box-sizing:border-box; background:#ffffff; border-radius:10px; box-shadow:0 1px 3px rgba(16,44,34,.06), 0 1px 2px rgba(16,44,34,.04); padding:16px;">

      <div style="display:flex; align-items:center; justify-content:space-between; padding-bottom:12px; border-bottom:2px solid #1a8a63; margin-bottom:4px;">
        <div style="font-size:15px; font-weight:700; color:#0f4a38;">Upcoming Payments</div>
        <a href="payment.php" style="color:#146c4f; font-size:12px; font-weight:700; text-decoration:none;">View all&nbsp;›</a>
      </div>

      <div id="upcomingPaymentsContainer" style="text-align:center; padding:30px 10px; color:#7b8783; font-size:13px;">
        No upcoming payments.
      </div>

    </section>

  </div>




<script>
document.addEventListener("DOMContentLoaded", function(){

    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("overlay");
    const menu = document.getElementById("menuButton");
    const close = document.getElementById("closeMenu");

    function open() {
        sidebar.classList.add("open");
        overlay.classList.add("open");
    }

    function shut() {
        sidebar.classList.remove("open");
        overlay.classList.remove("open");
    }

    if (menu) menu.onclick = open;
    if (close) close.onclick = shut;
    if (overlay) overlay.onclick = shut;


    // ==============================
    // LOAD DASHBOARD DATA
    // ==============================

    function loadDashboard() {

        fetch("api.php?action=dashboard&_=" + Date.now(), {
            cache: "no-store"
        })
        .then(response => {

            if (!response.ok) {
                throw new Error("Dashboard API error");
            }

            return response.json();

        })
        .then(d => {

            // ------------------------------
            // STATISTICS
            // ------------------------------

            document.getElementById("totalLoanAmount").textContent =
                "₱" + Number(
                    d.stats?.total_loan_amount || 0
                ).toLocaleString("en-PH", {
                    minimumFractionDigits: 2
                });


            document.getElementById("activeLoans").textContent =
                d.stats?.active_loans || 0;


            document.getElementById("overdueLoans").textContent =
                d.stats?.overdue_loans || 0;


            document.getElementById("totalCollected").textContent =
                "₱" + Number(
                    d.stats?.total_collected || 0
                ).toLocaleString("en-PH", {
                    minimumFractionDigits: 2
                });


            // ------------------------------
            // RECENT LOANS
            // ------------------------------

            const container =
                document.getElementById("recentLoansContainer");

            const recent = d.recent || [];

            if (recent.length === 0) {

                container.style.textAlign = "center";

                container.innerHTML =
                    "No recent loan applications.";

            } else {

                container.style.textAlign = "left";


                container.innerHTML = recent.map(x => {

                    const name = x.full_name || "Unknown";
                    const initials = name
                        .split(/\s+/)
                        .map(s => s[0])
                        .slice(0, 2)
                        .join("")
                        .toUpperCase();

                    return `
                        <div style="
                            display:flex;
                            align-items:center;
                            gap:10px;
                            padding:13px 0;
                            border-bottom:1px solid #e6ece9;
                        ">

                            <div style="
                                width:42px;
                                height:42px;
                                border-radius:50%;
                                background:#e3f3ea;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                color:#146c4f;
                                font-size:13.5px;
                                font-weight:700;
                                flex-shrink:0;
                            ">
                                ${initials}
                            </div>

                            <div style="flex:1">

                                <div style="
                                    font-size:13.5px;
                                    font-weight:700;
                                ">
                                    ${esc(x.full_name)}
                                </div>

                                <div style="
                                    font-size:12px;
                                    color:#6b7a76;
                                    margin-top:2px;
                                ">
                                    RFID: ${esc(x.rfid_no)}
                                </div>

                            </div>

                            <div style="text-align:right">

                                <div style="
                                    font-size:14px;
                                    font-weight:700;
                                ">
                                    ₱${Number(
                                        x.loan_amount || 0
                                    ).toLocaleString("en-PH", {
                                        minimumFractionDigits:2
                                    })}
                                </div>

                                <div style="
                                    font-size:11px;
                                    color:#6b7a76;
                                ">
                                    ${esc(x.status)}
                                </div>

                            </div>

                        </div>
                    `;

                }).join("");

            }


            // ------------------------------
            // UPCOMING PAYMENTS
            // ------------------------------

            const upContainer =
                document.getElementById("upcomingPaymentsContainer");

            const upcoming = d.upcoming || [];

            if (upcoming.length === 0) {

                upContainer.style.textAlign = "center";

                upContainer.innerHTML = "No upcoming payments.";

            } else {

                upContainer.style.textAlign = "left";


                const today = new Date();
                today.setHours(0, 0, 0, 0);


                upContainer.innerHTML = upcoming.map(x => {

                    const dueDate = new Date(
                        String(x.due_date) + "T00:00:00"
                    );

                    const diffDays = Math.round(
                        (dueDate - today) / 86400000
                    );


                    let badgeColor = "#146c4f";
                    let badgeText = "Due " + x.due_date;


                    if (diffDays < 0) {

                        badgeColor = "#e14b4b";
                        badgeText =
                            Math.abs(diffDays) + "d overdue";

                    } else if (diffDays === 0) {

                        badgeColor = "#e89b32";
                        badgeText = "Due today";

                    } else if (diffDays <= 3) {

                        badgeColor = "#e89b32";
                        badgeText =
                            "Due in " + diffDays + "d";

                    } else {

                        badgeText =
                            "Due in " + diffDays + "d";

                    }


                    const name = x.full_name || "Unknown";

                    const initials = name
                        .split(/\s+/)
                        .filter(Boolean)
                        .map(s => s[0])
                        .slice(0, 2)
                        .join("")
                        .toUpperCase();


                    return `
                        <div style="
                            display:flex;
                            align-items:center;
                            gap:10px;
                            padding:13px 0;
                            border-bottom:1px solid #e6ece9;
                        ">

                            <div style="
                                width:42px;
                                height:42px;
                                border-radius:50%;
                                background:#fdeee3;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                color:#e0722e;
                                font-size:13.5px;
                                font-weight:700;
                                flex-shrink:0;
                            ">
                                ${initials}
                            </div>

                            <div style="flex:1; min-width:0;">

                                <div style="
                                    font-size:13.5px;
                                    font-weight:700;
                                ">
                                    ${esc(x.full_name)}
                                </div>

                                <div style="
                                    font-size:12px;
                                    color:#6b7a76;
                                    margin-top:2px;
                                ">
                                    RFID: ${esc(x.rfid_no)}
                                </div>

                            </div>

                            <div style="text-align:right">

                                <div style="
                                    font-size:14px;
                                    font-weight:700;
                                    color:#146c4f;
                                ">
                                    ₱${Number(
                                        x.balance || 0
                                    ).toLocaleString("en-PH", {
                                        minimumFractionDigits:2
                                    })}
                                </div>

                                <div style="
                                    font-size:11px;
                                    color:${badgeColor};
                                    font-weight:700;
                                ">
                                    ${badgeText}
                                </div>

                            </div>

                        </div>
                    `;

                }).join("");

            }

        })
        .catch(error => {

            console.error(
                "Dashboard update failed:",
                error
            );

        });

    }


    // Load immediately
    loadDashboard();


    // Automatically update every 3 seconds
    setInterval(loadDashboard, 3000);


    // Escape HTML
    function esc(v) {

        return String(v ?? "").replace(
            /[&<>"']/g,
            function(m) {

                return {
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': "&quot;",
                    "'": "&#039;"
                }[m];

            }
        );

    }

});
</script>
</body>
</html>