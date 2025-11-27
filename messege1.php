<?php
session_start();
include "config/koneksi.php";

$user_id = $_SESSION['user_id'] ?? null;

// cek koneksi
if (!$conn) {
    die("Koneksi database tidak tersedia");
}

// ambil chat user
if ($user_id) {
    $result = mysqli_query($conn, "SELECT * FROM chat WHERE user_id = $user_id ORDER BY created_at ASC");
    $chats = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $chats[] = $row;
    }
} else {
    $chats = [];
}
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

.container {
    display: flex;
    gap: 2px;
    max-width: 1400px;
    margin: 0 auto;
    padding-bottom: 10px;
    align-items: flex-start;
}

.left-panel {
    width: 350px;
    background: #E7F2EF;
    border-radius: 15px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    padding: 25px;
    position: sticky;
    top: 100px;
}

.left-panel h2 {
    font-size: 24px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 8px;
}

.left-panel h4 {
    font-size: 12px;
    color: #999;
    letter-spacing: 1px;
    margin-bottom: 15px;
    font-weight: 600;
}

.admin-box {
    background: transparent;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);;
    padding: 25px;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.admin-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);

}



.admin-content {
    display: flex;
    align-items: center;
    gap: 15px;
    position: relative;
    z-index: 1;
}

.admin-box img {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    border: 3px solid #2b8574;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.admin-info .name {
    font-weight: 700;
    font-size: 18px;
    color: #1a1a1a;
    margin-bottom: 5px;
}

.admin-info .status {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #2b8574;
}

.status-dot {
    width: 8px;
    height: 8px;
    background: #4ade80;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.admin-info .message {
    color: #666;
    font-size: 13px;
    margin-top: 5px;
}

.chat-area {
    flex: 1;
    max-width: 800px;
    background: #dbe5e4;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    display: none;
    flex-direction: column;
    height: calc(100vh - 140px);
    overflow: hidden;
    transform: scale(0.95);
    opacity: 0;
    transition: all 0.3s ease;
    padding: 20px;
}

.chat-area.show {
    display: flex;
    transform: scale(1);
    opacity: 1;
}

.chat-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px 0;
    background: transparent;
    border-bottom: 1px solid #ccc;
    margin-bottom: 20px;
}

.chat-header img {
    width: 45px;
    height: 45px;
    border-radius: 50%;
}

.chat-header-info {
    flex: 1;
}

.chat-header .name {
    font-weight: bold;
    font-size: 16px;
}

.chat-header .status-text {
    font-size: 12px;
    color: #666;
}

.back-btn {
    background: rgba(43, 133, 116, 0.2);
    border: none;
    color: #2b8574;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.back-btn:hover {
    background: rgba(43, 133, 116, 0.3);
    transform: scale(1.1);
}

.messages {
    flex: 1;
    overflow-y: auto;
    height: 400px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.messages::-webkit-scrollbar {
    width: 6px;
}

.messages::-webkit-scrollbar-track {
    background: transparent;
}

.messages::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 10px;
}

