RFID MONEY LENDING MANAGEMENT SYSTEM - PHP VERSION

This project preserves the original HTML/CSS designs and connects
all pages to money_lending_db.

INSTALL:
1. Extract this folder into C:\xampp\htdocs\
2. The folder should be named money_lending.
3. Make sure Apache and MySQL are running in XAMPP.
4. Make sure the database money_lending_db and the SQL
   tables/views/triggers from rfid_lending_1.sql already exist.
5. Put your original images/logo.jpg inside:
   money_lending\image\logo.jpg
6. Open: http://localhost/money_lending/login.php
7. First-run account: admin / admin123.
   If needed, open reset_password.php once, then delete it.

PAGES:
- login.php      Login screen
- dashboard.php  Dashboard with live stats
- borrowers.php  Borrower registration + list
- loan.php       Loan creation + list
- payment.php    Payment history (view only)
- scanner.php    RFID scanner + inline payment recording
- logout.php     Ends the session

PAYMENT FLOW:
- Open scanner.php
- Scan the borrower's RFID card
- The page shows borrower, balance, amount due this month, etc.
- Enter the payment amount (pre-filled with the monthly due)
- Choose method + date, add optional notes
- Press "Save Payment" — the payment is recorded instantly
- No separate recpay.php page is required anymore.

IMPORTANT:
- The old localStorage/demo logic has been fully replaced with
  PHP/MySQL-backed requests.
- All API calls go through api.php.