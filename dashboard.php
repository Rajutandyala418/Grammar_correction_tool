<?php
include("validate_token.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : "User";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

html, body {
    width: 100%; 
    height: 100%;
    overflow-x: hidden;
    background:#e8f0f7;
    color:#000;
}

.top-left-nav {
    position: fixed;
    top: 15px;
    left: 20px;
    z-index: 9999;
}

.top-right-nav {
    position: fixed;
    top: 15px;
    right: 20px;
    display: flex;
    gap: 10px;
    align-items: center;
    z-index: 9999;
}

.history-btn {
    background: #1e3c57;
    padding: 10px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    color: #fff;
}

.history-btn:hover{
    background:#264a6e;
}

.profile-menu { position: relative; display: inline-block; }
.profile-circle {
    width: 45px; height: 45px;
    background: #ffde59;
    border-radius: 50%;
    display:flex; align-items:center; justify-content:center;
    font-weight:bold; font-size:1.2rem;
    cursor: pointer; color:#000;
}

.dropdown-content {
    display: none;
    position: absolute;
    top: 55px; right: 0;
    background: rgba(30,60,87,0.95);
    border-radius: 8px;
    min-width: 170px;
    box-shadow:0 4px 15px rgba(0,0,0,0.2);
}

.dropdown-content a {
    display:block; padding:12px 16px;
    text-decoration:none; color:#fff;
    font-weight:500;
}

.dropdown-content a:hover{
    background:#264a6e;
}

.container {
    width: 95%; 
    max-width: 900px;
    margin: 50px auto 20px;
    background: #ffffff;
    border:1px solid #d7e0ea;
    border-radius:14px;
    box-shadow:0 4px 15px rgba(0,0,0,0.06);
    padding:25px;
}

.container h1 {
    text-align:center;
    color:#1e3c57;
    font-size:2rem;
    font-weight:700;
    margin-bottom:25px;
}

label {
    font-size: 1.1rem;
    font-weight: 600;
    display:block;
    margin-bottom: 8px;
    color:#1e3c57;
}

textarea {
    width:100%;
    padding: 15px;
    font-size: 1rem;
    border-radius: 8px;
    border:1px solid #b9c7d8;
    resize: vertical;
    margin-bottom: 2px;
    outline:none;
    background:#fff;
    color:#000;
}

textarea:focus{
    border-color:#0072ff;
}

.button-row {
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap:15px;
}

.button-row button {
    padding: 14px;
    border:none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight:600;
    cursor:pointer;
    background:#1e3c57;
    color:#fff;
    transition:0.3s;
}

.button-row button:hover{
    background:#264a6e;
}

#posBtn { grid-column: 1 / -1; }

.popup-box-style {
    background: #ffffff;
    border:1px solid #d7e0ea;
    width: 90%;
    max-width: 350px;
    padding: 25px;
    border-radius: 14px;
    color: #1e3c57;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    display:none;
    z-index:9999;
    box-shadow:0 4px 18px rgba(0,0,0,0.18);
    font-weight:600;
}

.popup-close {
    float:right;
    font-size:24px;
    cursor:pointer;
    margin-top:-8px;
    color:#1e3c57;
}

#spinner {
    display:none;
    position:fixed;
    top:50%; left:50%;
    transform:translate(-50%, -50%);
    z-index:10000;
}

.loader {
    width:45px; height:45px;
    border:5px solid #e6e6e6;
    border-top:5px solid #1e3c57;
    border-radius:50%;
    animation: spin 1s linear infinite;
}

@keyframes spin { 100% { transform: rotate(360deg); } }

#languagePopup select {
    width:100%;
    padding:12px;
    border-radius:8px;
    border:1px solid #b9c7d8;
    margin-bottom:15px;
    font-size:1rem;
}

#languagePopup button {
    width:100%;
    padding:12px;
    border-radius:8px;
    background:#1e3c57;
    color:#fff;
    border:none;
    font-weight:600;
    font-size:1rem;
    cursor:pointer;
}

#languagePopup button:hover{
    background:#264a6e;
}

