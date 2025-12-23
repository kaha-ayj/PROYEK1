<?php
// Mencegah session hilang saat browser ditutup & memperpanjang umur session
ini_set('session.gc_maxlifetime', 86400); // 24 jam
session_set_cookie_params(86400);

session_start();

// Koneksi
include('config/koneksi.php');

// AMBIL ID SECARA STABIL (Jangan di-reset ke 0)
// Mengambil dari session 'user' (admin) atau session langsung (user biasa)
$user_id = $_SESSION['user']['penggunaID'] ?? $_SESSION['penggunaID'] ?? 0;

// Proteksi: Jika tidak ada session, lempar ke login
if ($user_id == 0) {
    header("Location: login.php"); 
    exit;
}

// Ambil Nama
$username = $_SESSION['user']['nama'] ?? $_SESSION['nama'] ?? "User";
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
        /* =========================
   RESPONSIVE FIX FINAL
========================= */

/* === TABLET & SMALL LAPTOP === */
@media (max-width: 1024px) {
    .container {
        flex-direction: column;
        gap: 20px;
    }

    .sidebar {
        width: 100%;
        position: relative;
        top: unset;
    }

    .chat-area {
        height: calc(100vh - 260px);
    }
}

/* === MOBILE LANDSCAPE & SMALL TABLET === */
@media (max-width: 768px) {
    body {
        overflow-x: hidden;
    }

    .container {
        padding: 0 10px;
        margin: 10px auto;
    }

    /* Sidebar jadi card atas */
    .sidebar {
        padding: 18px;
        border-radius: 14px;
    }

    .sidebar-header h1 {
        font-size: 20px;
    }

    .admin-card {
        padding: 15px;
    }

    .chat-area {
        height: calc(100vh - 300px);
        border-radius: 14px;
    }

    .chat-header {
        padding: 14px 16px;
    }

    .chat-title h2 {
        font-size: 16px;
    }

    .chat-title p {
        font-size: 12px;
    }

    .messages {
        padding: 16px;
    }

    .message {
        max-width: 90%;
    }

    .input-area {
        padding: 12px;
        gap: 10px;
    }

    .message-input {
        font-size: 14px;
        padding: 12px 16px;
    }

    .send-btn {
        width: 46px;
        height: 46px;
        font-size: 18px;
    }
}

/* === MOBILE PORTRAIT === */
@media (max-width: 480px) {
    .sidebar {
        padding: 15px;
    }

    .sidebar-header h1 {
        font-size: 18px;
    }

    .help-tips li {
        font-size: 12px;
    }

    .chat-area {
        height: calc(100vh - 320px);
    }

    .avatar {
        width: 32px;
        height: 32px;
        font-size: 12px;
    }

    .bubble {
        font-size: 13px;
        padding: 10px 14px;
    }

    .time {
        font-size: 10px;
    }

    .welcome-box {
        padding: 18px;
        margin: 10px 0;
    }

    .welcome-box h3 {
        font-size: 17px;
    }

    .welcome-box p {
        font-size: 13px;
    }

    .quick-links li {
        font-size: 13px;
    }
}

