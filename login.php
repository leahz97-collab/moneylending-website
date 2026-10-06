<?php
require_once __DIR__ . "/config.php";

if (!empty($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Please enter your username and password.";
    } else {

        $stmt = $pdo->prepare("
            SELECT id, username, password, role
            FROM users
            WHERE username = ?
            LIMIT 1
        ");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || $user["password"] !== $password) {
            $error = "Invalid username or password.";
        } else {
            $_SESSION["user_id"]  = (int)$user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"]     = $user["role"] ?: "staff";

            header("Location: dashboard.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" style="margin:0; padding:0; background:#f4f7f5;">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RFID Money Lending Management System - Log In</title>
</head>

<body style="box-sizing:border-box; margin:0; font-family:'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#1c2b27; max-width:460px; margin-left:auto; margin-right:auto; background:#f4f7f5; position:relative; min-height:100vh; overflow-y:auto; -webkit-font-smoothing:antialiased;">

<div style="box-sizing:border-box; width:100%; min-height:100vh; padding:30px 16px 40px; display:flex; align-items:flex-start; justify-content:center;">

<section style="box-sizing:border-box; width:100%; background:#ffffff; border:1px solid #e6ece9; border-radius:10px; box-shadow:0 1px 3px rgba(16,44,34,.06), 0 1px 2px rgba(16,44,34,.04); padding:28px 20px 24px;">

<!-- Login icon -->
<div style="box-sizing:border-box; width:120px; height:120px; margin:0 auto 20px; border-radius:50%; background:#f4faf7; display:flex; align-items:center; justify-content:center; position:relative;">

<div style="box-sizing:border-box; position:absolute; width:104px; height:104px; border-radius:50%; border:1px solid #e6ece9;"></div>

<svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg" style="width:80px; height:80px;">
<circle cx="47" cy="36" r="14" fill="none" stroke="#146c4f" stroke-width="5"/>
<path d="M20 82 C21 63 32 54 47 54 C58 54 66 58 72 66" fill="none" stroke="#146c4f" stroke-width="5" stroke-linecap="round"/>
<rect x="65" y="64" width="30" height="28" rx="5" fill="none" stroke="#146c4f" stroke-width="5"/>
<path d="M72 64V57 C72 50 77 46 82 46 C88 46 93 50 93 57V64" fill="none" stroke="#146c4f" stroke-width="5" stroke-linecap="round"/>
<circle cx="80" cy="76" r="3" fill="#146c4f"/>
<line x1="80" y1="79" x2="80" y2="85" stroke="#146c4f" stroke-width="3" stroke-linecap="round"/>
</svg>

</div>

<!-- Welcome -->
<div style="text-align:center; margin-bottom:24px;">
<div style="font-size:20px; font-weight:800; color:#0f4a38; margin-bottom:8px;">
Welcome Back!
</div>

<div style="font-size:13px; color:#6b7a76; line-height:1.6;">
Please log in to continue to the<br>
Money Lending Management System
</div>
</div>

<!-- REAL FORM -->
<form method="POST" action="login.php">

<!-- Username -->
<div style="margin-bottom:14px;">

<label style="display:block; font-size:12.5px; font-weight:700; margin-bottom:6px; color:#1c2b27;">
Username
</label>

<div style="box-sizing:border-box; width:100%; display:flex; align-items:center; border:1px solid #e6ece9; border-radius:8px; background:#fbfdfc; overflow:hidden;">

<div style="box-sizing:border-box; width:44px; display:flex; align-items:center; justify-content:center; flex:none;">

<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width:20px; height:20px;" fill="none">
<circle cx="12" cy="7" r="4" stroke="#146c4f" stroke-width="2"/>
<path d="M4 21 C4.8 16.5 7.4 14 12 14 C16.6 14 19.2 16.5 20 21" stroke="#146c4f" stroke-width="2" stroke-linecap="round"/>
</svg>

</div>

<input
type="text"
name="username"
placeholder="Enter your username"
autocomplete="username"
required
style="box-sizing:border-box; flex:1; min-width:0; width:100%; border:0; outline:none; padding:12px 13px 12px 0; font-family:inherit; font-size:16px; color:#1c2b27; background:transparent;"
>

</div>
</div>

<!-- Password -->
<div style="margin-bottom:18px;">

<label style="display:block; font-size:12.5px; font-weight:700; margin-bottom:6px; color:#1c2b27;">
Password
</label>

<div style="box-sizing:border-box; width:100%; display:flex; align-items:center; border:1px solid #e6ece9; border-radius:8px; background:#fbfdfc; overflow:hidden;">

<div style="box-sizing:border-box; width:44px; display:flex; align-items:center; justify-content:center; flex:none;">

<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width:19px; height:19px;" fill="none">
<rect x="5" y="10" width="14" height="11" rx="2" stroke="#146c4f" stroke-width="2"/>
<path d="M8 10V7 C8 4.8 9.8 3 12 3 C14.2 3 16 4.8 16 7V10" stroke="#146c4f" stroke-width="2" stroke-linecap="round"/>
<circle cx="12" cy="15" r="1.5" fill="#146c4f"/>
</svg>

</div>

<input
type="password"
name="password"
placeholder="Enter your password"
autocomplete="current-password"
required
style="box-sizing:border-box; flex:1; min-width:0; width:100%; border:0; outline:none; padding:12px 13px 12px 0; font-family:inherit; font-size:16px; color:#1c2b27; background:transparent;"
>

</div>
</div>

<!-- Remember me -->
<div style="box-sizing:border-box; width:100%; display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">

<label style="display:flex; align-items:center; gap:8px; color:#6b7a76; font-size:12.5px; font-weight:600;">

<input
type="checkbox"
name="remember"
style="width:16px; height:16px; accent-color:#146c4f; margin:0;"
>

Remember me

</label>

<a href="#" style="color:#146c4f; font-size:12.5px; font-weight:700; text-decoration:none;">
Forgot password?
</a>

</div>

<!-- Login button -->
<div style="display:flex; flex-direction:column; gap:10px;">

<button
type="submit"
style="box-sizing:border-box; width:100%; padding:13px 20px; border:none; border-radius:8px; background:#146c4f; color:#fff; font-family:inherit; font-size:14px; font-weight:700; text-decoration:none; display:flex; align-items:center; justify-content:center; box-shadow:0 3px 7px rgba(9,107,67,.16); cursor:pointer;"
>
Log In
</button>

</div>

</form>

<!-- Security message -->
<div style="display:flex; align-items:center; justify-content:center; gap:8px; margin-top:22px; color:#6b7a76; font-size:11.5px; text-align:center;">

<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width:22px; height:22px; flex:none;" fill="none">
<path d="M12 3 L19 6 V11 C19 16 16 19 12 21 C8 19 5 16 5 11V6L12 3Z" stroke="#146c4f" stroke-width="1.8" stroke-linejoin="round"/>
<rect x="9" y="10" width="6" height="5" rx="1" stroke="#146c4f" stroke-width="1.5"/>
<path d="M10 10V8.5 C10 7.4 10.9 6.5 12 6.5 C13.1 6.5 14 7.4 14 8.5V10" stroke="#146c4f" stroke-width="1.5"/>
</svg>

<span>Your data is safe and secure with us.</span>

</div>

</section>

</div>

<?php if ($error): ?>

<div style="position:fixed; top:15px; left:50%; transform:translateX(-50%); background:#fdeaea; color:#c33; padding:10px 16px; border-radius:8px; font-size:13px; z-index:9999;">
<?= htmlspecialchars($error) ?>
</div>

<?php endif; ?>

</body>
</html>

