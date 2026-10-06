<!DOCTYPE html>
<html lang="en">

<head>
<?php require_once __DIR__."/auth.php"; requireLogin(); ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RFID Money Lending Management System - Borrowers</title>

<style>
    * { box-sizing: border-box; }

    html, body {
        margin: 0;
        padding: 0;
        width: 100%;
        min-height: 100%;
        background: #f4f7f5;
    }

    body {
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        color: #1c2b27;
        min-height: 100vh;
        overflow-x: hidden;
    }

    /* ---------- SIDEBAR ---------- */
    #sidebar {
        position: fixed;
        top: 0; left: 0; bottom: 0;
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
        width: 130px;
        height: 130px;
        border-radius: 30px;
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
    .sidebar-menu li a.active {
        background: #1f9e6f;
        color: white;
    }

    .sidebar-menu li a.active { font-weight: 700; }

    /* ---------- OVERLAY ---------- */
    #overlay {
        position: fixed;
        top: 0; right: 0; bottom: 0; left: 0;
        background: rgba(13, 59, 46, .45);
        z-index: 150;
        opacity: 0;
        visibility: hidden;
        transition: opacity .25s ease, visibility .25s ease;
    }

    #overlay.show { opacity: 1; visibility: visible; }

    /* ---------- TOP BAR ---------- */
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

    /* ---------- CONTENT ---------- */
    .content {
        width: 100%;
        padding: 16px 12px 40px;
        overflow-x: hidden;
    }

    .card {
        width: 100%;
        background: white;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(16, 44, 34, .06), 0 1px 2px rgba(16, 44, 34, .04);
        padding: 16px;
        margin-bottom: 16px;
        overflow: hidden;
    }

    #registrationCard {
    background: #eaf6ef;
    border: 1px solid #d4e9de;
    }
    .section-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f4a38;
        padding-bottom: 12px;
        border-bottom: 2px solid #1a8a63;
        margin-bottom: 16px;
    }

    /* ---------- FORM ---------- */
    .form-group { margin-bottom: 14px; }

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
        box-shadow: 0 0 0 2px rgba(26, 138, 99, .10);
    }

    .form-group input.rfid-scanning {
        border-color: #1a8a63;
        background: #eaf6ef;
        box-shadow: 0 0 0 3px rgba(26, 138, 99, .15);
    }

    .rfid-hint {
        display: block;
        margin-top: 5px;
        font-size: 11.5px;
        color: #6b7a76;
    }

    /* ---------- BUTTONS ---------- */
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

    .btn:active { transform: scale(.98); }
    .btn-save { background: #146c4f; }
    .btn-save:hover { background: #0f573f; }
    .btn-clear { background: #e14b4b; }
    .btn-clear:hover { background: #c83b3b; }
    .btn:disabled { opacity: .6; cursor: not-allowed; }

    /* ---------- SEARCH ---------- */
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

    /* ---------- TABLE ---------- */
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

    table {
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    thead tr { background: #146c4f; }

    th {
        color: white;
        text-align: left;
        padding: 12px 14px;
        font-weight: 700;
        white-space: nowrap;
    }

    th:first-child { border-top-left-radius: 8px; }
    th:last-child  { border-top-right-radius: 8px; }

    td {
        padding: 13px 14px;
        border-bottom: 1px solid #e6ece9;
        white-space: nowrap;
    }

    tbody tr:hover { background: #f8fbf9; }

    /* ---------- ACTION BUTTONS ---------- */
    .edit-btn,
    .delete-btn {
        width: 30px;
        height: 30px;
        border: none;
        border-radius: 6px;
        color: white;
        cursor: pointer;
        font-size: 14px;
    }

    .edit-btn { background: #146c4f; margin-right: 5px; }
    .edit-btn:hover { background: #0f573f; }
    .delete-btn { background: #e14b4b; }
    .delete-btn:hover { background: #c83b3b; }
    .edit-btn:active,
    .delete-btn:active { transform: scale(.92); }

    /* ---------- FOOTER / PAGINATION ---------- */
    .table-footer {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 16px;
        color: #6b7a76;
        font-size: 13px;
        width: 100%;
    }

    .pagination {
        display: flex;
        gap: 6px;
        overflow-x: auto;
        width: 100%;
        padding-bottom: 4px;
        -webkit-overflow-scrolling: touch;
    }

    .page-btn {
        min-width: 36px;
        height: 36px;
        flex-shrink: 0;
        border: 1px solid #e6ece9;
        border-radius: 7px;
        background: white;
        cursor: pointer;
        font-weight: 600;
        color: #3c4b47;
    }

    .page-btn:hover { background: #f0f5f2; }
    .page-btn:active { transform: scale(.95); }

    .page-btn.active {
        border-color: #146c4f;
        background: #146c4f;
        color: white;
    }

    .page-btn:disabled { opacity: .5; cursor: not-allowed; }

    /* ---------- MISC ---------- */
    .empty-row {
        text-align: center;
        padding: 30px !important;
        color: #7b8783;
    }

    .loading-row {
        text-align: center;
        padding: 30px !important;
        color: #6b7a76;
    }

    .edit-mode { border: 2px solid #1a8a63; }

    /* ---------- MOBILE ---------- */
    @media (max-width: 600px) {
        .topbar { height: 58px; padding: 12px 14px; }
        .topbar h1 { font-size: 16px; }
        .content { padding: 12px 10px 30px; }
        .card { padding: 13px; border-radius: 9px; }
        .section-title { font-size: 14px; }
        .form-group input,
        .form-group select { font-size: 16px; }
        .table-container { width: 100%; overflow-x: auto; overflow-y: hidden; }
        table { min-width: 760px; }
        th, td { padding: 11px 12px; }
    }

    @media (max-width: 360px) {
        .content { padding-left: 8px; padding-right: 8px; }
        .card { padding: 11px; }
        .topbar { padding-left: 12px; padding-right: 12px; }
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
        <li><a href="borrowers.php" class="active">Borrowers</a></li>
        <li><a href="loan.php">Loans</a></li>
        <li><a href="payment.php">Payments</a></li>
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
    <h1>Borrowers</h1>
</div>

<!-- CONTENT -->
<div class="content">

    <!-- BORROWER REGISTRATION -->
    <section class="card" id="registrationCard">
        <div class="section-title">Borrower Registration</div>

        <form id="borrowerForm">

            <div class="form-group">
                <label for="rfid">RFID Card UID</label>
                <input type="text" id="rfid" name="rfid_uid"
                    placeholder="Click here, then scan the RFID card"
                    autocomplete="off" inputmode="none" required>
                <small class="rfid-hint">Click the box, then tap the card on the reader.</small>
            </div>

            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="full_name"
                    placeholder="Enter Full Name" required>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address"
                    placeholder="Enter Address" required>
            </div>

            <div class="form-group">
                <label for="contact">Contact Number</label>
                <input type="text" id="contact" name="contact_number"
                    placeholder="09XXXXXXXXX" maxlength="11"
                    inputmode="numeric" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="M">M</option>
                    <option value="F">F</option>
                </select>
            </div>

            <div class="form-group">
                <label for="occupation">Occupation</label>
                <input type="text" id="occupation" name="occupation"
                    placeholder="Enter Occupation" required>
            </div>

            <div class="form-group">
                <label for="dateRegistered">Date Registered</label>
                <input type="date" id="dateRegistered" name="date_registered"
                    readonly required
                    style="background:#f2f8f4; color:#0f4a38; font-weight:700; cursor:not-allowed;">
                <small class="rfid-hint">Automatically set to today's date.</small>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-save" id="saveButton">Save Borrower</button>
                <button type="button" class="btn btn-clear" id="clearButton">Clear Fields</button>
            </div>

        </form>
    </section>

    <!-- BORROWER LIST -->
    <section class="card">
        <div class="section-title">Borrower List</div>

        <div class="search-container">
            <span>🔍</span>
            <input type="text" id="searchInput" placeholder="Search borrower..." autocomplete="off">
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Address</th>
                        <th>Contact Number</th>
                        <th>Gender</th>
                        <th>Occupation</th>
                        <th>Date Registered</th>
                        <th>RFID UID</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="borrowerTable">
                    <tr><td colspan="8" class="loading-row">Loading borrowers...</td></tr>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <div id="entryInfo">Showing 0 entries</div>
            <div class="pagination" id="pagination"></div>
        </div>
    </section>

</div>

<script>

document.addEventListener("DOMContentLoaded", () => {

    /* ELEMENTS */
    const sidebar  = document.getElementById("sidebar");
    const overlay  = document.getElementById("overlay");
    const menu     = document.getElementById("menuButton");
    const close    = document.getElementById("closeMenu");
    const form     = document.getElementById("borrowerForm");
    const table    = document.getElementById("borrowerTable");
    const search   = document.getElementById("searchInput");
    const clear    = document.getElementById("clearButton");
    const save     = document.getElementById("saveButton");
    const rfidInput = document.getElementById("rfid");
    const fullNameInput = document.getElementById("fullName");
    const dateRegisteredInput = document.getElementById("dateRegistered");

    let rows = [];

    /* =====================================================
       AUTO-FILL TODAY'S DATE FOR "DATE REGISTERED"

       Called on page load, after a successful save, and
       after Clear Fields so the field always shows today.
    ===================================================== */

    function setToday() {

        const today =
            new Date()
            .toISOString()
            .slice(0, 10);

        dateRegisteredInput.value = today;

    }

        /* =========================================================
    INPUT RESTRICTIONS
    - Full Name  : letters only (A–Z, Ñ, ñ, spaces, . ' -)
    - Address    : letters, digits, spaces, common punctuation
    - Contact No.: digits only, max 11
    - Occupation : letters only (A–Z, Ñ, ñ, spaces, . ' -)
    ========================================================= */

    const NAME_ALLOWED     = /[^A-Za-zÑñ\s.'-]/g;
    const ADDRESS_ALLOWED  = /[^A-Za-z0-9Ññ\s.,#''\-\/]/g;

    /* FULL NAME — letters only, auto-capitalize each word */
    fullNameInput.addEventListener("input", function () {
        const cursorPos = this.selectionStart;

        // 1) Strip anything that is not a letter / allowed punctuation
        let value = this.value.replace(NAME_ALLOWED, "");

        // 2) Auto-capitalize the first letter of each word
        value = value.replace(/(^|\s)(\S)/g, (match, space, char) =>
            space + char.toUpperCase()
        );

        if (value !== this.value) {
            this.value = value;
            this.setSelectionRange(cursorPos, cursorPos);
        }
    });

    /* ADDRESS — letters, digits, spaces, common punctuation */
    document.getElementById("address").addEventListener("input", function () {
        const cleaned = this.value.replace(ADDRESS_ALLOWED, "");
        if (cleaned !== this.value) this.value = cleaned;
    });

    /* CONTACT NUMBER — digits only, max 11 */
    document.getElementById("contact").addEventListener("input", function () {
        const cleaned = this.value.replace(/\D/g, "").slice(0, 11);
        if (cleaned !== this.value) this.value = cleaned;
    });

    /* OCCUPATION — letters only */
    document.getElementById("occupation").addEventListener("input", function () {
        const cleaned = this.value.replace(NAME_ALLOWED, "");
        if (cleaned !== this.value) this.value = cleaned;
    });

    /* Prevent RFID reader's Enter from submitting the form */
    rfidInput.addEventListener("keydown", event => {
        if (event.key === "Enter") {
            event.preventDefault();
            event.stopPropagation();
        }
    });

    /* SIDEBAR */
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

    /* ESCAPE HTML */
    function esc(value) {
        return String(value ?? "").replace(/[&<>"']/g, m => ({
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;"
        }[m]));
    }

    /* RENDER BORROWERS */
    function render() {

        const q = (search.value || "").toLowerCase().trim();

        const filtered = rows.filter(borrower => {
            const searchableText = [
                borrower["full name"],
                borrower["adres"],
                borrower["cont no"],
                borrower["gend"],
                borrower["occup"],
                borrower["dtreg"],
                borrower["Rfid no"]
            ].join(" ").toLowerCase();

            return searchableText.includes(q);
        });

        /* Notification count */
        const notif = document.getElementById("notificationCount");
        if (notif) notif.textContent = filtered.length;

        /* Table body */
        if (filtered.length === 0) {
            table.innerHTML = `
                <tr>
                    <td colspan="8" class="empty-row">No borrowers found.</td>
                </tr>`;
        } else {
            table.innerHTML = filtered.map(borrower => `
                <tr>
                    <td>${esc(borrower["full name"])}</td>
                    <td>${esc(borrower["adres"])}</td>
                    <td>${esc(borrower["cont no"])}</td>
                    <td>${esc(borrower["gend"])}</td>
                    <td>${esc(borrower["occup"])}</td>
                    <td>${esc(borrower["dtreg"])}</td>
                    <td>${esc(borrower["Rfid no"])}</td>
                    <td>
                        <button type="button" class="edit-btn editBtn"
                            data-rfid="${esc(borrower["Rfid no"])}">Edit</button>
                        <button type="button" class="delete-btn deleteBtn"
                            data-rfid="${esc(borrower["Rfid no"])}">Delete</button>
                    </td>
                </tr>
            `).join("");
        }

        /* Entry count */
        document.getElementById("entryInfo").textContent =
            `Showing ${filtered.length} entries`;

        /* Wire up row buttons */
        document.querySelectorAll(".editBtn").forEach(button => {
            button.onclick = () => edit(button.dataset.rfid);
        });

        document.querySelectorAll(".deleteBtn").forEach(button => {
            button.onclick = () => del(button.dataset.rfid);
        });
    }

    /* LOAD BORROWERS */
    function load() {

        table.innerHTML = `
            <tr><td colspan="8" class="loading-row">Loading borrowers...</td></tr>`;

        fetch("api.php?action=borrowers")
            .then(response => {
                if (!response.ok) throw new Error("Server error.");
                return response.json();
            })
            .then(data => {

                console.log("BORROWERS API RESPONSE:", data);

                if (!data.success) {
                    console.error(data.message);
                    rows = [];
                    table.innerHTML = `
                        <tr><td colspan="8" class="empty-row">
                            Unable to load borrowers.
                        </td></tr>`;
                    return;
                }

                rows = Array.isArray(data.borrowers) ? data.borrowers : [];

                console.log("BORROWERS FROM DATABASE:", rows);

                render();

            })
            .catch(error => {
                console.error("Error loading borrowers:", error);
                table.innerHTML = `
                    <tr><td colspan="8" class="empty-row">
                        Unable to connect to the database.
                    </td></tr>`;
            });
    }

    /* EDIT BORROWER */
    function edit(rfid) {

        const borrower = rows.find(
            item => String(item["Rfid no"]) === String(rfid)
        );

        if (!borrower) {
            alert("Borrower record not found.");
            return;
        }

        document.getElementById("fullName").value       = borrower["full name"] || "";
        document.getElementById("address").value        = borrower["adres"] || "";
        document.getElementById("contact").value        = borrower["cont no"] || "";
        document.getElementById("occupation").value     = borrower["occup"] || "";
        document.getElementById("dateRegistered").value = borrower["dtreg"] || "";
        document.getElementById("rfid").value           = borrower["Rfid no"] || "";

        /* DB stores M/F; select uses M/F so direct mapping works */
        const gender = String(borrower["gend"] || "").toUpperCase();
        document.getElementById("gender").value =
            (gender === "M" || gender === "F") ? gender : "";

        /* Remember which borrower is being edited */
        form.dataset.edit = borrower["Rfid no"];
        save.textContent = "Update Borrower";

        document.getElementById("registrationCard").classList.add("edit-mode");
        document.getElementById("registrationCard").scrollIntoView({
            behavior: "smooth",
            block: "start"
        });
    }

    /* DELETE BORROWER */
    function del(rfid) {

        if (!confirm("Delete this borrower?")) return;

        fetch("api.php?action=delete_borrower", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ rfid: rfid })
        })
        .then(response => {
            if (!response.ok) throw new Error("Server error.");
            return response.json();
        })
        .then(data => {

            console.log("DELETE RESPONSE:", data);

            if (!data.success) {
                alert(data.message || "Unable to delete borrower.");
                return;
            }

            alert("Borrower deleted successfully.");
            load();

        })
        .catch(error => {
            console.error("Delete error:", error);
            alert("Unable to delete borrower.");
        });
    }

    /* SAVE / UPDATE BORROWER */
    form.addEventListener("submit", event => {

        event.preventDefault();

        const formData = new FormData(form);

        /* If editing, send the original RFID */
        formData.append("old_rfid", form.dataset.edit || "");

        fetch("api.php?action=save_borrower", {
            method: "POST",
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error("Server error.");
            return response.json();
        })
        .then(data => {

            console.log("SAVE BORROWER RESPONSE:", data);

            if (!data.success) {
                alert(data.message || "Unable to save borrower.");
                return;
            }

            alert(form.dataset.edit
                ? "Borrower updated successfully!"
                : "Borrower saved successfully!");

            form.reset();
            delete form.dataset.edit;
            save.textContent = "Save Borrower";

            document.getElementById("registrationCard")
                .classList.remove("edit-mode");

            /* Re-fill today's date after the form reset */
            setToday();

            load();

        })
        .catch(error => {
            console.error("Save error:", error);
            alert("Unable to connect to the database.");
        });
    });

    /* CLEAR */
    clear.onclick = () => {
        form.reset();
        delete form.dataset.edit;
        save.textContent = "Save Borrower";
        document.getElementById("registrationCard")
            .classList.remove("edit-mode");

        /* Re-fill today's date after the form reset */
        setToday();
    };

    /* SEARCH */
    search.oninput = render;

    /* INITIAL LOAD */
    setToday();
    load();

});

</script>
</body>
</html>