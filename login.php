<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include(__DIR__ . '/include/db_connect.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, first_name, last_name, password FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $first_name, $last_name, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $token = bin2hex(random_bytes(32));
            $hashedToken = hash('sha256', $token);

            $upd = $conn->prepare("UPDATE users SET auth_token=? WHERE id=?");
            $upd->bind_param("si", $hashedToken, $id);
            $upd->execute();

            setcookie("remember_token", $token, time() + (86400 * 30), "/", "", false, true);

            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;

            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Invalid username or password.";
        }
    } else {
        $message = "Invalid username or password.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login – Mini Chat App</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:#e8f0f7;display:flex;justify-content:center;align-items:center;min-height:100vh;padding:15px;}
.container{background:#ffffff;border:1px solid #d7e0ea;padding:25px;border-radius:14px;width:100%;max-width:380px;box-shadow:0 4px 15px rgba(0,0,0,0.06);position:relative;}
.container h2{text-align:center;margin-top:40px;margin-bottom:20px;font-weight:700;color:#1e3c57;}
.input-group{margin-bottom:15px;}
.input-group label{font-weight:500;font-size:14px;color:#1e3c57;}
.input-group input{width:100%;padding:11px;border-radius:8px;border:1px solid #b9c7d8;margin-top:6px;outline:none;font-size:15px;}
.input-group input:focus{border-color:#0072ff;}
.input-group input::placeholder{color:#9bb1c7;font-size:14px;}
.login-btn{background:#1e3c57;color:#fff;width:100%;padding:12px;border:none;border-radius:8px;cursor:pointer;margin-top:8px;font-weight:600;font-size:16px;}
.login-btn:hover{background:#264a6e;}
.back-btn{position:absolute;top:15px;right:15px;background:#1e3c57;color:#fff;border:none;font-size:16px;padding:8px 16px;border-radius:8px;cursor:pointer;display:flex;align-items:center;gap:6px;}
.back-btn i{font-size:17px;}
.back-btn:hover{background:#264a6e;}
.popup{display:none;position:fixed;top:40%;left:50%;transform:translate(-50%,-50%);background:#ffffff;padding:22px 30px;color:#1e3c57;border-radius:14px;text-align:center;border:1px solid #d7e0ea;box-shadow:0 4px 18px rgba(0,0,0,0.12);}
.popup button{margin-top:10px;padding:8px 20px;border-radius:8px;border:none;background:#1e3c57;color:white;cursor:pointer;font-size:14px;}
.popup button:hover{background:#264a6e;}
.opt-btn{background:#ffffff;color:#1e3c57;border:1px solid:#0072ff;padding:10px 12px;width:48%;border-radius:8px;font-size:13px;cursor:pointer;transition:0.3s;}
.opt-btn:hover{background:#e8eef5;}
.options{display:flex;justify-content:space-between;margin-top:15px;}
.loader{display:none;border:5px solid #e6e6e6;border-radius:50%;border-top:5px solid #1e3c57;width:38px;height:38px;animation:spin 1s linear infinite;margin:auto;margin-top:15px;}
@keyframes spin{100%{transform:rotate(360deg);}}
.message{color:#d80000;font-size:0.9rem;margin-bottom:10px;text-align:center;}
@media(max-width:480px){
    .container{padding:20px;border-radius:12px;}
    .login-btn{font-size:15px;padding:11px;}
    .opt-btn{font-size:12px;padding:9px;}
    .back-btn{padding:8px 15px;font-size:14px;}
}
</style>
</head>
<body>

<div class="container">
    <button type="button" onclick="window.location.href='index.php'" class="back-btn">
        <i class="fa fa-arrow-left"></i> Back
    </button>

    <h2>Login</h2>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="post" autocomplete="off">
        <input type="text" style="display:none">
        <input type="password" style="display:none">

        <div class="input-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="Enter username" autocomplete="new-username" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" autocomplete="new-password" required>
        </div>

        <button type="submit" class="login-btn">Login</button>

        <div class="options">
            <button type="button" onclick="window.location.href='register.php'" class="opt-btn">Register</button>
            <button type="button" onclick="window.location.href='forgot_password.php'" class="opt-btn">Forgot Credentials</button>
        </div>
    </form>
</div>

<div class="popup" id="errorPopup">
    <h4>Invalid username or password</h4>
    <button onclick="closePopup()">OK</button>
</div>

<script>
function closePopup(){
    document.getElementById("errorPopup").style.display = "none";
}
</script>
</body>
</html>
