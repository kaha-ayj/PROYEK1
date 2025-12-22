<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['role']!=='admin'){
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Chat - Lapangin.Aja</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
    background: #f0f2f5;
}

.admin-container {
    display: flex;
    height: 100vh;
    max-width: 1400px;
    margin: 0 auto;
    background: white;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

/* SIDEBAR */
.user-sidebar {
    width: 320px;
    border-right: 1px solid #e5e7eb;
    display: flex;
    flex-direction: column;
    background: white;
}

.sidebar-header {
    padding: 20px;
    background: linear-gradient(135deg, #2b8574 0%, #3a9e8c 100%);
    color: white;
}

.sidebar-header h3 {
    font-size: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.sidebar-header p {
    font-size: 13px;
    opacity: 0.9;
    margin-top: 5px;
}

.user-list-container {
    flex: 1;
    overflow-y: auto;
}

#userList {
    padding: 10px;
}

.user-item {
    padding: 15px;
    border-radius: 10px;
    cursor: pointer;
    margin-bottom: 8px;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.user-item:hover {
    background: #f3f4f6;
    border-color: #e5e7eb;
}

.user-item.active {
    background: #e8f5f3;
    border-color: #2b8574;
}

.user-item b {
    display: block;
    color: #1f2937;
    font-size: 15px;
    margin-bottom: 5px;
}

.user-item small {
    color: #6b7280;
    font-size: 13px;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.unread-badge {
    display: inline-block;
    background: #ef4444;
    color: white;
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 12px;
    margin-left: 8px;
}

/* CHAT AREA */
.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #f9fafb;
}

.empty-chat {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
}

.empty-chat i {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.5;
}

.empty-chat p {
    font-size: 18px;
}

.chat-header {
    display: none;
    background: white;
    padding: 20px 25px;
    border-bottom: 1px solid #e5e7eb;
    align-items: center;
    gap: 15px;
}

.chat-header.active {
    display: flex;
}

.chat-header .avatar {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
    font-weight: bold;
}

.chat-header .user-info {
    flex: 1;
}

.chat-header .user-info strong {
    display: block;
    color: #1f2937;
    font-size: 16px;
}

.chat-header .user-info small {
    color: #6b7280;
    font-size: 13px;
}

.chat-messages {
    display: none;
    flex: 1;
    padding: 25px;
    overflow-y: auto;
    background: #f9fafb;
}

.chat-messages.active {
    display: block;
}

.message {
    margin-bottom: 15px;
    display: flex;
    gap: 10px;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.message.user {
    flex-direction: row;
}

.message.admin {
    flex-direction: row-reverse;
}

.message-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    font-weight: bold;
    flex-shrink: 0;
}

.message.user .message-avatar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.message.admin .message-avatar {
    background: linear-gradient(135deg, #2b8574 0%, #3a9e8c 100%);
}

.message-content {
    max-width: 65%;
}

.message-bubble {
    padding: 12px 16px;
    border-radius: 16px;
    font-size: 14px;
    line-height: 1.5;
    word-wrap: break-word;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.message.user .message-bubble {
    background: white;
    color: #1f2937;
    border: 1px solid #e5e7eb;
    border-radius: 16px 16px 16px 4px;
}

.message.admin .message-bubble {
    background: linear-gradient(135deg, #2b8574 0%, #3a9e8c 100%);
    color: white;
    border-radius: 16px 16px 4px 16px;
}

.message-time {
    font-size: 11px;
    color: #9ca3af;
    margin-top: 4px;
    padding: 0 4px;
}

.message.admin .message-time {
    text-align: right;
}

.chat-input {
    display: none;
    padding: 20px 25px;
    background: white;
    border-top: 1px solid #e5e7eb;
    gap: 12px;
}

.chat-input.active {
    display: flex;
}

.chat-input input {
    flex: 1;
    padding: 12px 18px;
    border: 1px solid #e5e7eb;
    border-radius: 24px;
    font-size: 14px;
    outline: none;
    transition: all 0.3s ease;
    background: #f9fafb;
}

.chat-input input:focus {
    border-color: #2b8574;
    background: white;
    box-shadow: 0 0 0 3px rgba(43, 133, 116, 0.1);
}

.chat-input button {
    padding: 12px 24px;
    background: linear-gradient(135deg, #2b8574 0%, #3a9e8c 100%);
    color: white;
    border: none;
    border-radius: 24px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(43, 133, 116, 0.3);
}

.chat-input button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(43, 133, 116, 0.4);
}

.chat-input button:active {
    transform: translateY(0);
}

/* SCROLLBAR */
.user-list-container::-webkit-scrollbar,
.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.user-list-container::-webkit-scrollbar-track,
.chat-messages::-webkit-scrollbar-track {
    background: #f3f4f6;
}

.user-list-container::-webkit-scrollbar-thumb,
.chat-messages::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

.user-list-container::-webkit-scrollbar-thumb:hover,
.chat-messages::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* LOADING */
.loading {
    text-align: center;
    padding: 20px;
    color: #9ca3af;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .user-sidebar {
        width: 100%;
        border-right: none;
    }
    
    .chat-main {
        display: none;
    }
    
    .chat-main.show {
        display: flex;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1000;
    }
}
</style>
</head>
<body>

<div class="admin-container">
    <!-- SIDEBAR USER LIST -->
    <div class="user-sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-comments"></i> Chat Admin</h3>
            <p>Kelola percakapan dengan pengguna</p>
        </div>
        <div class="user-list-container">
            <div id="userList" class="loading">
                <i class="fas fa-spinner fa-spin"></i> Memuat...
            </div>
        </div>
    </div>

    <!-- CHAT AREA -->
    <div class="chat-main">
        <div id="emptyChat" class="empty-chat">
            <i class="far fa-comments"></i>
            <p>Pilih percakapan untuk memulai</p>
        </div>
        
        <div id="chatHeader" class="chat-header">
            <div class="avatar" id="chatAvatar">U</div>
            <div class="user-info">
                <strong id="currentUserName">Username</strong>
                <small>Pengguna</small>
            </div>
        </div>
        
        <div id="chatMessages" class="chat-messages"></div>
        
        <div id="chatInput" class="chat-input">
            <input type="text" id="adminMessageInput" placeholder="Ketik pesan...">
            <button onclick="sendAdminMessage()">
                <i class="fas fa-paper-plane"></i> Kirim
            </button>
        </div>
    </div>
</div>

<script>
const API_GET_USERS = 'api/get_users.php';
const API_GET_MESSAGES = 'api/get_messages.php';
const API_SEND_MESSAGE = 'api/send_admin_message.php';
const API_MARK_READ = 'api/mark_read.php';

let currentUserId = null;
let lastMessageCount = 0;
let autoRefreshInterval = null;

// =========== LOAD USER LIST ===========
async function loadUserList() {
    const userList = document.getElementById('userList');
    try {
        const res = await fetch(API_GET_USERS);
        const json = await res.json();
        
        if (!json.success) {
            throw new Error(json.message || 'Gagal load user');
        }
        
        if (json.data.length === 0) {
            userList.innerHTML = '<div class="loading">Belum ada percakapan</div>';
            return;
        }
        
        userList.innerHTML = json.data.map(u => {
            const initial = u.username ? u.username.charAt(0).toUpperCase() : 'U';
            const lastMsg = u.last_message || 'Belum ada pesan';
            const lastTime = u.last_time ? new Date(u.last_time).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}) : '';
            
            return `
            <div class="user-item ${u.user_id === currentUserId ? 'active' : ''}" 
                 onclick="selectUser(${u.user_id}, '${u.username}', this)">
                <b>${u.username} ${lastTime ? `<small style="float:right;font-weight:normal;color:#999">${lastTime}</small>` : ''}</b>
                <small>${lastMsg}</small>
            </div>`;
        }).join('');
        
    } catch (err) {
        console.error('Error loading users:', err);
        userList.innerHTML = '<div class="loading">❌ Gagal memuat percakapan</div>';
    }
}

// =========== SELECT USER ===========
function selectUser(userId, username, el) {
    currentUserId = userId;
    
    // Update UI
    document.querySelectorAll('.user-item').forEach(i => i.classList.remove('active'));
    if (el) el.classList.add('active');
    
    // Show chat area
    document.getElementById('emptyChat').style.display = 'none';
    document.getElementById('chatHeader').classList.add('active');
    document.getElementById('chatMessages').classList.add('active');
    document.getElementById('chatInput').classList.add('active');
    
    // Update header
    const initial = username ? username.charAt(0).toUpperCase() : 'U';
    document.getElementById('currentUserName').innerText = username;
    document.getElementById('chatAvatar').innerText = initial;
    
    // Load messages
    loadMessages(userId);
    
    // Mark as read
    markAsRead(userId);
    
    // Start auto-refresh
    if (autoRefreshInterval) clearInterval(autoRefreshInterval);
    autoRefreshInterval = setInterval(() => loadMessages(userId, true), 3000);
}

// =========== LOAD MESSAGES ===========
async function loadMessages(userId, isAutoRefresh = false) {
    const chatBox = document.getElementById('chatMessages');
    
    // Simpan scroll position sebelumnya
    const wasAtBottom = chatBox.scrollHeight - chatBox.scrollTop <= chatBox.clientHeight + 50;
    
    try {
        const res = await fetch(`${API_GET_MESSAGES}?penggunaID=${userId}`);
        const json = await res.json();
        
        if (!json.success) {
            if (!isAutoRefresh) {
                chatBox.innerHTML = '<div class="empty-chat"><i class="fas fa-exclamation-circle"></i><p>Gagal memuat pesan</p></div>';
            }
            return;
        }
        
        if (json.data.length === 0) {
            chatBox.innerHTML = '<div class="empty-chat"><i class="far fa-comment-dots"></i><p>Belum ada pesan</p></div>';
            lastMessageCount = 0;
            return;
        }
        
        // Cek ada pesan baru atau tidak
        if (isAutoRefresh && json.data.length === lastMessageCount) {
            return; // Tidak ada pesan baru, skip render
        }
        
        lastMessageCount = json.data.length;
        
        // Render messages
        chatBox.innerHTML = json.data.map(msg => {
            const side = msg.sender === 'admin' ? 'admin' : 'user';
            const initial = side === 'admin' ? 'A' : (msg.sender_name ? msg.sender_name.charAt(0).toUpperCase() : 'U');
            const time = new Date(msg.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
            
            return `
            <div class="message ${side}">
                <div class="message-avatar">${initial}</div>
                <div class="message-content">
                    <div class="message-bubble">${escapeHtml(msg.message)}</div>
                    <div class="message-time">${time}</div>
                </div>
            </div>`;
        }).join('');
        
        // Auto-scroll jika sebelumnya di bawah atau ada pesan baru
        if (wasAtBottom || !isAutoRefresh) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
        
    } catch (err) {
        console.error('Error loading messages:', err);
        if (!isAutoRefresh) {
            chatBox.innerHTML = '<div class="empty-chat"><i class="fas fa-exclamation-circle"></i><p>Error memuat chat</p></div>';
        }
    }
}

// =========== SEND MESSAGE ===========
async function sendAdminMessage() {
    const input = document.getElementById('adminMessageInput');
    const message = input.value.trim();
    
    if (!message || !currentUserId) return;
    
    // Disable input
    input.disabled = true;
    
    try {
        const res = await fetch(API_SEND_MESSAGE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: currentUserId, message })
        });
        
        const json = await res.json();
        
        if (!json.success) {
            alert(json.message || 'Gagal kirim pesan');
            return;
        }
        
        // Clear input & reload messages
        input.value = '';
        loadMessages(currentUserId);
        loadUserList(); // Refresh user list untuk update last message
        
    } catch (err) {
        console.error('Error sending message:', err);
        alert('Error kirim pesan');
    } finally {
        input.disabled = false;
        input.focus();
    }
}

// =========== MARK AS READ ===========
async function markAsRead(userId) {
    try {
        const formData = new FormData();
        formData.append('user_id', userId);
        
        await fetch(API_MARK_READ, {
            method: 'POST',
            body: formData
        });
    } catch (err) {
        console.error('Error marking as read:', err);
    }
}

// =========== HELPER FUNCTIONS ===========
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML.replace(/\n/g, '<br>');
}

// =========== EVENT LISTENERS ===========
document.getElementById('adminMessageInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendAdminMessage();
    }
});

// =========== INIT ===========
loadUserList();
setInterval(loadUserList, 30000); // Refresh user list setiap 30 detik

console.log('💬 Admin Chat System Ready!');
</script>

</body>
</html>