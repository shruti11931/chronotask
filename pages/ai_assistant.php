<?php
// pages/ai_assistant.php
?>
<div class="p-4">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">🤖 AI Project Assistant</h1>
            <p class="text-xs text-slate-400 font-bold uppercase">Get insights and advice based on your project data</p>
        </div>
        <div class="bg-purple-100 text-purple-700 px-4 py-2 rounded-full text-[10px] font-black tracking-widest uppercase">
            Groq Powered
        </div>
    </div>

    <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm mb-6">
        <label class="block text-[10px] font-black text-slate-400 uppercase mb-3 ml-2 tracking-widest">Select Project Context</label>
        <select id="ai_project_select" class="w-full p-4 rounded-2xl border-none bg-slate-50 focus:ring-2 ring-purple-500 outline-none font-bold text-slate-700">
            <option value="">-- Choose a Project --</option>
            <?php
            $projs = $conn->query("SELECT id, project_name FROM projects ORDER BY project_name ASC");
            while($p = $projs->fetch_assoc()) {
                echo "<option value='{$p['id']}'>{$p['project_name']}</option>";
            }
            ?>
        </select>
    </div>

    <div id="ai_chat_container" class="hidden animate-in fade-in duration-500">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden flex flex-col h-[550px]">
            <div id="ai-messages" class="flex-1 p-8 overflow-y-auto space-y-4 bg-slate-50/30 custom-scrollbar">
                </div>

            <div class="p-6 bg-white border-t border-slate-50">
                <div class="flex gap-3 bg-slate-50 p-2 rounded-3xl">
                    <input type="text" id="ai_question" placeholder="Ask me about progress or blockers..." 
                           class="flex-1 bg-transparent p-4 outline-none font-medium text-sm ml-2">
                    <button onclick="askAIAssistant()" id="ai-btn" class="bg-purple-600 text-white px-8 py-4 rounded-2xl font-bold hover:bg-purple-700 transition-all shadow-lg shadow-purple-200">
                        ASK
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// When a project is picked, show the chat and load history
document.getElementById('ai_project_select').addEventListener('change', function() {
    const container = document.getElementById('ai_chat_container');
    if(this.value) {
        container.classList.remove('hidden');
        loadAIMessages(this.value);
    } else {
        container.classList.add('hidden');
    }
});

function loadAIMessages(id) {
    const chatBox = document.getElementById('ai-messages');
    fetch(`fetch_messages.php?project_id=${id}&type=ai`)
        .then(res => res.text())
        .then(html => {
            chatBox.innerHTML = html;
            chatBox.scrollTop = chatBox.scrollHeight;
        });
}

async function askAIAssistant() {
    const id = document.getElementById('ai_project_select').value;
    const input = document.getElementById('ai_question');
    const q = input.value;
    const btn = document.getElementById('ai-btn');
    
    if(!q) return;
    
    btn.disabled = true;
    btn.innerHTML = "Thinking...";

    try {
        const response = await fetch('ajax/ask_ai.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ project_id: id, question: q })
        });
        const data = await response.json();
        
        input.value = '';
        btn.disabled = false;
        btn.innerHTML = "ASK";
        loadAIMessages(id);
    } catch (err) {
        alert("AI Connection Error. Check console.");
        btn.disabled = false;
        btn.innerHTML = "ASK";
    }
}
</script>