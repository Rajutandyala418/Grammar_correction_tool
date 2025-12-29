<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/include/db_connect.php";

if (!isset($_GET['username']) || empty($_GET['username'])) {
    die("No username found.");
}

$username = $_GET['username'];
$errorPopup = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $errorPopup = "❌ Password and Confirm Password do not match.";
    } 
    elseif (!preg_match("/(?=.*[0-9])/", $password)) {
        $errorPopup = "❌ Password must contain at least one digit.";
    }
    elseif (!preg_match("/(?=.*[A-Z])/", $password)) {
        $errorPopup = "❌ Password must contain at least one uppercase letter.";
    }
    elseif (!preg_match("/(?=.*[a-z])/", $password)) {
        $errorPopup = "❌ Password must contain at least one lowercase letter.";
    }
    elseif (!preg_match("/(?=.*[\W])/", $password)) {
        $errorPopup = "❌ Password must contain at least one special character.";
    }
    elseif (strlen($password) < 8) {
        $errorPopup = "❌ Password must be at least 8 characters long.";
    }
    else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $hashedPassword, $username);

        if ($stmt->execute()) {
            echo "
            <div class='popup-bg'>
                <div class='popup-box'>
                    <h2>Password Updated Successfully 🎉</h2>
                    <p>Redirecting to login in <span id='countdown'>5</span> seconds...</p>
                    <button onclick=\"window.location.href='login.php'\">OK</button>
                </div>
            </div>

            <script>
                let sec = 5;
                let cd = document.getElementById('countdown');
                const timer = setInterval(()=>{
                    sec--;
                    cd.textContent = sec;
                    if(sec <= 0){
                        clearInterval(timer);
                        window.location.href = 'login.php';
                    }
                },1000);
            </script>

            <style>
                body {background: rgba(0,0,0,0.8);}
                .popup-bg{
                    position:fixed;inset:0;display:flex;
                    justify-content:center;align-items:center;
                    background:rgba(0,0,0,0.7);z-index:9999;
                }
                .popup-box{
                    background:#fff;padding:22px;
                    border-radius:14px;text-align:center;
                    width:90%;max-width:350px;color:#1e3c57;
                    box-shadow:0 4px 18px rgba(0,0,0,0.18);
                    border:1px solid #d7e0ea;
                }
                .popup-box h2{color:#1e3c57;}
                .popup-box button{
                    padding:10px 20px;border:none;
                    border-radius:8px;
                    background:#1e3c57;color:#fff;
                    font-weight:600;cursor:pointer;
                    width:100%;
                }
            </style>";
            exit();
        } else {
            $errorPopup = "❌ Error updating password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Update Password</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

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
}

.back-btn i{
    font-size:16px;
}

.back-btn:hover{
    background:#264a6e;
}

.container h2{
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

input[readonly]{
    background:#f0f4f8;
    font-weight:600;
    color:#1e3c57;
}

input::placeholder{
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

.popup-bg{
    position:fixed;
    inset:0;
    display:flex;
    justify-content:center;
    align-items:center;
    background:rgba(0,0,0,0.55);
    z-index:9999;
    display:none;
}

.popup-box{
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

.popup-box button{
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

.popup-box button:hover{
    background:#264a6e;
}

@media(max-width:480px){
    .container{
        padding:20px;
        border-radius:12px;
        max-width:100%;
    }
    .container h2{
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

<button class="back-btn" onclick="window.location.href='forgot_password.php'">
    <i class="fa fa-arrow-left"></i> Back
</button>

<h2>Update Password</h2>

<form method="POST">

    <div class="form-group">
        <label>Username:</label>
        <input type="text" value="<?php echo htmlspecialchars($username); ?>" readonly>
    </div>

    <div class="form-group">
        <label>New Password:</label>
        <input type="password" name="password" required placeholder="Enter new password">
    </div>

    <div class="form-group">
        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" required placeholder="Re-enter password">
    </div>

    <button type="submit">Update Password</button>

</form>
</div>

<?php if(!empty($errorPopup)): ?>
<div class="popup-bg" style="display:flex;">
    <div class="popup-box">
        <p><?php echo $errorPopup; ?></p>
        <button onclick="document.querySelector('.popup-bg').style.display='none';">OK</button>
    </div>
</div>
<?php endif; ?>

</body>
</html>