/* === FIX INPUT KE-ANGKAT SAAT KEYBOARD MOBILE === */
@media (max-width: 480px) {
    .input-area {
        position: sticky;
        bottom: 0;
        background: #fff;
        z-index: 10;
    }
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
    
    // ===== Jika admin, TARGET_USER_ID harus ditentukan (misal dari sidebar list user)
    const TARGET_USER_ID = <?php echo $user_id; ?>; // user biasa = USER_ID, admin ganti sesuai user yg dipilih
    
    // =========== VARIABEL ===========
    let messages = [];
    let lastMessageTime = null;

    // =========== ELEMEN DOM ===========
    const messagesContainer = document.getElementById('messagesContainer');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');

    // =========== LOAD PESAN SEBELUMNYA ===========
    async function loadPreviousMessages() {
        if (!TARGET_USER_ID) return;
        try {
            const res = await fetch(`admin/api/get_user_messages.php?user_id=${TARGET_USER_ID}`);
            const data = await res.json();

            if (data.success && data.messages.length > 0) {
                messagesContainer.innerHTML = '';
                messages = data.messages; // simpan array pesan
                data.messages.forEach(msg => addMessage({
                    sender: msg.sender,
                    text: msg.message,
                    time: msg.time || msg.created_at
                }, false));
                
                // update lastMessageTime
                const lastMsg = data.messages[data.messages.length - 1];
                lastMessageTime = lastMsg.created_at || lastMsg.time;

                scrollToBottom();
            }
        } catch(err) { console.error(err); }
    }

    // =========== FETCH PESAN BARU ===========
    async function fetchNewMessages() {
        if (!TARGET_USER_ID) return;
        try {
            const res = await fetch(`admin/api/get_user_messages.php?user_id=${TARGET_USER_ID}`);
            const data = await res.json();

            if (data.success && data.messages.length > 0) {
                const newMessages = data.messages.filter(msg => {
                    const msgTime = msg.created_at || msg.time;
                    return lastMessageTime === null || msgTime > lastMessageTime;
                });

                newMessages.forEach(msg => {
                    addMessage({
                        sender: msg.sender,
                        text: msg.message,
                        time: msg.time || msg.created_at
                    });
                });

                const lastMsg = data.messages[data.messages.length - 1];
                lastMessageTime = lastMsg.created_at || lastMsg.time;
            }
        } catch(err) { console.error(err); }
    }

    // =========== KIRIM PESAN ===========
    async function sendMessage() {
        const text = messageInput.value.trim();
        if (!text || !TARGET_USER_ID) return;

        messageInput.disabled = sendButton.disabled = true;
        sendButton.innerHTML = '⏳';

        try {
            const formData = new FormData();
            formData.append('message', text);
            formData.append('user_id', TARGET_USER_ID);

            const res = await fetch('admin/api/send_messages.php', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if (data.success) {
                messageInput.value = '';
                await loadPreviousMessages(); // update tampilan
            } else {
                alert("Gagal: " + (data.error || "Unknown error"));
            }

        } catch(err) {
            console.error(err);
        } finally {
            messageInput.disabled = sendButton.disabled = false;
            sendButton.innerHTML = '➤';
            messageInput.focus();
        }
    }

    // =========== TAMBAH PESAN KE TAMPILAN ===========
    function addMessage(msg, shouldScroll=true) {
        const welcomeBox = messagesContainer.querySelector('.welcome-box');
        if (welcomeBox) welcomeBox.remove();

        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${msg.sender}`;

        const avatarText = msg.sender === 'user' 
            ? (USER_NAME.charAt(0).toUpperCase()) 
            : (msg.sender === 'bot' ? 'B' : 'R');

        const senderName = msg.sender === 'user' ? 'Anda'
                          : msg.sender === 'bot' ? 'Bot'
                          : ADMIN_NAME;

        const escapedText = escapeHtml(msg.text).replace(/\n/g,'<br>');

        messageDiv.innerHTML = `
            <div class="avatar ${msg.sender}">${avatarText}</div>
            <div class="message-content">
                <div class="bubble">${escapedText}</div>
                <div class="time">${msg.time} • ${senderName}</div>
            </div>
        `;
        messagesContainer.appendChild(messageDiv);

        if (shouldScroll) scrollToBottom();
    }

    function scrollToBottom() {
        setTimeout(() => messagesContainer.scrollTop = messagesContainer.scrollHeight, 100);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // =========== EVENT LISTENERS ===========
    messageInput.addEventListener('keypress', e=>{
        if(e.key==='Enter' && !e.shiftKey){ e.preventDefault(); sendMessage(); }
    });

    document.addEventListener('DOMContentLoaded', () => {
        messageInput.focus();
        loadPreviousMessages();
        setInterval(fetchNewMessages, 3000);
        console.log('💬 Chat system ready! User:', USER_NAME, 'ID:', USER_ID);
    });
</script>

</body>
</html>