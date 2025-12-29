<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include(__DIR__ . '/include/db_connect.php');

$popup_message = "";
$show_popup = false;
$redirect_time = 5;
$inline_error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST["live_check"])) {

    $first_name = trim($_POST["first_name"]);
    $last_name  = trim($_POST["last_name"]);
    $email      = trim($_POST["email"]);
    $phone      = trim($_POST["phone"]);
    $username   = trim($_POST["username"]);
    $password   = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($password !== $confirm_password) {
        $inline_error = "❌ Password and Confirm Password do not match.";
    } else {
        $check_stmt = $conn->prepare("SELECT * FROM users WHERE email=? OR phone=? OR username=?");
        $check_stmt->bind_param("sss", $email, $phone, $username);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $inline_error = "❌ Email, Phone or Username already exists!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone, username, password) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $first_name, $last_name, $email, $phone, $username, $hashed_password);

            if ($stmt->execute()) {
                $popup_message = "🎉 Registration Successful! Redirecting to Login in <span id='countdown'>$redirect_time</span> seconds...";
                $show_popup = true;
            } else {
                $inline_error = "❌ Error: " . $stmt->error;
            }
        }
        $check_stmt->close();
    }
}

if (isset($_POST["live_check"])) {
    $value = trim($_POST["value"]);
    $type  = trim($_POST["type"]);

    if ($type === "email") $sql = "SELECT * FROM users WHERE email=?";
    elseif ($type === "phone") $sql = "SELECT * FROM users WHERE phone=?";
    else $sql = "SELECT * FROM users WHERE username=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $value);
    $stmt->execute();
    echo ($stmt->get_result()->num_rows > 0) ? "taken" : "available";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register</title>

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

.register-box{
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
}

.back-btn i{
    font-size:16px;
}

.back-btn:hover{
    background:#264a6e;
}

h2{
    text-align:center;
    margin-top:40px;
    margin-bottom:20px;
    font-size:1.7rem;
    color:#1e3c57;
    font-weight:700;
}

.form-group{
    margin-bottom:14px;
    display:flex;
    flex-direction:column;
}

label{
    margin-bottom:6px;
    font-weight:500;
    color:#1e3c57;
    font-size:0.95rem;
}

input{
    padding:11px;
    border-radius:8px;
    border:1px solid #b9c7d8;
    font-size:0.95rem;
    outline:none;
    background:#fff;
    color:#000;
}

input:focus{
    border-color:#0072ff;
}

input::placeholder{
    color:#9bb1c7;
    font-size:0.9rem;
}

button[type="submit"]{
    margin-top:8px;
    width:100%;
    padding:12px;
    border:none;
    border-radius:8px;
    font-size:1rem;
    background:#1e3c57;
    color:#fff;
    font-weight:600;
    cursor:pointer;
}

button[type="submit"]:hover{
    background:#264a6e;
}

.live-msg{
    font-size:0.8rem;
    margin-top:4px;
}

.taken{color:#ff6b6b;}
.available{color:#32cd32;}

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

.popup{
    position:fixed;
    inset:0;
    display:flex;
    justify-content:center;
    align-items:center;
    background:rgba(0,0,0,0.55);
    z-index:9999;
}

.popup-content{
    background:#ffffff;
    padding:22px 26px;
    border-radius:14px;
    text-align:center;
    max-width:360px;
    width:90%;
    color:#1e3c57;
    font-weight:600;
    box-shadow:0 4px 18px rgba(0,0,0,0.18);
}

.popup-btn{
    margin-top:15px;
    padding:9px 20px;
    background:#1e3c57;
    border-radius:8px;
    border:none;
    color:#fff;
    cursor:pointer;
    font-size:0.95rem;
    font-weight:600;
}

.popup-btn:hover{
    background:#264a6e;
}

@media(max-width:480px){
    .register-box{
        padding:20px;
        border-radius:12px;
        max-width:100%;
    }
    h2{
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

<div class="register-box">

<button class="back-btn" onclick="window.location.href='index.php'">
    <i class="fa fa-arrow-left"></i> Back
</button>

<h2>Create Account</h2>

<?php if (!empty($inline_error)): ?>
<div class="inline-error"><?php echo $inline_error; ?></div>
<?php endif; ?>

<form method="POST" id="registerForm">

    <div class="form-group">
        <label>First Name:</label>
        <input type="text" name="first_name" placeholder="Enter first name" required>
    </div>

    <div class="form-group">
        <label>Last Name:</label>
        <input type="text" name="last_name" placeholder="Enter last name" required>
    </div>

    <div class="form-group">
        <label>Email:</label>
        <input type="email" name="email" id="email" placeholder="Enter email" required>
        <span id="email-msg" class="live-msg"></span>
    </div>

    <div class="form-group">
        <label>Phone Number:</label>
        <input type="text" name="phone" id="phone" placeholder="Enter phone number" required>
        <span id="phone-msg" class="live-msg"></span>
    </div>

    <div class="form-group">
        <label>Username:</label>
        <input type="text" name="username" id="username" placeholder="Enter username" required>
        <span id="username-msg" class="live-msg"></span>
    </div>

    <div class="form-group">
        <label>Password:</label>
        <input type="password" name="password" id="password" placeholder="Enter password" required>
    </div>

    <div class="form-group">
        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password" required>
        <span id="password-msg" class="live-msg"></span>
    </div>

    <button type="submit">Register</button>
</form>
</div>

<?php if ($show_popup): ?>
<div class="popup" id="popupBox">
    <div class="popup-content">
        <p><?php echo $popup_message; ?></p>
        <button class="popup-btn" onclick="closePopup()">OK</button>
    </div>
</div>
<?php endif; ?>

<script>
function closePopup() {
    document.getElementById("popupBox").style.display = "none";
}

function liveCheck(value, type, msgBox) {
    let formData = new FormData();
    formData.append("live_check", true);
    formData.append("value", value);
    formData.append("type", type);

    fetch("", { method: "POST", body: formData })
    .then(res => res.text())
    .then(data => {
        if (data === "taken") {
            msgBox.innerHTML = "❌ Already taken";
            msgBox.className = "live-msg taken";
        } else {
            msgBox.innerHTML = "✔ Available";
            msgBox.className = "live-msg available";
        }
    });
}

document.getElementById("email").addEventListener("input", function() {
    liveCheck(this.value, "email", document.getElementById("email-msg"));
});
document.getElementById("phone").addEventListener("input", function() {
    liveCheck(this.value, "phone", document.getElementById("phone-msg"));
});
document.getElementById("username").addEventListener("input", function() {
    liveCheck(this.value, "username", document.getElementById("username-msg"));
});

const passwordInput = document.getElementById("password");
const confirmInput = document.getElementById("confirm_password");
const passwordMsg = document.getElementById("password-msg");

function validatePasswordFields() {
    const password = passwordInput.value;
    const confirm = confirmInput.value;
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@.#$!%*?&])[A-Za-z\d@.#$!%*?&]{8,}$/;

    if (!regex.test(password)) {
        passwordMsg.innerHTML = "Password must be 8+ chars, include upper, lower, number, and special symbol.";
        passwordMsg.className = "live-msg taken";
        return false;
    }

    if (confirm && password !== confirm) {
        passwordMsg.innerHTML = "Password and Confirm Password do not match.";
        passwordMsg.className = "live-msg taken";
        return false;
    }

    if (password && confirm && password === confirm) {
        passwordMsg.innerHTML = "Password looks good.";
        passwordMsg.className = "live-msg available";
        return true;
    }

    passwordMsg.innerHTML = "";
    passwordMsg.className = "live-msg";
    return true;
}

passwordInput.addEventListener("input", validatePasswordFields);
confirmInput.addEventListener("input", validatePasswordFields);

document.getElementById("registerForm").addEventListener("submit", function(e) {
    if (!validatePasswordFields()) {
        e.preventDefault();
    }
});

<?php if ($show_popup && strpos($popup_message, "Successful") !== false): ?>
let sec = <?php echo $redirect_time; ?>;
let cd = document.getElementById("countdown");
let timer = setInterval(() => {
    sec--;
    cd.textContent = sec;
    if(sec <= 0){
        clearInterval(timer);
        window.location.href = "login.php";
    }
}, 1000);
<?php endif; ?>
</script>

</body>
</html>
