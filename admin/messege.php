<?php
session_start();
include "../config/koneksi.php";

// ambil semua user yang pernah chat
$user_list = [];
$result_users = mysqli_query($conn, "SELECT DISTINCT user_id FROM chat ORDER BY user_id ASC");
while($row = mysqli_fetch_assoc($result_users)){
    $user_list[] = $row['user_id'];
}

// ambil user yang dipilih untuk chat (default user pertama)
$selected_user = $_GET['user_id'] ?? ($user_list[0] ?? null);

// ambil semua chat dengan user yang dipilih
$chats = [];
if($selected_user){
    $stmt = mysqli_prepare($conn, "SELECT * FROM chat WHERE user_id=? ORDER BY created_at ASC");
    mysqli_stmt_bind_param($stmt, "i", $selected_user);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while($row = mysqli_fetch_assoc($res)){
        $chats[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin - Lapangin.Aja</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
    --bg-light: #F0F4F8;
    --bg-sidebar: #DDE8F3;
    --text-dark: #2C3E50;
    --card-white: #FFFFFF;
    --header-blue: #AED6F1;
    --green: #2b8574;
}
body {
    font-family: 'Poppins', sans-serif;
    margin:0;
    display:flex;
    background-color: var(--bg-light);
    color: var(--text-dark);
    height:100vh;
}
.sidebar {
    width: 250px;
    background-color: var(--bg-sidebar);
    padding: 30px;
    display:flex;
    flex-direction:column;
    flex-shrink:0;
}
.sidebar .logo {
    font-size:24px;
    font-weight:700;
    margin-bottom:40px;
}
.sidebar nav a {
    display:block;
    text-decoration:none;
    color:var(--text-dark);
    font-size:18px;
    font-weight:600;
    margin-bottom:25px;
    opacity:0.7;
}
.sidebar nav a:hover,
.sidebar nav a.active{ opacity:1; }
.sidebar .profile{
    margin-top:auto;
    display:flex;
    align-items:center;
    font-weight:600;
}
.sidebar .profile-icon{
    width:40px;
    height:40px;
    background-color:#5D6D7E;
    border-radius:50%;
    margin-right:15px;
    background-image:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>');
    background-size:60%;
    background-position:center;
}
.main-content{
    flex-grow:1;
    padding:40px;
    overflow-y:auto;
    display:flex;
    flex-direction:row;
    gap:20px;
}
.user-list{
    width:30%;
    background-color:var(--card-white);
    border-radius:15px;
    padding:20px;
    box-shadow:0 4px 12px rgba(0,0,0,0.05);
    height:80vh;
    overflow-y:auto;
}
.user-item{
    padding:10px 15px;
    border-radius:8px;
    margin-bottom:10px;
    cursor:pointer;
    transition:0.2s;
}
.user-item:hover{
    background-color:#E7F2EF;
}
.user-item.active{
    background-color:var(--green);
    color:white;
}
.chat-area{
    flex-grow:1;
    background-color:var(--card-white);
    border-radius:15px;
    display:flex;
    flex-direction:column;
    height:80vh;
    box-shadow:0 4px 12px rgba(0,0,0,0.05);
}
.chat-header{
    padding:15px 20px;
    border-bottom:1px solid #ccc;
    font-weight:600;
    display:flex;
    align-items:center;
    gap:10px;
}
.messages{
    flex:1;
    padding:20px;
    overflow-y:auto;
    background-color:#f9f9f9;
}
.msg{
    display:flex;
    margin-bottom:10px;
}
.msg.user{ justify-content:flex-end; }
.msg.admin{ justify-content:flex-start; }
.msg .bubble{
    max-width:60%;
    padding:10px 15px;
    border-radius:10px;
    background-color:#fff;
    box-shadow:0 2px 3px rgba(0,0,0,0.1);
}
.msg.user .bubble{ background-color:var(--green); color:white; }
.input-area{
    display:flex;
    gap:10px;
    padding:10px 20px;
    border-top:1px solid #ccc;
}
.input-area input{
    flex:1;
    padding:10px 15px;
    border-radius:8px;
    border:1px solid #bbb;
    outline:none;
}
.input-area input:focus{
    border-color: var(--green);
}
.input-area button{
    background-color: var(--green);
    color:white;
    border:none;
    padding:10px 20px;
    border-radius:8px;
    cursor:pointer;
}
.input-area button:hover{
    background-color:#22685c;
}
</style>
</head>
<body>
<aside class="sidebar">
    <div class="logo">Lapangin.Aja</div>
    <nav>
        <a href="dashboard.php" >Dashboard</a>
        <a href="konfirmasi.php">Order</a>
        <a href="jadwal.php">Jadwal</a>
        <a href="lapangan.php">Lapangan</a>
        <a href="messege.php" class="active">Pesan</a>
    </nav>
    <div class="profile">
        <div class="profile-icon"></div>
        <span>Admin</span>
    </div>
</aside>

<main class="main-content">
    <div class="user-list">
        <h3>Daftar User</h3>
        <?php foreach($user_list as $uid): ?>
            <div class="user-item <?= $uid == $selected_user ? 'active' : '' ?>" data-user="<?= $uid ?>">
                User #<?= $uid ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="chat-area">
        <div class="chat-header">
            Chat dengan User #<?= $selected_user ?>
        </div>
        <div class="messages" id="chatBox">
            <?php foreach($chats as $chat):
                $senderClass = $chat['sender'] === 'user' ? 'user' : 'admin';
            ?>
            <div class="msg <?= $senderClass ?>">
                <div class="bubble"><?= htmlspecialchars($chat['message']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="input-area">
            <input type="text" id="messageInput" placeholder="Tulis pesan...">
            <button id="sendBtn">Kirim</button>
        </div>
    </div>
</main>

<script>
const chatBox = document.getElementById('chatBox');
const input = document.getElementById('messageInput');
const sendBtn = document.getElementById('sendBtn');

// pilih user
document.querySelectorAll('.user-item').forEach(el=>{
    el.addEventListener('click',()=>{
        const uid = el.getAttribute('data-user');
        window.location.href = 'dashboard_chat.php?user_id='+uid;
    });
});

// append message
function appendMessage(sender,text){
    if(!text) return;
    const div = document.createElement('div');
    div.className = 'msg '+sender;
    div.innerHTML = `<div class="bubble">${text}</div>`;
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
}

// kirim pesan
sendBtn.onclick = async function(){
    const text = input.value.trim();
    if(!text) return;
    appendMessage('admin', text);
    input.value='';

    const res = await fetch('admin_send_message.php',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({user_id: <?= $selected_user ?>, message:text})
    });
    const data = await res.json();
    if(data.reply) appendMessage('user', data.reply);
};

// scroll otomatis
window.onload = ()=>{ chatBox.scrollTop = chatBox.scrollHeight; }
</script>
</body>
</html>
