<?php
session_start();
include 'config/koneksi.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="assets/home.css">
<link rel="stylesheet" href="assets/nav.css">
<title>Lapangin.Aja | Chat Admin</title>

<style>
/* ====== Layout ====== */


/* ====== PANEL KIRI ====== */
.left-panel {
    width: 100%;
    border-radius: 10px;
    background-color: #E7F2EF;
    padding: 30px;
    margin-top: 50px;
    transition: 0.4s;
}

.left-panel h2 {
    font-weight: 700;
    color: #000000ff;
    margin-bottom: 15px;

}

.left-panel h4 {
    color: #333;
    margin-bottom: 10px;
}

.admin-box {
    background: transparent;
    margin-top: 10px;
    padding: 10px 2px;
    border-radius: 8px;
    width: 40%;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 3px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: transform 0.2s ease;
}

.admin-box:hover {
    transform: scale(1.02);
}

.admin-box img {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    margin-right: 10px;
}

.admin-box .info .name {
    font-weight: bold;
}

.admin-box .info .message {
    color: #555;
    font-style: italic;
    font-size: 13px;
}

/* ====== AREA CHAT ====== */
.chat-area {
    width: 65%;
    background: #dbe5e4;
    display: none;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    transition: 0.4s;
}

.chat-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px 25px;
    background: #cdd7d6;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.chat-header img {
    width: 45px;
    height: 45px;
    border-radius: 50%;
}

.chat-header .name {
    font-weight: bold;
    font-size: 16px;
}

.messages {
    padding: 25px;
    overflow-y: auto;
    height: 400px;
    
}

.msg {
    display: flex;
    align-items: flex-end;
    margin-bottom: 15px;
}

.msg.admin { justify-content: flex-start; }
.msg.user { justify-content: flex-end; }

.msg img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin: 0 10px;
}

.msg .bubble {
    background: #fff;
    padding: 10px 15px;
    border-radius: 10px;
    box-shadow: 0 2px 3px rgba(0,0,0,0.1);
    max-width: 60%;
    font-size: 14px;
}

/* ====== INPUT CHAT ====== */
.input-area {
    background: #cdd7d6;
    padding: 10px 15px;
    display: flex;
    align-items: center;
    border-top: 1px solid #ccc;
    gap: 10px;
}

.input-area input {
    flex: 1;
    border: 1px solid #bbb;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 14px;
    outline: none;
}

.input-area input:focus {
    border-color: #2b8574;
}

.input-area button {
    background: #2b8574;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
}

.input-area button:hover {
    background: #22685c;
    transform: scale(1.03);
}

footer img {
    position: fixed;
    bottom: 15px;
    left: 15px;
    width: 70px;
    opacity: 0.7;
}

/* Saat chat dibuka (HP mode) */
.container.chat-open .left-panel {
    display: none;
}
.container.chat-open .chat-area {
    display: flex;
    width: 100%;
}
</style>
</head>

<body>

    <?php include 'includes/nav.php'; ?>


<div class="container" id="chatContainer">
    <!-- PANEL KIRI -->
    <div class="left-panel" id="adminList">
        <h2>Chat dengan Admin</h2>
        <h4>ADMIN</h4>
        <div class="admin-box" id="openChat">
            <img src="assets/image/image 49.png" alt="Admin">
            <div class="info">
                <div class="name">Reinayla</div>
                <div class="message">Ada kesulitan? chat dengan admin</div>
            </div>
        </div>
    </div>

    <!-- AREA CHAT -->
    <div class="chat-area" id="chatArea">
        <div class="chat-header">
            <img src="assets/image/image 49.png" alt="Admin">
            <div class="name">Reinayla</div>
        </div>

        <div class="messages" id="chatBox">
            <div class="msg user">
                <div class="bubble">Hai, admin!</div>
                <img src="assets/image/image 50.png" alt="User">
            </div>

            <div class="msg admin">
                <img src="assets/image/image 49.png" alt="Admin">
                <div class="bubble">
                    Halo, selamat datang di Lapangan SmashPoint! 😊<br>
                    Ada yang bisa saya bantu?<br><br>
                    1. Cek jadwal lapangan<br>
                    2. Pesan lapangan<br>
                    3. Lihat tarif & fasilitas<br>
                    4. Hubungi admin
                </div>
            </div>
        </div>

        <div class="input-area">
            <input type="text" id="messageInput" placeholder="Tulis pesan...">
            <button id="sendBtn">Kirim</button>
        </div>
    </div>
</div>

<footer>
    <img src="assets/image/image 4.png" alt="Shuttlecock">
</footer>

<script>
// Klik admin => buka chat
document.getElementById("openChat").addEventListener("click", () => {
    document.getElementById("chatContainer").classList.add("chat-open");
});

// Kirim pesan user
document.getElementById("sendBtn").addEventListener("click", sendMessage);
document.getElementById("messageInput").addEventListener("keypress", function(e) {
    if (e.key === "Enter") {
        e.preventDefault();
        sendMessage();
    }
});

function sendMessage() {
    const input = document.getElementById("messageInput");
    const text = input.value.trim();
    if (text === "") return;

    const chatBox = document.getElementById("chatBox");

    // Tambahkan pesan user
    const userMsg = document.createElement("div");
    userMsg.className = "msg user";
    userMsg.innerHTML = `<div class="bubble">${text}</div><img src="assets/image/image 50.png" alt="User">`;
    chatBox.appendChild(userMsg);

    // Scroll ke bawah
    chatBox.scrollTop = chatBox.scrollHeight;

    // Kosongkan input
    input.value = "";
}
</script>
</body>
</html>