@media(max-width:768px){
    .top-left-nav{left:15px;}
    .top-right-nav{right:15px; font-size:0.9rem;}
    .container {
        margin: 120px 10px 10px;
        width: calc(100% - 20px);
        padding:20px;
        border-radius:12px;
    }
    .container h1{font-size:1.6rem;}
    .button-row {
        grid-template-columns: repeat(2, 1fr);
        gap:12px;
    }
    #posBtn { grid-column: 1 / -1; }
    textarea{padding:12px; font-size:0.95rem;}
    label{font-size:1rem;}
}

@media(max-width:480px){
    .profile-circle{font-size:1rem; width:40px; height:40px;}
    .dropdown-content{right:-10px; min-width:160px;}
    .dropdown-content a{padding:10px 12px; font-size:0.9rem;}
    .container {
        margin: 80px 5px 5px;
        width: calc(100% - 10px);
        padding:15px;
    }
    .container h1{font-size:1.4rem; margin-bottom:20px;}
    .button-row {
        grid-template-columns: repeat(2, 1fr);
        gap:10px;
    }
    .button-row button{padding:12px; font-size:0.9rem;}
    textarea{padding:10px; font-size:0.9rem;}
    label{font-size:0.95rem;}
}
</style>

</head>
<body>

<div class="top-left-nav">
    <a href="history.php" class="history-btn"><i class="fa fa-history"></i> History</a>
</div>

<div class="top-right-nav">
    <span style="font-weight:600;color:#1e3c57;"><?php echo $username; ?></span>
    <div class="profile-menu">
        <div class="profile-circle" id="profileBtn">
            <?php echo strtoupper(substr($username, 0, 1)); ?>
        </div>
        <div class="dropdown-content" id="dropdownMenu">
            <a href="settings.php"><i class="fa fa-cog"></i> Settings</a>
            <a href="user_details.php"><i class="fa fa-user"></i> Profile Details</a>
            <a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a>
        </div>
    </div>
</div>

<div class="container">
    <h1>Grammar Correction Dashboard</h1>

    <label>Input:</label>
    <textarea id="inputText" placeholder="Enter your text here..." rows="3"></textarea>

    <label>Output:</label>
    <textarea id="outputText" placeholder="Output will appear here..." readonly rows="3"></textarea>

    <div class="button-row">
        <button id="submitBtn"><i class="fa fa-check"></i> Submit</button>
        <button id="translateBtn"><i class="fa fa-language"></i> Translate</button>
        <button id="speechBtn"><i class="fa fa-volume-up"></i> Speech Out</button>
        <button id="speechRecBtn"><i class="fa fa-microphone"></i> Speech Input</button>
        <button id="posBtn"><i class="fa fa-tags"></i> POS Tags</button>
    </div>
</div>

<div id="spinner"><div class="loader"></div></div>

<div id="translateContainer" class="popup-box-style">
    <span class="popup-close" onclick="translateContainer.style.display='none'">×</span>
    <h2>Translation</h2>
</div>

<div id="posContainer" class="popup-box-style">
    <span class="popup-close" onclick="posContainer.style.display='none'">×</span>
    <h2>Parts of Speech</h2>
</div>

<div id="languagePopup" class="popup-box-style">
    <span class="popup-close" onclick="languagePopup.style.display='none'">×</span>
    <h2>Select Language</h2>
    <select id="languageSelect">
        <option value="Hindi">Hindi</option>
        <option value="Telugu">Telugu</option>
        <option value="Tamil">Tamil</option>
        <option value="Kannada">Kannada</option>
        <option value="Malayalam">Malayalam</option>
        <option value="Bengali">Bengali</option>
        <option value="Marathi">Marathi</option>
        <option value="Urdu">Urdu</option>
        <option value="Gujarati">Gujarati</option>
        <option value="Punjabi">Punjabi</option>
        <option value="Assamese">Assamese</option>
        <option value="Sanskrit">Sanskrit</option>
    </select>
    <button id="translateConfirm">Confirm</button>
</div>

<script>
const API_BASE = "https://rajutandyala.pythonanywhere.com";


const profileBtn = document.getElementById('profileBtn');
const dropdownMenu = document.getElementById('dropdownMenu');
profileBtn.onclick = () => dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
document.addEventListener("click", e => { if (!profileBtn.contains(e.target)) dropdownMenu.style.display = "none"; });

