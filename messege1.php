<?php
session_start();

// Sesuaikan path ini dengan folder kamu
include('config/koneksi.php');

// 1. Ambil User ID secara fleksibel
$user_id = 0; 
if (isset($_SESSION['penggunaID'])) {
    $user_id = $_SESSION['penggunaID'];
} elseif (isset($_SESSION['user']['id'])) {
    $user_id = $_SESSION['user']['id'];
} elseif (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

// HAPUS BARIS INI DI KODE LAMA KAMU: $user_id = 0; <--- Ini yang bikin reset terus

// 2. Jika tetap 0, arahkan ke login (Opsional tapi disarankan)
if ($user_id == 0) {
    // header("Location: login.php"); 
    // exit;
}

// 3. Ambil username secara fleksibel
$username = "User";
if (isset($_SESSION['nama'])) {
    $username = $_SESSION['nama'];
} elseif (isset($_SESSION['user']['nama'])) {
    $username = $_SESSION['user']['nama'];
} elseif (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/home.css">
    <link rel="stylesheet" href="assets/nav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Chat Support - Lapangin.Aja</title>
    <style>
   
        
        /* MAIN CONTAINER - Match dengan layout yang ada */
        .container {
            flex: 0;
            max-width: 1400px;
            width: 100%;
            margin: 30px auto;
            padding: 0 20px;
            display: flex;
            gap: 25px;
        }
        
        /* SIDEBAR */
        .sidebar {
            width: 320px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            padding: 25px;
            height: fit-content;
            position: sticky;
            top: 30px;
        }
        
        .sidebar-header {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eef1f6;
        }
        
        .sidebar-header h1 {
            color: #2b8574;
            font-size: 24px;
            margin-bottom: 8px;
            font-weight: 700;
        }
        
        .sidebar-header p {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
        }
        
        /* ADMIN CARD */
        .admin-card {
            background: #f8f9fa;
            border-radius: 14px;
            padding: 20px;
            border: 2px solid #e3f2fd;
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }
        
        .admin-card:hover {
            border-color: #2b8574;
            box-shadow: 0 8px 25px rgba(43, 133, 116, 0.12);
        }
        
        .admin-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .admin-avatar {
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, #2b8574 0%, #3a9e8c 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
            font-weight: bold;
            flex-shrink: 0;
        }
        
        .admin-details h3 {
            color: #333;
            font-size: 17px;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .admin-details p {
            color: #666;
            font-size: 13px;
            margin-bottom: 8px;
        }
        
        .status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #28a745;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            background: #28a745;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        /* HELP TIPS */
        .help-tips {
            background: linear-gradient(135deg, #e8f5f3 0%, #f0f9ff 100%);
            border-radius: 14px;
            padding: 20px;
            border: 1px solid #d1e7dd;
        }
        
        .help-tips h4 {
            color: #2b8574;
            font-size: 15px;
            margin-bottom: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .help-tips h4:before {
            content: "💡";
        }
        
        .help-tips ul {
            list-style: none;
        }
        
        .help-tips li {
            color: #555;
            font-size: 13px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(209, 231, 221, 0.5);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .help-tips li:before {
            content: "✓";
            color: #2b8574;
            font-weight: bold;
        }
        
        .help-tips li:last-child {
            border-bottom: none;
        }
        
        /* CHAT AREA */
        .chat-area {
            flex: 1;
            background: white;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: calc(100vh - 160px);
        }
        
        /* CHAT HEADER */
        .chat-header {
            background: linear-gradient(135deg, #2b8574 0%, #3a9e8c 100%);
            padding: 20px 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
        }
        
        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }
        
        .chat-title {
            flex: 1;
        }
        
        .chat-title h2 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .chat-title p {
            font-size: 13px;
            opacity: 0.9;
        }
        
        /* MESSAGES CONTAINER */
        .messages {
            flex: 1;
            overflow-y: auto;
            padding: 25px 30px;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        
        /* MESSAGE STYLES */
        .message {
            display: flex;
            gap: 12px;
            max-width: 75%;
            animation: fadeIn 0.3s ease;
        }
        
        .message.user {
            margin-left: auto;
            flex-direction: row-reverse;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            font-size: 15px;
            flex-shrink: 0;
        }
        
        .avatar.admin {
            background: linear-gradient(135deg, #2b8574 0%, #3a9e8c 100%);
        }
        
        .avatar.user {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .avatar.bot {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: #333;
        }
        
        .message-content {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        
        .bubble {
            padding: 14px 18px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.5;
            word-wrap: break-word;
            max-width: 100%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .message.admin .bubble {
            background: white;
            color: #333;
            border: 1px solid #e9ecef;
            border-radius: 18px 18px 18px 4px;
        }
        
        .message.bot .bubble {
            background: #fff9db;
            color: #333;
            border: 1px solid #ffe066;
            border-radius: 18px 18px 18px 4px;
        }
        
        .message.user .bubble {
            background: linear-gradient(135deg, #2b8574 0%, #3a9e8c 100%);
            color: white;
            border-radius: 18px 18px 4px 18px;
        }
        
        .time {
            font-size: 11px;
            color: #999;
            padding: 0 6px;
            font-family: 'Courier New', monospace;
        }
        
        .message.user .time {
            text-align: right;
        }
        
        /* WELCOME MESSAGE */
        .welcome-box {
            background: white;
            border-radius: 16px;
            padding: 30px;
            margin: 20px auto;
            max-width: 600px;
            border: 2px solid #e9ecef;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        
        .welcome-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eef1f6;
        }
        
        .welcome-box h3 {
            color: #2b8574;
            margin-bottom: 15px;
            font-size: 20px;
            font-weight: 700;
        }
        
        .welcome-box p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .quick-links {
            list-style: none;
            padding-left: 0;
            margin: 25px 0;
        }
        
        .quick-links li {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            color: #555;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .quick-links li:hover {
            background: #f8f9fa;
            padding-left: 10px;
            border-radius: 8px;
        }
        
        .quick-links li strong {
            color: #2b8574;
        }
        
        .quick-links li:last-child {
            border-bottom: none;
        }
        
        /* TYPING INDICATOR */
        .typing {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 18px;
            background: #f8f9fa;
            border-radius: 18px;
            width: fit-content;
            margin-bottom: 10px;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }
        
        .typing-dot {
            width: 8px;
            height: 8px;
            background: #999;
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }
        
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }
        
        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-5px); }
        }
        
        /* INPUT AREA */
        .input-area {
            padding: 20px 30px;
            background: white;
            border-top: 1px solid #e9ecef;
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .message-input {
            flex: 1;
            padding: 14px 20px;
            border: 1px solid #e9ecef;
            border-radius: 25px;
            font-size: 15px;
            outline: none;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .message-input:focus {
            border-color: #2b8574;
            background: white;
            box-shadow: 0 0 0 3px rgba(43, 133, 116, 0.1);
        }
        
        .send-btn {
            background: linear-gradient(135deg, #2b8574 0%, #3a9e8c 100%);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 20px;
            box-shadow: 0 4px 15px rgba(43, 133, 116, 0.3);
        }
        
        .send-btn:hover:not(:disabled) {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(43, 133, 116, 0.4);
        }
        
        .send-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            box-shadow: none;
        }
        
        /* SCROLLBAR STYLING */
        .messages::-webkit-scrollbar {
            width: 8px;
        }
        
        .messages::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .messages::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }
        
        .messages::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* RESPONSIVE DESIGN */
        @media (max-width: 1024px) {
            .container {
                flex-direction: column;
                margin: 20px auto;
                padding: 0 15px;
            }
            
            .sidebar {
                width: 100%;
                position: static;
                margin-bottom: 20px;
            }
            
            .chat-area {
                height: 70vh;
            }
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 15px auto;
                padding: 0 10px;
            }
            
            .chat-header {
                padding: 15px 20px;
            }
            
            .messages {
                padding: 20px;
            }
            
            .message {
                max-width: 85%;
            }
            
            .welcome-box {
                padding: 20px;
                margin: 10px;
            }
            
            .input-area {
                padding: 15px 20px;
            }
        }
        
        @media (max-width: 480px) {
            .message {
                max-width: 90%;
            }
            
            .avatar {
                width: 35px;
                height: 35px;
                font-size: 13px;
            }
            
            .bubble {
                padding: 12px 15px;
                font-size: 13px;
            }
            
            .sidebar {
                padding: 20px;
            }
        }
        
        /* ANIMATIONS */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .sidebar {
            animation: slideIn 0.5s ease;
        }
        
        .chat-area {
            animation: slideIn 0.5s ease 0.1s backwards;
        }
    </style>
</head>
<body>

<header class="header">
    <?php include 'includes/nav.php'; ?>
</header>
    
    <!-- MAIN CONTAINER -->
    <div class="container">
        <!-- SIDEBAR -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h1>💬 Chat Support</h1>
                <p>Hubungi admin untuk bantuan</p>
            </div>
            
            <div class="admin-card">
                <div class="admin-info">
                    <div class="admin-avatar">R</div>
                    <div class="admin-details">
                        <h3>Reinayla</h3>
                        <p>Admin Lapangin.Aja</p>
                        <div class="status">
                            <div class="status-dot"></div>
                            <span>Online</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="help-tips">
                <h4>💡 Tips Cepat:</h4>
                <ul>
                    <li>Admin merespons dalam 1-5 menit</li>
                    <li>Siap bantu booking & informasi</li>
                    <li>Bot otomatis untuk info dasar</li>
                    <li>Bisa tanya harga & promo</li>
                </ul>
            </div>
        </div>
        
        <!-- CHAT AREA -->
        <div class="chat-area">
            <!-- CHAT HEADER -->
            <div class="chat-header">
                <button class="back-btn" onclick="window.history.back()">←</button>
                <div class="chat-title">
                    <h2>Reinayla</h2>
                    <p>Admin Support • 🟢 Online</p>
                </div>
            </div>
            
            <!-- MESSAGES -->
            <div class="messages" id="messagesContainer">
                <!-- WELCOME MESSAGE -->
                <div class="welcome-box">
                    <div class="welcome-header">
                        <div class="avatar admin">R</div>
                        <div>
                            <h3>👋 Halo, Selamat Datang!</h3>
                            <p style="font-size: 13px; color: #999;">Saya Reinayla siap membantu Anda</p>
                        </div>
                    </div>
                    
                    <p>Ada yang bisa saya bantu hari ini?</p>
                    
                    <ul class="quick-links">
                        <li>🏸 <strong>Cek ketersediaan lapangan</strong></li>
                        <li>💰 <strong>Info harga & promo terbaru</strong></li>
                        <li>📅 <strong>Booking lapangan online</strong></li>
                    
                    </ul>
                    
                    <p style="font-size: 13px; color: #2b8574; font-weight: 500;">
                        ⚡ Ketik pesan di bawah untuk memulai percakapan...
                    </p>
                </div>
            </div>
            
            <!-- TYPING INDICATOR (HIDDEN) -->
            <div class="typing" id="typingIndicator" style="display: none;">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <span style="font-size: 13px; color: #666;">Admin sedang mengetik...</span>
            </div>
            
            <!-- INPUT AREA -->
            <div class="input-area">
                <input type="text" 
                       class="message-input" 
                       id="messageInput" 
                       placeholder="Ketik pesan Anda di sini..." 
                       autocomplete="off">
                <button class="send-btn" id="sendButton" onclick="sendMessage()">
                    ➤
                </button>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
<script>
    // =========== KONFIGURASI ===========
    const USER_ID = <?php echo $user_id; ?>;
    const USER_NAME = "<?php echo addslashes($username); ?>";
    const ADMIN_NAME = "Reinayla";
    
    // =========== VARIABEL ===========
    let messages = [];
    let isTyping = false;
    let lastMessageTime = null; // Track pesan terakhir
    
    // =========== ELEMEN DOM ===========
    const messagesContainer = document.getElementById('messagesContainer');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    const typingIndicator = document.getElementById('typingIndicator');
    
    // =========== LOAD PESAN SEBELUMNYA ===========
  async function loadPreviousMessages() {
    try {
        const response = await fetch('admin/api/get_user_messages.php');
        const data = await response.json(); // Pastikan di-parse sebagai JSON

        if (data.success && data.messages.length > 0) {
            // Hapus isi container agar tidak double jika fungsi dipanggil ulang
            messagesContainer.innerHTML = ''; 
            
            data.messages.forEach(msg => {
                addMessage({
                    sender: msg.sender,
                    text: msg.message,
                    time: msg.time || msg.created_at
                }, false);
            });
            scrollToBottom();
        }
    } catch (err) {
        console.error('Gagal memuat pesan lama:', err);
    }
}
    // =========== AUTO REFRESH PESAN BARU ===========
    async function fetchNewMessages() {
        try {
            const response = await fetch('admin/api/get_user_messages.php');
            const data = await response.json();
            
            if (data.success && data.messages && data.messages.length > 0) {
                // Ambil pesan terakhir dari server
                const latestServerMessage = data.messages[data.messages.length - 1];
                const latestServerTime = latestServerMessage.created_at || latestServerMessage.time;
                
                // Cek apakah ada pesan baru
                if (lastMessageTime === null) {
                    // Pertama kali load, set lastMessageTime
                    lastMessageTime = latestServerTime;
                } else if (latestServerTime > lastMessageTime) {
                    // Ada pesan baru!
                    data.messages.forEach(msg => {
                        const msgTime = msg.created_at || msg.time;
                        if (msgTime > lastMessageTime) {
                            // Pesan baru dari admin/bot
                            if (msg.sender !== 'user') {
                                addMessage({
                                    sender: msg.sender,
                                    text: msg.message,
                                    time: msg.time || getCurrentTime()
                                });
                            }
                        }
                    });
                    lastMessageTime = latestServerTime;
                }
            }
        } catch (err) {
            console.error('Error fetching new messages:', err);
        }
    }
    
// =========== KIRIM PESAN ===========
async function sendMessage() {
    const text = messageInput.value.trim();
    if (!text) {
        messageInput.focus();
        return;
    }
    
    messageInput.disabled = true;
    sendButton.disabled = true;
    sendButton.innerHTML = '⏳';
    
    // 1. Tampilkan pesan user di layar
    addMessage({
        sender: 'user',
        text: text,
        time: getCurrentTime()
    });
    
    messageInput.value = '';
    
    try {
        const formData = new FormData();
        formData.append('message', text);
        
        const response = await fetch('admin/api/send_messages.php', {
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
        
        const data = await response.json();
        
        if (data.success) {
            // ✅ 2. Jika ada balasan Bot dari API
            if (data.reply) {
                // Beri jeda sedikit seolah bot sedang mengetik
                typingIndicator.style.display = 'flex';
                
                setTimeout(async () => {
                    typingIndicator.style.display = 'none';
                    
                    // Tampilkan balasan bot di layar
                    const botMsg = {
                        sender: 'bot',
                        text: data.reply.message,
                        time: data.reply.time || getCurrentTime()
                    };
                    addMessage(botMsg);

                    // ✅ 3. SIMPAN BALASAN BOT KE DATABASE
                    // Kita kirim lagi ke server dengan sender 'bot'
                    const botData = new FormData();
                    botData.append('message', botMsg.text);
                    botData.append('sender', 'bot'); 
                    await fetch('admin/api/send_messages.php', {
                        method: 'POST',
                        body: botData
                    });
                }, 1000);
            }
        } else {
            throw new Error(data.message || 'Gagal menyimpan pesan');
        }
        
    } catch (error) {
        console.error('❌ Error:', error);
        addMessage({
            sender: 'bot',
            text: '⚠️ Maaf, terjadi gangguan koneksi.',
            time: getCurrentTime()
        });
    } finally {
        messageInput.disabled = false;
        sendButton.disabled = false;
        sendButton.innerHTML = '➤';
        messageInput.focus();
    }
}
    // =========== TAMBAH PESAN KE TAMPILAN ===========
    function addMessage(msg, shouldScroll = true) {
        // Hapus welcome message jika ada
        const welcomeBox = messagesContainer.querySelector('.welcome-box');
        if (welcomeBox && msg.sender === 'user') {
            welcomeBox.remove();
        }
        
        // Cek duplikat berdasarkan waktu + text + sender
        const isDuplicate = messages.some(m => 
            m.time === msg.time && 
            m.text === msg.text && 
            m.sender === msg.sender
        );
        
        if (isDuplicate) {
            console.log('Duplicate message detected, skipping...');
            return;
        }
        
        // Buat elemen pesan
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${msg.sender}`;
        
        const avatarText = msg.sender === 'user' ? 
            (USER_NAME ? USER_NAME.charAt(0).toUpperCase() : 'U') : 
            (msg.sender === 'bot' ? 'B' : 'R');
        
        const senderName = msg.sender === 'user' ? USER_NAME :
                          msg.sender === 'bot' ? 'Bot' : ADMIN_NAME;
        
        // Escape HTML
        const escapedText = escapeHtml(msg.text).replace(/\n/g,'<br>');
        
        messageDiv.innerHTML = `
            <div class="avatar ${msg.sender}">${avatarText}</div>
            <div class="message-content">
                <div class="bubble">${escapedText}</div>
                <div class="time">${msg.time} • ${senderName}</div>
            </div>
        `;
        
        messagesContainer.appendChild(messageDiv);
        
        if (shouldScroll) {
            scrollToBottom();
        }
        
        // Simpan ke array messages
        messages.push(msg);
    }
    
    // =========== FUNGSI BANTUAN ===========
    function scrollToBottom() {
        setTimeout(() => {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }, 100);
    }
    
    function getCurrentTime() {
        const now = new Date();
        return now.getHours().toString().padStart(2, '0') + ':' + 
               now.getMinutes().toString().padStart(2, '0');
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // =========== EVENT LISTENERS ===========
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    
    // =========== INISIALISASI ===========
    document.addEventListener('DOMContentLoaded', function() {
        messageInput.focus();
        console.log('💬 Chat system ready! User:', USER_NAME, 'ID:', USER_ID);
        
        // Load pesan sebelumnya
        loadPreviousMessages();
        
        // Mulai auto-refresh setiap 3 detik
        setInterval(fetchNewMessages, 3000);
    });
</script>
</body>
</html>