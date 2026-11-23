<?php
$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>

<div class="flex gap-2 mb-4 overflow-x-auto pb-2 no-scrollbar">
    <span class="text-[10px] font-black text-slate-300 uppercase pt-1.5 tracking-widest mr-2">Mentions:</span>
    <?php
    $members = $conn->query("SELECT full_name FROM users LIMIT 6");
    while($m = $members->fetch_assoc()):
        $firstName = explode(' ', $m['full_name'])[0];
    ?>
        <button type="button" onclick="document.getElementById('chat_message').value += '@<?= $firstName ?> '" 
                class="text-[10px] font-bold bg-white hover:bg-orange-500 hover:text-white px-3 py-1.5 rounded-xl border border-slate-100 transition-all shadow-sm">
            @<?= $firstName ?>
        </button>
    <?php endwhile; ?>
</div>

<div class="bg-white rounded-[2.5rem] border border-slate-50 shadow-sm p-8 mt-4">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-black text-slate-800 flex items-center gap-3">
            Team Discussion 
            <span class="flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
            </span>
        </h3>
        <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">Live Sync Enabled</p>
    </div>
    
    <div id="chat-messages" class="h-96 overflow-y-auto mb-6 space-y-4 pr-2 custom-scrollbar flex flex-col">
        <div class="flex flex-col items-center justify-center h-full text-slate-300 italic text-sm">
            <p class="animate-pulse">Connecting to project stream...</p>
        </div>
    </div>

    <form id="chat-form" class="flex gap-3 bg-slate-50 p-2 rounded-[1.8rem] border border-slate-100 focus-within:border-orange-200 transition-all">
        <input type="hidden" name="project_id" id="chat_project_id" value="<?= $project_id ?>">
        <input type="text" name="message" id="chat_message" placeholder="Type a message for the team..." required autocomplete="off"
               class="flex-1 bg-transparent border-none px-4 py-3 text-sm focus:ring-0 outline-none text-slate-700 placeholder:text-slate-400">
        <button type="submit" class="bg-slate-900 text-white px-8 py-3 rounded-2xl font-black text-xs hover:bg-orange-600 hover:-translate-y-0.5 transition-all shadow-lg shadow-slate-200">
            SEND
        </button>
    </form>
</div>

<script>
// 1. Auto-Load Messages
function loadMessages() {
    const projectId = document.getElementById('chat_project_id').value;
    const chatBox = document.getElementById('chat-messages');
    
    fetch(`fetch_messages.php?project_id=${projectId}`)
        .then(response => response.text())
        .then(data => {
            chatBox.innerHTML = data;
            // Only scroll to bottom if user isn't scrolling up to read history
            chatBox.scrollTop = chatBox.scrollHeight;
        });
}

// 2. Handle Message Submission
document.getElementById('chat-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const msgInput = document.getElementById('chat_message');

    if(msgInput.value.trim() === "") return;

    fetch('save_comment.php', {
        method: 'POST',
        body: formData
    }).then(() => {
        msgInput.value = ''; 
        loadMessages(); 
    });
});

// 3. Keep Chat Updated every 3 seconds
setInterval(loadMessages, 3000);
// Initial load
loadMessages();
</script>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
</style>