const submitBtn = document.getElementById('submitBtn');
const translateBtn = document.getElementById('translateBtn');
const speechBtn = document.getElementById('speechBtn');
const speechRecBtn = document.getElementById('speechRecBtn');
const posBtn = document.getElementById('posBtn');
const inputText = document.getElementById('inputText');
const outputText = document.getElementById('outputText');
const translateContainer = document.getElementById('translateContainer');
const posContainer = document.getElementById('posContainer');
const spinner = document.getElementById('spinner');
const languagePopup = document.getElementById('languagePopup');
const languageSelect = document.getElementById('languageSelect');
const translateConfirm = document.getElementById('translateConfirm');

let lastTranslatedText = "";
let lastTranslatedLanguage = "English";
let recognition = null;
let isListening = false;

submitBtn.onclick = async () => {
    const text = inputText.value.trim();
    if (!text) return;
    spinner.style.display = "block";
    outputText.value = "";
    try {
        const response = await fetch(`${API_BASE}/process_text`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({input_text: text})
        });
        const data = await response.json();
        outputText.value = data.corrected_text;
      fetch("save_history.php", {
    method: "POST",
    headers: {
        "Content-Type": "application/x-www-form-urlencoded"
    },
    body: `input_text=${encodeURIComponent(text)}&output_text=${encodeURIComponent(data.corrected_text)}`,
    credentials: "same-origin"
})
.then(res => res.text())
.then(console.log)
.catch(console.error);

    } finally {
        spinner.style.display = "none";
    }
};

posBtn.onclick = async () => {
    const text = inputText.value.trim();
    if (!text) return;
    spinner.style.display = "block";
    posContainer.style.display = "block";
    posContainer.innerHTML = `<span class="popup-close" onclick="posContainer.style.display='none'">×</span><h2>Parts of Speech</h2>`;
    try {
        const response = await fetch(`${API_BASE}/process_text`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({input_text: text})
        });
        const data = await response.json();
        data.pos_tags.forEach(i => posContainer.innerHTML += `<div style="margin:10px 0; padding:8px; background:#f8f9fa; border-radius:6px; border-left:3px solid #1e3c57;">Word: <b>${i[0]}</b> - POS: <b>${i[1]}</b></div>`);
    } finally {
        spinner.style.display = "none";
    }
};

translateBtn.onclick = () => {
    if (!outputText.value.trim()) return;
    languagePopup.style.display = "block";
};

translateConfirm.onclick = async () => {
    const lang = languageSelect.value;
    const text = outputText.value.trim();
    spinner.style.display = "block";
    languagePopup.style.display = "none";
    translateContainer.style.display = "block";
    translateContainer.innerHTML = `<span class='popup-close' onclick="translateContainer.style.display='none'">×</span><h2>${lang} Translation</h2>`;
    try {
        const res = await fetch(`${API_BASE}/translate_text`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({input_text: text, target_language: lang})
        });
        const data = await res.json();
        translateContainer.innerHTML += `<p style="margin-top:20px; padding:15px; background:#f8f9fa; border-radius:8px; white-space:pre-wrap;">${data.translated_text}</p>`;
        lastTranslatedText = data.translated_text;
        lastTranslatedLanguage = lang;
    } finally {
        spinner.style.display = "none";
    }
};

speechBtn.onclick = async () => {
    if (!lastTranslatedText) return alert("Translate first");
    spinner.style.display = "block";
    try {
        const res = await fetch(`${API_BASE}/speech_output`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({input_text: lastTranslatedText, language: lastTranslatedLanguage})
        });
        const data = await res.json();
        if (data.audio_base64) new Audio("data:audio/mpeg;base64," + data.audio_base64).play();
    } finally {
        spinner.style.display = "none";
    }
};

speechRecBtn.onclick = () => {
    if (!('webkitSpeechRecognition' in window)) return alert("Speech recognition not supported");
    if (!isListening) {
        recognition = new webkitSpeechRecognition();
        recognition.lang = "en-US";
        recognition.onresult = e => inputText.value = e.results[0][0].transcript;
        recognition.onend = () => { isListening = false; speechRecBtn.innerHTML = '<i class="fa fa-microphone"></i> Speech Input'; };
        recognition.start();
        isListening = true;
        speechRecBtn.innerHTML = '<i class="fa fa-stop"></i> Stop Listening';
    } else {
        recognition.stop();
        isListening = false;
        speechRecBtn.innerHTML = '<i class="fa fa-microphone"></i> Speech Input';
    }
};
</script>

</body>
</html>
