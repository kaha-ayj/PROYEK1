<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lapangin.Aja | Chat Admin</title>
<style>
    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        background: linear-gradient(to bottom right, #eaf2f2, #cfd8dc);
    }

    /* Navbar */
    .navbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #eaf2f2;
        padding: 10px 50px;
        box-shadow: 0 1px 5px rgba(0,0,0,0.1);
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .logo img {
        height: 40px;
    }

    .logo h2 {
        color: #1e2f97;
        font-style: italic;
        margin: 0;
    }

    .menu {
        display: flex;
        align-items: center;
        gap: 30px;
    }

    .menu a {
        text-decoration: none;
        color: #6b7a8f;
        font-weight: 600;
        font-style: italic;
    }

    .menu a.active {
        color: #1e2f97;
        text-decoration: underline;
    }

    .search {
        background: white;
        padding: 5px 10px;
        border-radius: 15px;
        border: 1px solid #ccc;
        display: flex;
        align-items: center;
    }

    .search input {
        border: none;
        outline: none;
    }

    .icon {
        background: #fff;
        border-radius: 50%;
        padding: 8px;
        box-shadow: 0 2px 3px rgba(0,0,0,0.1);
    }

    /* Layout utama */
    .container {
        display: flex;
        width: 90%;
        margin: 40px auto;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* Sisi kiri */
    .left-panel {
        width: 35%;
        background: #e8f4f3;
        padding: 30px;
    }

    .left-panel h2 {
        font-weight: 700;
        color: #000;
        margin-bottom: 15px;
    }

    .left-panel h4 {
        color: #333;
        margin-bottom: 10px;
    }

    .admin-box {
        background: #f5fafa;
        margin-top: 10px;
        padding: 15px 20px;
        border-radius: 8px;
        width: 90%;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 3px rgba(0,0,0,0.1);
        cursor: pointer;
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

    /* Chat Area */
    .chat-area {
        width: 65%;
        background: #dbe5e4;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
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

    .msg.admin {
        justify-content: flex-start;
    }

    .msg.user {
        justify-content: flex-end;
    }

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

    .input-area {
        background: #cdd7d6;
        padding: 10px 15px;
        display: flex;
        align-items: center;
        border-top: 1px solid #ccc;
    }

    .input-area input {
        flex: 1;
        border: none;
        background: transparent;
        font-style: italic;
        outline: none;
        padding: 10px;
    }

    .input-area button {
        background: none;
        border: none;
        font-size: 22px;
        cursor: pointer;
        color: #333;
    }

    footer img {
        position: fixed;
        bottom: 15px;
        left: 15px;
        width: 70px;
        opacity: 0.7;
    }
</style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="logo">
        <img src="assets/image/logo.png" alt="Logo">
        <h2></h2>
    </div>
    <div class="menu">
        <a href="jadwal_lapangan1.php">Lapangan</a>
        <a href="homepage.php">Home</a>
        <a href="messege1.php" class="active">Message</a>
        <div class="search">
            <input type="text" placeholder="Cari lapangan">
            🔍
        </div>
        <div class="icon">👤</div>
    </div>
</div>

<!-- Chat Container -->
<div class="container">
    <div class="left-panel">
        <h2>Chat dengan Admin</h2>
        <h4>ADMIN</h4>
        <div class="admin-box">
            <img src="assets/image/image 49.png" alt="Admin">
            <div class="info">
                <div class="name">Reinayla</div>
                <div class="message">Ada kesulitan? chat dengan admin</div>
            </div>
        </div>
    </div>

    <div class="chat-area">
        <div class="chat-header">
            <img src="assets/image/image 49.png" alt="Admin">
            <div class="name">Reinayla</div>
        </div>

        <div class="messages" id="chatBox">
            <!-- USER dulu -->
            <div class="msg user">
                <div class="bubble">Hai, admin!</div>
                <img src="assets/image/image 50.png" alt="User">
            </div>

            <!-- ADMIN balasan -->
            <div class="msg admin">
                <img src="assets/image/image 49.png" alt="Admin">
                <div class="bubble"> Halo, selamat datang di Lapangan SmashPoint!<br>
                Saya AdminBot yang siap membantu kamu 😊<br><br>
                Ada yang bisa saya bantu hari ini?<br>
                1. Cek jadwal lapangan<br>
                2. Pesan lapangan<br>
                3. Lihat tarif dan fasilitas<br>
                4. Hubungi admin</div>
            </div>
        </div>

        <div class="input-area">
            <input type="text" id="messageInput" placeholder="Ketik pesan">
            <button onclick="sendMessage()">▶️</button>
        </div>
    </div>
</div>

<footer>
    <img src="assets/image/image 4.png" alt="Shuttlecock">
</footer>

<script>
function sendMessage() {
    const input = document.getElementById("messageInput");
    const text = input.value.trim();
    if (text === "") return;

    const chatBox = document.getElementById("chatBox");

    const userMsg = document.createElement("div");
    userMsg.className = "msg user";
    userMsg.innerHTML = `<div class="bubble">${text}</div><img src="user.jpg" alt="User">`;
    chatBox.appendChild(userMsg);

    input.value = "";
    chatBox.scrollTop = chatBox.scrollHeight;
}
</script>

</body>
</html>