.msg {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    max-width: 70%;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.msg.user {
    align-self: flex-end;
    align-items: flex-end;
}

.msg .sender-name {
    font-size: 11px;
    color: #718096;
    margin-bottom: 6px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.msg .bubble {
    padding: 12px 18px;
    border-radius: 18px;
    font-size: 14.5px;
    line-height: 1.5;
    word-wrap: break-word;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.msg.user .bubble {
    background: #2b8574;
    color: white;
    border-radius: 18px 18px 4px 18px;
}

.msg.admin .bubble, .msg.bot .bubble {
    background: white;
    color: #2d3748;
    border: 1px solid #e2e8f0;
    border-radius: 18px 18px 18px 4px;
}

.msg .time {
    font-size: 10px;
    color: #a0aec0;
    margin-top: 4px;
}

.input-area {
    background: #cdd7d6;
    padding: 10px 15px;
    display: flex;
    align-items: center;
    border-top: 1px solid #ccc;
    gap: 10px;
    border-radius: 0 0 10px 10px;
    margin-top: 20px;
}

.input-area input {
    flex: 1;
    border: 1px solid #bbb;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 14px;
    outline: none;
    transition: all 0.2s ease;
}

.input-area input:focus {
    border-color: #2b8574;
    box-shadow: 0 0 0 3px rgba(43, 133, 116, 0.1);
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

.input-area button:active {
    transform: translateY(0);
}

footer img {
    position: fixed;
    bottom: 15px;
    left: 15px;
    width: 70px;
    opacity: 0.7;
    transition: opacity 0.3s ease;
}

footer img:hover {
    opacity: 1;
}


@media (max-width: 1024px) {
    .container {
        flex-direction: column;
        padding: 80px 15px 20px;
    }
    
    .left-panel {
        width: 100%;
        position: relative;
        top: 0;
    }
    
    .chat-area {
        width: 100%;
        max-width: 100%;
        height: calc(100vh - 200px);
    }
    
    .msg {
        max-width: 85%;
    }
}

@media (max-width: 768px) {
    .left-panel {
        padding: 20px;
    }
    
    .chat-header {
        padding: 15px 20px;
    }
    
    .messages {
        padding: 20px 15px;
    }
    
    .input-area {
        padding: 15px;
    }
    
    .msg {
        max-width: 90%;
    }
}
</style>
</head>
<body>
<header class="header">
<?php include 'includes/nav.php'; ?>
</header>

<div class="container" id="chatContainer">
    <div class="left-panel" id="adminList">
        <h2>Chat dengan Admin</h2>
        <h4>ADMIN</h4>
        <div class="admin-box" id="openChat">
            <div class="admin-content">
                <img src="assets/image/image 49.png" alt="Admin">
                <div class="admin-info">
                    <div class="name"><?= isset($_SESSION['admin_nama']) ? htmlspecialchars($_SESSION['admin_nama']) : 'Admin' ?></div>
                    <div class="status">
                        <span class="status-dot"></span>
                        <span>Online</span>
                    </div>
                    <div class="message">Siap membantu Anda</div>
                </div>
            </div>
        </div>
    </div>

    <div class="chat-area" id="chatArea">
        <div class="chat-header">
            <button class="back-btn" id="backBtn">←</button>
            <img src="assets/image/image 49.png" alt="Admin">
            <div class="chat-header-info">
                <div class="name"><?= isset($_SESSION['admin_nama']) ? htmlspecialchars($_SESSION['admin_nama']) : 'Admin' ?></div>
                <div class="status-text">Online - Siap membantu</div>
            </div>
        </div>

        <div class="messages" id="chatBox">
            <?php foreach ($chats as $chat): 
                $senderClass = match($chat['sender']) {
                    'user' => 'user',
                    'bot' => 'bot',
                    'admin' => 'admin',
                    default => 'user'
                };
                $senderName = match($chat['sender']) {
                    'user' => 'Anda',
                    'bot' => 'Bot',
                    'admin' => 'Admin',
                    default => 'User'
                };
            ?>
            <div class="msg <?= $senderClass ?>">
                <div class="sender-name"><?= $senderName ?></div>
                <div class="bubble"><?= htmlspecialchars($chat['message']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="input-area">
            <input type="text" id="messageInput" placeholder="Ketik pesan Anda...">
            <button id="sendBtn">Kirim</button>
        </div>
    </div>
</div>

<footer>
    <img src="assets/image/image 4.png" alt="Shuttlecock">
</footer>

<script>
const chatBox = document.getElementById('chatBox');
const input = document.getElementById('messageInput');
const sendBtn = document.getElementById('sendBtn');
const openChatBtn = document.getElementById('openChat');
const chatArea = document.getElementById('chatArea');
const backBtn = document.getElementById('backBtn');

// Toggle chat area
openChatBtn.addEventListener('click', () => {
    chatArea.classList.add('show');
    setTimeout(() => input.focus(), 300);
});

backBtn.addEventListener('click', () => {
    chatArea.classList.remove('show');
});

// Fungsi untuk mendapatkan waktu saat ini
function getCurrentTime() {
    const now = new Date();
    return now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}

// Append message dengan animasi
function appendMessage(sender, text) {
    if (!text) return;
    const senderName = sender === 'user' ? 'Anda' : sender === 'bot' ? 'Bot' : 'Admin';
    const div = document.createElement('div');
    div.className = `msg ${sender}`;
    div.innerHTML = `
        <div class="sender-name">${senderName}</div>
        <div class="bubble">${text}</div>
        <div class="time">${getCurrentTime()}</div>
    `;
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
}

// Kirim pesan
async function sendMessage() {
    const text = input.value.trim();
    if (!text) return;

    appendMessage('user', text);
    input.value = '';

    // Tampilkan typing indicator
    const typingDiv = document.createElement('div');
    typingDiv.className = 'msg bot';
    typingDiv.id = 'typing-indicator';
    typingDiv.innerHTML = `
        <div class="sender-name">Bot</div>
        <div class="bubble">Mengetik...</div>
    `;
    chatBox.appendChild(typingDiv);
    chatBox.scrollTop = chatBox.scrollHeight;

    try {
        const res = await fetch('message.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({message: text})
        });
        const data = await res.json();
        
        // Hapus typing indicator
        const typing = document.getElementById('typing-indicator');
        if (typing) typing.remove();
        
        if (data.reply) appendMessage('bot', data.reply);
    } catch (err) {
        console.error('Fetch error:', err);
        const typing = document.getElementById('typing-indicator');
        if (typing) typing.remove();
        appendMessage('bot', 'Maaf, terjadi kesalahan. Silakan coba lagi.');
    }
}

sendBtn.onclick = sendMessage;
input.addEventListener('keypress', e => { 
    if (e.key === 'Enter') sendMessage(); 
});

window.onload = () => {
    chatBox.scrollTop = chatBox.scrollHeight;
};
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>