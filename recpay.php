<!DOCTYPE html>
<html lang="en">

<head>
<?php require_once __DIR__."/auth.php"; requireLogin(); ?>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>RFID Money Lending Management System - Record Payment</title>

<style>

* {
    box-sizing:border-box;
}

html,
body {
    margin:0;
    padding:0;
    width:100%;
    min-height:100%;
    background:#f4f7f5;
}

body {
    font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif;
    color:#1c2b27;
    min-height:100dvh;
    overflow-y:auto;
    overflow-x:hidden;
}

#sidebar.open {
    transform:translateX(0) !important;
}

#overlay.open {
    opacity:1 !important;
    visibility:visible !important;
}

input,
select,
textarea,
button {
    font-family:inherit;
}

input:focus,
select:focus,
textarea:focus {
    outline:none;
    border-color:#1a8a63 !important;
    box-shadow:0 0 0 2px rgba(26,138,99,.08);
}

.field {
    width:100%;
    margin-bottom:16px;
}

.field-label {
    display:block;
    font-size:12.5px;
    font-weight:700;
    color:#3c4b47;
    margin-bottom:6px;
}

.input-control {
    width:100%;
    min-height:44px;
    padding:12px 13px;
    border:1px solid #e6ece9;
    border-radius:9px;
    font-size:13.5px;
    color:#1c2b27;
    background:#fff;
}

.rfid-field {
    background:#f4f7f5 !important;
    color:#6b7a76 !important;
}

.auto-field {
    background:#f4f7f5 !important;
    color:#1c2b27 !important;
    font-weight:600;
}

#scannedBorrowerCard {
    display:block;
    text-decoration:none;
    color:#fff;
    cursor:pointer;
    -webkit-tap-highlight-color:transparent;
    transition:transform .1s ease, box-shadow .15s ease;
}

#scannedBorrowerCard:active {
    transform:scale(.99);
}

#scannedBorrowerCard.empty {
    box-shadow:
        0 4px 10px rgba(15,74,56,.18),
        0 0 0 2px rgba(255,255,255,.25) inset;
}

#successModal {
    position:fixed;
    top:0;
    right:0;
    bottom:0;
    left:0;
    width:100%;
    height:100dvh;
    background:rgba(13,59,46,.45);
    z-index:9999;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:16px;
    opacity:0;
    visibility:hidden;
    pointer-events:none;
    transition:
        opacity .2s ease,
        visibility .2s ease;
}

#successModal.open {
    opacity:1;
    visibility:visible;
    pointer-events:auto;
}

.success-card {
    width:100%;
    max-width:360px;
    background:#fff;
    border-radius:14px;
    padding:24px 20px 20px;
    text-align:center;
    box-shadow:
        0 12px 40px rgba(15,74,56,.25);
}

.success-icon {
    width:58px;
    height:58px;
    border-radius:50%;
    background:#e3f3ea;
    color:#146c4f;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:0 auto 13px;
    font-size:28px;
    font-weight:800;
}

.success-title {
    font-size:18px;
    font-weight:800;
    color:#0f4a38;
    margin-bottom:5px;
}

.success-message {
    font-size:12.5px;
    color:#6b7a76;
    line-height:1.5;
    margin-bottom:18px;
}

.success-button {
    width:100%;
    border:none;
    background:#146c4f;
    color:#fff;
    padding:12px;
    border-radius:9px;
    font-size:13.5px;
    font-weight:700;
    cursor:pointer;
}

.success-button:hover {
    background:#0f5b43;
}

@media(max-width:360px) {

    .content {
        padding-left:12px !important;
        padding-right:12px !important;
    }

    .form-card {
        padding:14px !important;
    }

}

</style>

</head>

<body>

<!-- SIDEBAR -->

