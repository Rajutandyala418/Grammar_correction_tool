<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include(__DIR__ . "/validate_token.php");

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grammar Correction Tool</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap');

        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.6), rgba(0,0,0,0.8)),
                        url('https://www.bharatplus.ai/wp-content/uploads/2024/04/Grammar-Correction.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: 'Poppins', sans-serif;
            color: white;
            text-align: center;
            min-height: 100vh;
        }

        .container {
            padding: 100px 20px 40px;
        }

        .title {
            font-size: 3rem;
            color: #ffde59;
            font-weight: 800;
            text-shadow: 0 0 8px rgba(0,0,0,0.7);
        }

        .subtitle {
            font-size: 1.3rem;
            max-width: 600px;
            margin: 10px auto 30px;
            opacity: 0.95;
        }

        .buttons {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            font-size: 15px;
        }

        .buttons a {
            padding: 14px 35px;
            color: white;
            text-decoration: none;
            background: linear-gradient(90deg, #ff512f, #dd2476);
            border-radius: 8px;
            font-weight: 900;
            transition: transform 0.2s ease-in-out;
        }

        .buttons a:hover {
            transform: scale(1.1);
        }

        footer {
            position: fixed;
            bottom: 10px;
            width: 100%;
            text-align: center;
            color: #ddd;
            font-size: 0.9rem;
        }

        #support-btn {
            position: fixed;
            bottom: 20px;
            left: 20px;
            width: 60px;
            height: 60px;
            background: #007bff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
            z-index: 10;
        }

        #support-btn img {
            width: 30px;
            height: 30px;
        }

        #support-popup {
            display: none;
            position: fixed;
            bottom: 90px;
            left: 20px;
            width: 260px;
            padding: 18px;
            background: #fff;
            color: #000;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            z-index: 11;
        }

        #support-popup h3 {
            margin: 0 0 10px;
            color: #007bff;
        }

        #support-popup button {
            width: 100%;
            margin-top: 10px;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            font-weight: 600;
        }

        .call-btn { background: #007bff; color: white; }
        .email-btn { background: #28a745; color: white; }

        #whatsapp-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: #25d366;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
            z-index: 10;
        }

        #whatsapp-btn img {
            width: 34px;
            height: 34px;
        }

        @media (max-width: 768px) {
            .title { font-size: 2.2rem; }
            .subtitle { font-size: 1.1rem; }
            .buttons a { width: 80%; text-align: center; }

            #support-btn,
            #whatsapp-btn {
                width: 50px;
                height: 50px;
            }

            #support-btn img,
            #whatsapp-btn img {
                width: 26px;
                height: 26px;
            }
        }

        @media (max-width: 480px) {
            .title { font-size: 1.8rem; }
            .subtitle { font-size: 1rem; }

            #support-btn,
            #whatsapp-btn {
                width: 45px;
                height: 45px;
            }

            #support-btn img,
            #whatsapp-btn img {
                width: 22px;
                height: 22px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="title">Grammar Correction Tool</h1>
    <p class="subtitle">Enhance your writing by correcting grammar, spelling, and sentence structure instantly.</p>

    <div class="buttons">
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </div>
</div>

<div id="support-btn">
    <img src="https://cdn-icons-png.flaticon.com/128/1067/1067566.png">
</div>

<div id="support-popup">
    <h3>Customer Support</h3>
    <button class="call-btn" onclick="window.location.href='tel:7569398385'">📞 Call: 7569398385</button>
    <button class="email-btn" onclick="window.location.href='mailto:rajutandyala369@gmail.com'">📧 Email Us</button>
</div>

<div id="whatsapp-btn" onclick="window.location.href='https://wa.me/917569398385'">
    <img src="https://cdn-icons-png.flaticon.com/512/733/733585.png">
</div>

<script>
    const supportBtn = document.getElementById('support-btn');
    const supportPopup = document.getElementById('support-popup');

    supportBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        supportPopup.style.display =
            supportPopup.style.display === 'block' ? 'none' : 'block';
    });

    supportPopup.addEventListener('click', (e) => {
        e.stopPropagation();
    });

    document.addEventListener('click', () => {
        supportPopup.style.display = 'none';
    });
</script>

</body>
</html>
