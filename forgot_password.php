<?php
include(__DIR__ . '/include/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'username') {
        $phone = trim($_POST['phone']);
        $emailInput = trim($_POST['email']);

        if ($phone === "" && $emailInput === "") {
            echo json_encode(["status" => "error", "message" => "Please enter Phone OR Email"]);
            exit;
        }

        if ($phone !== "") {
            $stmt = $conn->prepare("SELECT username, email FROM users WHERE phone = ?");
            $stmt->bind_param("s", $phone);
        } else {
            $stmt = $conn->prepare("SELECT username, email FROM users WHERE email = ?");
            $stmt->bind_param("s", $emailInput);
        }

        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($username, $email);
            $stmt->fetch();
            echo json_encode([
                "status" => "success",
                "username" => $username,
                "email" => $email
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "No account found"]);
        }
        exit;
    }

    elseif ($action === 'password') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);

        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND email = ? AND phone = ?");
        $stmt->bind_param("sss", $username, $email, $phone);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo json_encode([
                "status" => "redirect",
                "url" => "update_password.php?username=" . urlencode($username)
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "No matching account found"]);
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Username / Password</title>

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

.forgot-box{
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

.input-group{
    margin-bottom:14px;
}

.input-group label{
    margin-bottom:6px;
    font-weight:500;
    color:#1e3c57;
    font-size:0.95rem;
    display:block;
}

select, input{
    width:100%;
    padding:11px;
    border-radius:8px;
    border:1px solid #b9c7d8;
    font-size:0.95rem;
    outline:none;
    background:#fff;
    color:#000;
}

select:focus, input:focus{
    border-color:#0072ff;
}

select::placeholder, input::placeholder{
    color:#9bb1c7;
    font-size:0.9rem;
}

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
    margin-top:8px;
}

button[type="submit"]:hover{
    background:#264a6e;
}

.form-section { display: none; }
.active { display: block; }

.popup{
    position:fixed;
    inset:0;
    display:flex;
    justify-content:center;
    align-items:center;
    background:rgba(0,0,0,0.55);
    z-index:9999;
    display:none;
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
    border:1px solid #d7e0ea;
}

.popup button{
    margin-top:15px;
    padding:9px 20px;
    background:#1e3c57;
    border-radius:8px;
    border:none;
    color:#fff;
    cursor:pointer;
    font-size:0.95rem;
    font-weight:600;
    width:100%;
}

.popup button:hover{
    background:#264a6e;
}

@media(max-width:480px){
    .forgot-box{
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

<div class="forgot-box">

<button class="back-btn" onclick="window.location.href='login.php'">
    <i class="fa fa-arrow-left"></i> Back
</button>

<h2>Forgot Username / Password</h2>

<select id="forgotType" class="input-group">
    <option value="username">Forgot Username</option>
    <option value="password">Forgot Password</option>
</select>

<form id="formUsername" class="form-section active">
    <div class="input-group">
        <label>Phone Number (optional)</label>
        <input type="text" name="phone" placeholder="Enter Phone Number">
    </div>
    <div class="input-group">
        <label>Email (optional)</label>
        <input type="email" name="email" placeholder="Enter Email">
    </div>
    <input type="hidden" name="action" value="username">
    <button type="submit">Find Username</button>
</form>

<form id="formPassword" class="form-section">
    <div class="input-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter Username" required>
    </div>
    <div class="input-group">
        <label>Registered Email</label>
        <input type="email" name="email" placeholder="Enter Registered Email" required>
    </div>
    <div class="input-group">
        <label>Phone Number</label>
        <input type="text" name="phone" placeholder="Enter Phone Number" required>
    </div>
    <input type="hidden" name="action" value="password">
    <button type="submit">Reset Password</button>
</form>

</div>

<div class="popup" id="popupBox">
    <div class="popup-content">
        <p id="popupText"></p>
        <button onclick="closePopup()">OK</button>
    </div>
</div>

<script>
const selectBox = document.getElementById('forgotType');
const formUsername = document.getElementById('formUsername');
const formPassword = document.getElementById('formPassword');
const popup = document.getElementById('popupBox');
const popupText = document.getElementById('popupText');

selectBox.addEventListener('change', () => {
    if (selectBox.value === "username") {
        formUsername.classList.add("active");
        formPassword.classList.remove("active");
    } else {
        formPassword.classList.add("active");
        formUsername.classList.remove("active");
    }
});

function showPopup(message) {
    popupText.innerHTML = message;
    popup.style.display = "flex";
}

function closePopup() {
    popup.style.display = "none";
}

function submitForm(form) {
    const data = new FormData(form);
    fetch("", {
        method: "POST",
        body: data
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === "success") {
            showPopup("Username: <strong>" + res.username + "</strong><br>Email: <strong>" + res.email + "</strong>");
        }
        else if (res.status === "error") {
            showPopup(res.message);
        }
        else if (res.status === "redirect") {
            window.location.href = res.url;
        }
    });
}

formUsername.addEventListener("submit", e => {
    e.preventDefault();
    submitForm(formUsername);
});

formPassword.addEventListener("submit", e => {
    e.preventDefault();
    submitForm(formPassword);
});
</script>

</body>
</html>