<aside id="sidebar"
style="
position:fixed;
top:0;
left:0;
bottom:0;
width:min(250px,82vw);
background:linear-gradient(180deg,#0d3b2e 0%,#0f4a38 100%);
transform:translateX(-100%);
transition:transform .25s;
z-index:200;
display:flex;
flex-direction:column;
padding:20px 14px;
">

    <div style="display:flex;justify-content:flex-end;">

        <button id="closeMenu"
        type="button"
        style="
        background:none;
        border:none;
        color:#fff;
        font-size:18px;
        cursor:pointer;
        ">

            ✕

        </button>

    </div>

    <div style="
    display:flex;
    justify-content:center;
    padding:4px 6px 24px;
    ">

        <img src="image/logo.png"
        alt="Logo"
        style="
        width:120px;
        height:120px;
        border-radius:20px;
        object-fit:cover;
        ">

    </div>

    <ul style="
    list-style:none;
    margin:6px 0 0;
    padding:0;
    display:flex;
    flex-direction:column;
    gap:4px;
    flex:1;
    ">

        <li>
            <a href="dashboard.php"
            style="
            display:flex;
            padding:12px 13px;
            color:rgba(255,255,255,.82);
            text-decoration:none;
            font-size:14px;
            ">
                Home
            </a>
        </li>

        <li>
            <a href="borrowers.php"
            style="
            display:flex;
            padding:12px 13px;
            color:rgba(255,255,255,.82);
            text-decoration:none;
            font-size:14px;
            ">
                Borrowers
            </a>
        </li>

        <li>
            <a href="loan.php"
            style="
            display:flex;
            padding:12px 13px;
            color:rgba(255,255,255,.82);
            text-decoration:none;
            font-size:14px;
            ">
                Loans
            </a>
        </li>

        <li>
            <a href="payment.php"
            style="
            display:flex;
            padding:12px 13px;
            border-radius:9px;
            background:#1f9e6f;
            color:#fff;
            text-decoration:none;
            font-size:14px;
            font-weight:700;
            ">
                Payments
            </a>
        </li>

        <li>
            <a href="scanner.php"
            style="
            display:flex;
            padding:12px 13px;
            color:rgba(255,255,255,.82);
            text-decoration:none;
            font-size:14px;
            ">
                RFID Scanner
            </a>
        </li>

        <li>
            <a href="logout.php"
            style="
            display:flex;
            padding:12px 13px;
            color:rgba(255,255,255,.82);
            text-decoration:none;
            font-size:14px;
            ">
                Logout
            </a>
        </li>

    </ul>

</aside>

<!-- OVERLAY -->

<button id="overlay"
type="button"
aria-label="Close menu"
style="
position:fixed;
top:0;
right:0;
bottom:0;
left:0;
width:100%;
height:100%;
border:none;
background:rgba(13,59,46,.45);
z-index:150;
opacity:0;
visibility:hidden;
cursor:pointer;
">
</button>

<!-- TOP BAR -->

<div style="
background:#fff;
border-bottom:1px solid #e6ece9;
display:flex;
align-items:center;
gap:14px;
padding:14px 16px;
height:64px;
">

    <h1 style="
    font-size:17px;
    margin:0;
    font-weight:700;
    flex:1;
    ">

        Record Payment

    </h1>

    <div style="position:relative;font-size:18px;">

        🔔

        <span style="
        position:absolute;
        top:-8px;
        right:-9px;
        width:16px;
        height:16px;
        border-radius:50%;
        background:#e14b4b;
        color:#fff;
        font-size:9px;
        font-weight:700;
        display:flex;
        align-items:center;
        justify-content:center;
        border:2px solid #fff;
        ">

            0

        </span>

    </div>

</div>

<!-- CONTENT -->

<div class="content"
style="
padding:16px 14px 40px;
width:100%;
">

<!-- SCANNED BORROWER CARD -->

<a id="scannedBorrowerCard"
class="empty"
href="scanner.php"
aria-label="Go to RFID scanner"
style="
background:linear-gradient(135deg,#146c4f 0%,#0f4a38 100%);
border-radius:12px;
padding:16px;
margin-bottom:16px;
box-shadow:0 4px 10px rgba(15,74,56,.18);
">

    <div style="
    font-size:11px;
    color:rgba(255,255,255,.7);
    margin-bottom:6px;
    ">

        SCANNED RFID BORROWER

    </div>

    <div style="
    display:flex;
    align-items:center;
    gap:12px;
    ">

        <div id="borrowerInitials"
        style="
        width:48px;
        height:48px;
        border-radius:50%;
        background:rgba(255,255,255,.15);
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:20px;
        font-weight:800;
        flex-shrink:0;
        ">

            💳

        </div>

        <div style="flex:1;min-width:0;">

            <div id="scannedBorrowerName"
            style="
            font-size:15px;
            font-weight:800;
            ">

                Tap here to scan RFID card

            </div>

            <div id="scannedRFID"
            style="
            font-size:11.5px;
            color:rgba(255,255,255,.72);
            margin-top:3px;
            ">

                RFID: --

            </div>

        </div>

        <span style="
        color:rgba(255,255,255,.7);
        font-size:22px;
        flex-shrink:0;
        ">

            ›

        </span>

    </div>

    <div style="
    display:flex;
    justify-content:space-between;
    margin-top:14px;
    padding-top:12px;
    border-top:1px solid rgba(255,255,255,.15);
    ">

        <div>

            <div style="
            font-size:10px;
            color:rgba(255,255,255,.65);
            ">

                Outstanding

            </div>

            <div id="scannedBalance"
            style="
            font-size:14px;
            font-weight:800;
            margin-top:3px;
            ">

                ₱0.00

            </div>

        </div>

        <div style="text-align:right;">

            <div style="
            font-size:10px;
            color:rgba(255,255,255,.65);
            ">

                Next Due

            </div>

            <div id="scannedDue"
            style="
            font-size:12px;
            font-weight:700;
            margin-top:3px;
            ">

                --

            </div>

        </div>

    </div>

    <div id="scanHint"
    style="
    margin-top:12px;
    font-size:11.5px;
    font-weight:700;
    color:rgba(255,255,255,.85);
    text-align:center;
    ">

        Scan an RFID card to fill in the details

    </div>

</a>

<!-- FORM -->

<form id="paymentForm"
class="form-card"
style="
background:#fff;
border-radius:10px;
box-shadow:0 1px 3px rgba(16,44,34,.06);
padding:16px;
">

    <div style="
    font-size:15px;
    font-weight:700;
    color:#0f4a38;
    padding-bottom:14px;
    border-bottom:2px solid #1a8a63;
    margin-bottom:16px;
    ">

        Payment Details

    </div>

    <!-- BORROWER -->

    <div class="field">

        <label class="field-label" for="borrower">

            Borrower

        </label>

        <input id="borrower"
        type="text"
        class="input-control auto-field"
        readonly
        placeholder="Filled after scanning">

    </div>

    <!-- RFID -->

    <div class="field">

        <label class="field-label" for="rfidCard">

            RFID Card No.

        </label>

        <input id="rfidCard"
        type="text"
        class="input-control rfid-field"
        readonly
        placeholder="Filled after scanning">

    </div>

    <!-- OUTSTANDING -->

    <div class="field">

        <label class="field-label" for="outstandingBalance">

            Outstanding Balance

        </label>

        <div style="position:relative;">

            <span style="
            position:absolute;
            left:13px;
            top:50%;
            transform:translateY(-50%);
            color:#6b7a76;
            font-weight:700;
            z-index:2;
            ">

                ₱

            </span>

            <input id="outstandingBalance"
            type="text"
            class="input-control auto-field"
            readonly
            style="padding-left:30px;"
            value="0.00">

        </div>

    </div>

    <!-- AMOUNT -->

    <div class="field">

        <label class="field-label" for="amount">

            Payment Amount

        </label>

        <div style="position:relative;">

            <span style="
            position:absolute;
            left:13px;
            top:50%;
            transform:translateY(-50%);
            color:#6b7a76;
            font-weight:700;
            z-index:2;
            ">

                ₱

            </span>

            <input id="amount"
            type="number"
            min="0.01"
            step="0.01"
            required
            placeholder="0.00"
            class="input-control"
            style="padding-left:30px;">

        </div>

    </div>

    <!-- PAYMENT DATE -->

    <div class="field">

        <label class="field-label" for="paymentDate">

            Payment Date

        </label>

        <input id="paymentDate"
        type="date"
        required
        class="input-control">

    </div>

    <!-- METHOD -->

    <div class="field">

        <label class="field-label" for="paymentMethod">

            Payment Method

        </label>

        <select id="paymentMethod"
        required
        class="input-control">

            <option value="">
                Select method
            </option>

            <option value="cash">
                Cash
            </option>

            <option value="gcash">
                GCash
            </option>

            <option value="bank_transfer">
                Bank Transfer
            </option>

            <option value="check">
                Check
            </option>

        </select>

    </div>

    <!-- NOTES -->

    <div class="field"
    style="margin-bottom:20px;">

        <label class="field-label" for="notes">

            Notes

            <span style="
            color:#9aa8a4;
            font-weight:500;
            ">

                (optional)

            </span>

        </label>

        <textarea id="notes"
        rows="3"
        class="input-control"
        placeholder="Add a note about this payment..."></textarea>

    </div>

    <!-- SAVE -->

    <button type="submit"
    style="
    width:100%;
    background:#146c4f;
    color:#fff;
    border:none;
    padding:13px;
    border-radius:9px;
    font-size:14px;
    font-weight:700;
    cursor:pointer;
    margin-bottom:10px;
    ">

        Save Payment

    </button>

    <!-- CANCEL -->

    <a href="payment.php"
    style="
    display:block;
    text-align:center;
    width:100%;
    background:#fff;
    color:#6b7a76;
    border:1px solid #e6ece9;
    padding:12px;
    border-radius:9px;
    text-decoration:none;
    font-size:14px;
    font-weight:600;
    ">

        Cancel

    </a>

</form>

</div>

<!-- SUCCESS / ERROR MODAL -->

<div id="successModal">

    <div class="success-card">

        <div id="modalIcon"
        class="success-icon">

            ✓

        </div>

        <div id="successTitle"
        class="success-title">

            Payment Successful!

        </div>

        <div id="successMessage"
        class="success-message">

            The payment has been recorded successfully.

        </div>

        <button id="successDone"
        type="button"
        class="success-button">

            Done

        </button>

    </div>

</div>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const sidebar =
        document.getElementById('sidebar');

    const overlay =
        document.getElementById('overlay');

    const close =
        document.getElementById('closeMenu');


    if (document.getElementById('menuButton')) {

        document.getElementById('menuButton').onclick =
            function () {

                sidebar.classList.add('open');

                overlay.classList.add('open');

            };

    }


    function shut() {

        sidebar.classList.remove('open');

        overlay.classList.remove('open');

    }


    close.onclick = shut;

    overlay.onclick = shut;


    /* =====================================================
       FORM
    ===================================================== */

    const form =
        document.getElementById('paymentForm');

    const modal =
        document.getElementById('successModal');

    const scannedCard =
        document.getElementById('scannedBorrowerCard');

    const saveButton =
        form.querySelector('button[type="submit"]');


    /*
     * UNIQUE PAYMENT REQUEST ID
     *
     * This identifies one specific submission.
     * The API uses it to prevent the same request
     * from being processed twice.
     */

    const paymentRequestId =
        Date.now().toString(36) +
        '-' +
        Math.random().toString(36).slice(2);


    let isSubmitting = false;


    /* SET TODAY'S DATE */

    document.getElementById('paymentDate').value =
        new Date().toISOString().slice(0, 10);


    /* =====================================================
       HELPERS
    ===================================================== */

    function money(value) {

        return Number(value || 0).toLocaleString(
            'en-PH',
            {
                minimumFractionDigits:2,
                maximumFractionDigits:2
            }
        );

    }


    function date(value) {

        return value || '--';

    }


    /* =====================================================
       LOAD RFID BORROWER
    ===================================================== */

    function loadRFID(r) {

        fetch(
            'api.php?action=scan&rfid=' +
            encodeURIComponent(r) +
            '&_=' +
            Date.now(),
            {
                cache:'no-store'
            }
        )

        .then(function (response) {

            return response.json();

        })

        .then(function (d) {

            if (!d.success) {

                alert(
                    d.message ||
                    'Unable to load RFID information.'
                );

                return;

            }


            if (!d.authorized) {

                alert(
                    'RFID card is not registered.'
                );

                return;

            }


            const borrower =
                d.data || {};


            /* BORROWER */

            document.getElementById('borrower').value =
                borrower.full_name || '';


            /* RFID */

            document.getElementById('rfidCard').value =
                borrower.rfid_no || r;


            /* OUTSTANDING BALANCE */

            const realBalance =
                Number(
                    borrower.outstanding_balance || 0
                );


            document.getElementById(
                'outstandingBalance'
            ).value =
                money(realBalance);


            document.getElementById('amount').setAttribute(
                'max',
                realBalance.toFixed(2)
            );


            /* SCANNED CARD */

            document.getElementById(
                'scannedBorrowerName'
            ).textContent =
                borrower.full_name || 'Borrower';


            document.getElementById(
                'scannedRFID'
            ).textContent =
                'RFID: ' +
                (borrower.rfid_no || r);


            document.getElementById(
                'scannedBalance'
            ).textContent =
                '₱' +
                money(
                    borrower.outstanding_balance
                );


            document.getElementById(
                'scannedDue'
            ).textContent =
                date(borrower.next_due);


            /* BORROWER INITIALS */

            const name =
                borrower.full_name || '';


            const initials =
                name
                    .split(/\s+/)
                    .filter(function (part) {

                        return part !== '';

                    })
                    .map(function (part) {

                        return part.charAt(0);

                    })
                    .slice(0, 2)
                    .join('')
                    .toUpperCase();


            document.getElementById(
                'borrowerInitials'
            ).textContent =
                initials || '💳';


            /* CARD IS NO LONGER EMPTY */

            scannedCard.classList.remove(
                'empty'
            );


            /* SAVE LOAN ID */

            form.dataset.loanId =
                borrower.loan_id || '';

        })

        .catch(function (error) {

            console.error(error);

            alert(
                'Unable to connect to the server.'
            );

        });

    }


    /* =====================================================
       GET RFID FROM URL
    ===================================================== */

    const r =
        new URLSearchParams(
            location.search
        ).get('rfid');


    if (r) {

        loadRFID(r);

    }


    /* =====================================================
       SAVE PAYMENT
    ===================================================== */

    form.addEventListener(
        'submit',
        function (e) {

            e.preventDefault();


            /* BLOCK DOUBLE SUBMISSION */

            if (isSubmitting) {

                return;

            }


            /* GET VALUES DIRECTLY FROM INPUTS */

            const amount =
                document
                    .getElementById('amount')
                    .value
                    .trim();


            const paymentDate =
                document.getElementById(
                    'paymentDate'
                ).value;


            const paymentMethod =
                document.getElementById(
                    'paymentMethod'
                ).value;


            const notes =
                document.getElementById(
                    'notes'
                ).value
                .trim();


            const loanId =
                form.dataset.loanId || '';


            /* CHECK LOAN */

            if (!loanId) {

                alert(
                    'No loan was found for this borrower. Please scan the RFID card again.'
                );

                return;

            }


            /* CHECK AMOUNT */

            if (
                !amount ||
                Number(amount) <= 0
            ) {

                alert(
                    'Please enter a valid payment amount.'
                );

                return;

            }


            /* CHECK OUTSTANDING BALANCE */

            const outstanding =
                Number(
                    document.getElementById(
                        'outstandingBalance'
                    ).value.replace(/,/g, '')
                );


            if (
                !isNaN(outstanding) &&
                Number(amount) > outstanding
            ) {

                alert(
                    'Payment cannot be greater than the outstanding balance.'
                );

                return;

            }


            /* CHECK DATE */

            if (!paymentDate) {

                alert(
                    'Please select a payment date.'
                );

                return;

            }


            /* CHECK METHOD */

            if (!paymentMethod) {

                alert(
                    'Please select a payment method.'
                );

                return;

            }


            /* =================================================
               CREATE FORM DATA
            ================================================= */

            const fd =
                new FormData();


            fd.append(
                'loan_id',
                loanId
            );


            fd.append(
                'payment_amount',
                amount
            );


            fd.append(
                'payment_date',
                paymentDate
            );


            fd.append(
                'payment_method',
                paymentMethod
            );


            fd.append(
                'notes',
                notes
            );


            /*
             * IMPORTANT:
             * This unique ID prevents the same payment
             * request from being processed twice.
             */

            fd.append(
                'payment_request_id',
                paymentRequestId
            );


            /* =================================================
               LOCK FORM
            ================================================= */

            isSubmitting = true;

            saveButton.disabled = true;

            saveButton.style.opacity = '0.7';

            saveButton.style.cursor = 'not-allowed';

            saveButton.textContent =
                'Saving...';


            /* =================================================
               SEND TO API
            ================================================= */

            fetch(
                'api.php?action=save_payment',
                {
                    method:'POST',
                    body:fd
                }
            )

            .then(function (response) {

                return response.json();

            })

            .then(function (d) {

                if (!d.success) {

                    alert(
                        (d.message ||
                        'Unable to save payment.') +
                        (
                            d.error
                            ? '\n\nDetails: ' +
                              d.error
                            : ''
                        )
                    );


                    /*
                     * Allow retry only when
                     * the server rejected the payment.
                     */

                    isSubmitting = false;

                    saveButton.disabled = false;

                    saveButton.style.opacity = '1';

                    saveButton.style.cursor =
                        'pointer';

                    saveButton.textContent =
                        'Save Payment';

                    return;

                }


                /* =================================================
                   SUCCESS
                ================================================= */

                document.getElementById(
                    'successTitle'
                ).textContent =
                    'Payment Successful!';


                document.getElementById(
                    'successMessage'
                ).textContent =
                    'The payment has been recorded successfully.';


                modal.classList.add('open');


                /*
                 * Return to Payments page.
                 */

                setTimeout(function () {

                    window.location.href =
                        'payment.php';

                }, 1500);

            })

            .catch(function (error) {

                console.error(error);


                alert(
                    'Unable to connect to the database.'
                );


                /*
                 * Allow retry when the request
                 * completely failed.
                 */

                isSubmitting = false;

                saveButton.disabled = false;

                saveButton.style.opacity = '1';

                saveButton.style.cursor =
                    'pointer';

                saveButton.textContent =
                    'Save Payment';

            });

        }
    );


    /* =====================================================
       SUCCESS MODAL
    ===================================================== */

    document.getElementById(
        'successDone'
    ).onclick = function () {

        window.location.href =
            'payment.php';

    };

});

</script>

</body>
</html>