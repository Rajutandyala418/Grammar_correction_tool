<?php
include("validate_token.php");
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/include/db_connect.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$popupMessage = "";
$showPopup = false;
$inlineError = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['live_check'])) {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');

    $check_stmt = $conn->prepare("SELECT id FROM users WHERE (email = ? OR phone = ?) AND username != ?");
    $check_stmt->bind_param("sss", $email, $phone, $username);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $inlineError = "❌ Email or Phone number already exists!";
        $showPopup = true;
    } else {
        $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssss", $first_name, $last_name, $email, $phone, $username);
            if ($stmt->execute()) {
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name']  = $last_name;
                $_SESSION['email']      = $email;
                $_SESSION['phone']      = $phone;
                $popupMessage = "Profile updated successfully!";
                $showPopup = true;
            } else {
                $popupMessage = "Update failed.";
                $showPopup = true;
            }
            $stmt->close();
        } else {
            $popupMessage = "Database error.";
            $showPopup = true;
        }
    }
    $check_stmt->close();
}

if (isset($_POST['live_check'])) {
    $value = trim($_POST['value']);
    $type = trim($_POST['type']);
    $current_username = $_SESSION['username'];

    if ($type === 'email') {
        $sql = "SELECT id FROM users WHERE email = ? AND username != ?";
    } else {
        $sql = "SELECT id FROM users WHERE phone = ? AND username != ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $value, $current_username);
    $stmt->execute();
    echo ($stmt->get_result()->num_rows > 0) ? "taken" : "available";
    exit;
}

$sql = "SELECT username, first_name, last_name, email, phone FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) die("User not found.");

$username   = htmlspecialchars($user['username']);
$first_name = htmlspecialchars($user['first_name']);
$last_name  = htmlspecialchars($user['last_name']);
$email      = htmlspecialchars($user['email']);
$phone      = htmlspecialchars($user['phone']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Profile</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#e8f0f7;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:15px;
}

.container{
    background:#ffffff;
    border:1px solid #d7e0ea;
    padding:25px;
    border-radius:14px;
    width:100%;
    max-width:420px;
    box-shadow:0 4px 15px rgba(0,0,0,0.06);
    position:relative;
}

.back-btn{
    position:absolute;
    top:15px;
    right:15px;
    background:#1e3c57;
    color:#fff;
    border:none;
    font-size:14px;
    padding:8px 16px;
    border-radius:8px;
    cursor:pointer;
    display:flex;
    align-items:center;
    gap:6px;
    font-weight:600;
    text-decoration:none;
}

.back-btn i{
    font-size:16px;
}

.back-btn:hover{
    background:#264a6e;
}

.container h1{
    text-align:center;
    margin-top:40px;
    margin-bottom:25px;
    font-size:1.7rem;
    color:#1e3c57;
    font-weight:700;
}

.inline-error{
    background:rgba(255,0,0,0.08);
    padding:10px;
    text-align:center;
    border-radius:8px;
    margin-bottom:15px;
    color:#c40000;
    font-weight:600;
    font-size:0.9rem;
}

.form-group{
    margin-bottom:18px;
    display:flex;
    flex-direction:column;
}

.form-group label{
    font-weight:500;
    color:#1e3c57;
    font-size:0.95rem;
    margin-bottom:6px;
}

input[type="text"],
input[type="email"]{
    padding:11px;
    border-radius:8px;
    border:1px solid #b9c7d8;
    font-size:0.95rem;
    outline:none;
    background:#fff;
    color:#000;
    width:100%;
}

input:focus{
    border-color:#0072ff;
}

input[readonly]{
    background:#f0f4f8;
    font-weight:600;
    color:#1e3c57;
}

input::placeholder{
    color:#9bb1c7;
    font-size:0.9rem;
}

.live-msg{
    font-size:0.8rem;
    margin-top:4px;
}

.taken{color:#ff6b6b;}
.available{color:#32cd32;}

button[type="submit"]{
    width:100%;
    padding:12px;
    border:none;
    border-radius:8px;
    font-size:1rem;
    background:#1e3c57;
    color:#fff;
    font-weight:600;
    cursor:pointer;
    margin-top:15px;
}

button[type="submit"]:hover{
    background:#264a6e;
}

.popup-bg {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.popup-box {
    background: #ffffff;
    border:1px solid #d7e0ea;
    width: 90%;
    max-width: 350px;
    padding: 22px 26px;
    border-radius: 14px;
    text-align: center;
    color: #1e3c57;
    font-weight: 600;
    box-shadow: 0 4px 18px rgba(0,0,0,0.18);
}

.popup-box button {
    margin-top: 15px;
    padding: 9px 20px;
    border-radius: 8px;
    border: none;
    background: #1e3c57;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    font-size: 0.95rem;
    width: 100%;
}

.popup-box button:hover {
    background: #264a6e;
}

@media(max-width:480px){
    .container{
        padding:20px;
        border-radius:12px;
        max-width:100%;
    }
    .container h1{
        font-size:1.5rem;
    }
    button[type="submit"]{
        font-size:0.95rem;
        padding:11px;
    }
    .back-btn{
        padding:7px 14px;
        font-size:13px;
    }
}
</style>
</head>

<body>

<div class="container">

<button class="back-btn" onclick="window.location.href='dashboard.php'">
    <i class="fa fa-arrow-left"></i> Back
</button>

<h1>Your Profile Details</h1>

<?php if (!empty($inlineError)): ?>
<div class="inline-error"><?= $inlineError ?></div>
<?php endif; ?>

<form method="post" id="profileForm">

    <div class="form-group">
        <label>Username</label>
        <input type="text" value="<?= $username ?>" readonly>
    </div>

    <div class="form-group">
        <label>First Name</label>
        <input type="text" name="first_name" value="<?= $first_name ?>" required>
    </div>

    <div class="form-group">
        <label>Last Name</label>
        <input type="text" name="last_name" value="<?= $last_name ?>" required>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" id="email" value="<?= $email ?>" required>
        <span id="email-msg" class="live-msg"></span>
    </div>

    <div class="form-group">
        <label>Phone</label>
        <input type="text" name="phone" id="phone" value="<?= $phone ?>" required pattern="[0-9]{10}" title="Enter 10-digit phone number">
        <span id="phone-msg" class="live-msg"></span>
    </div>

    <button type="submit">Update Profile</button>

</form>

</div>

<?php if($showPopup): ?>
<div class="popup-bg" id="popupBox">
    <div class="popup-box">
        <p><?= $popupMessage ?></p>
        <button onclick="document.getElementById('popupBox').style.display='none'">OK</button>
    </div>
</div>
<?php endif; ?>

<script>
function liveCheck(value, type, msgBox) {
    if (value === '') {
        msgBox.innerHTML = '';
        msgBox.className = 'live-msg';
        return;
    }

    let formData = new FormData();
    formData.append('live_check', true);
    formData.append('value', value);
    formData.append('type', type);

    fetch('', { method: 'POST', body: formData })
    .then(res => res.text())
    .then(data => {
        if (data === 'taken') {
            msgBox.innerHTML = '❌ Already taken';
            msgBox.className = 'live-msg taken';
        } else {
            msgBox.innerHTML = '✔ Available';
            msgBox.className = 'live-msg available';
        }
    });
}

document.getElementById('email').addEventListener('input', function() {
    liveCheck(this.value, 'email', document.getElementById('email-msg'));
});

document.getElementById('phone').addEventListener('input', function() {
    liveCheck(this.value, 'phone', document.getElementById('phone-msg'));
});
</script>

</body>
</html>
