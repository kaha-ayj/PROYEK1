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
<title>Admin Chat</title>
<style>
body{margin:0;font-family:Arial}
.admin-container{display:flex;height:100vh}
.user-sidebar{width:300px;border-right:1px solid #ddd;padding:10px;overflow:auto}
.user-item{padding:10px;border-radius:6px;cursor:pointer}
.user-item:hover{background:#f1f1f1}
.user-item.active{background:#e3f2fd}
.chat-main{flex:1;display:flex;flex-direction:column}
.chat-header{display:none;background:#2b8574;color:#fff;padding:15px}
.chat-messages{flex:1;display:none;padding:20px;background:#f7f7f7;overflow:auto}
.chat-input{display:none;padding:10px;border-top:1px solid #ddd;display:flex;gap:10px}
.chat-input input{flex:1;padding:10px}
.chat-input button{padding:10px 20px;background:#2b8574;color:#fff;border:none;cursor:pointer}
.message{margin-bottom:10px;max-width:70%}
.message.user{margin-right:auto}
.message.admin{margin-left:auto;text-align:right}
.message-bubble{padding:10px 15px;border-radius:15px;display:inline-block}
.message.user .message-bubble{background:#fff;border:1px solid #ddd}
.message.admin .message-bubble{background:#2b8574;color:#fff}
.message-time{font-size:11px;color:#888;margin-top:3px}
.empty-chat{text-align:center;color:#999;margin-top:50px}
</style>
</head>
<body>

<div class="admin-container">
<div class="user-sidebar">
<h3>💬 Percakapan</h3>
<div id="userList">Loading...</div>
</div>

<div class="chat-main">
<div id="emptyChat" class="empty-chat">👈 Pilih user</div>
<div id="chatHeader" class="chat-header"><strong id="currentUserName"></strong></div>
<div id="chatMessages" class="chat-messages"></div>
<div id="chatInput" class="chat-input">
<input type="text" id="adminMessageInput" placeholder="Ketik pesan...">
<button onclick="sendAdminMessage()">Kirim</button>
</div>
</div>
</div>

<script>
const API_GET_USERS = 'api/get_users.php';
const API_GET_MESSAGES = 'api/get_messages.php';
const API_SEND_MESSAGE = 'api/send_admin_message.php';
let currentUserId = null;

async function loadUserList(){
    const userList = document.getElementById('userList');
    try{
        const res = await fetch(API_GET_USERS);
        const json = await res.json();
        if(!json.success) throw 'Gagal load user';
        userList.innerHTML = json.data.map(u=>`
            <div class="user-item" onclick="selectUser(${u.user_id}, this)">
                <b>${u.username}</b><br>
                <small>${u.last_message || 'Belum ada pesan'}</small>
            </div>
        `).join('');
    }catch(err){
        console.error(err);
        userList.innerHTML='Gagal load user';
    }
}

function selectUser(userId, el){
    currentUserId = userId;
    document.querySelectorAll('.user-item').forEach(i=>i.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('emptyChat').style.display='none';
    document.getElementById('chatHeader').style.display='block';
    document.getElementById('chatMessages').style.display='block';
    document.getElementById('chatInput').style.display='flex';
    document.getElementById('currentUserName').innerText = el.querySelector('b').innerText;
    loadMessages(userId);
}

async function loadMessages(userId){
    const chatBox = document.getElementById('chatMessages');
    chatBox.innerHTML='';
    try{
        const res = await fetch(`${API_GET_MESSAGES}?penggunaID=${userId}`);
        const json = await res.json();
        if(!json.success){
            chatBox.innerHTML='<div class="empty-chat">Gagal memuat pesan</div>';
            return;
        }
        if(json.data.length===0){
            chatBox.innerHTML='<div class="empty-chat">Belum ada pesan</div>';
            return;
        }
        json.data.forEach(msg=>{
            const side = msg.sender==='admin'?'admin':'user';
            chatBox.innerHTML+=`
            <div class="message ${side}">
                <div class="message-bubble">${msg.message}</div>
                <div class="message-time">${msg.created_at}</div>
            </div>`;
        });
        chatBox.scrollTop = chatBox.scrollHeight;
    }catch(err){
        console.error(err);
        chatBox.innerHTML='<div class="empty-chat">Error memuat chat</div>';
    }
}

async function sendAdminMessage(){
    const input = document.getElementById('adminMessageInput');
    const message = input.value.trim();
    if(!message || !currentUserId) return;
    try{
        const res = await fetch(API_SEND_MESSAGE,{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({user_id:currentUserId,message})
        });
        const json = await res.json();
        if(!json.success){alert(json.message||'Gagal kirim pesan');return;}
        input.value='';
        loadMessages(currentUserId);
    }catch(err){
        console.error(err);
        alert('Error kirim pesan');
    }
}

loadUserList();
setInterval(()=>{if(currentUserId) loadMessages(currentUserId);},3000);
</script>

</body>
</html>
