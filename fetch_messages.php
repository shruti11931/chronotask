<?php
session_start();
include 'db.php';

function formatMentions($text) {
    return preg_replace(
        '/@(\w+)/', 
        '<span class="bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded-md font-bold text-[11px]">@$1</span>', 
        $text
    );
}

$project_id = (int)$_GET['project_id'];
// Get the type of chat (defaults to 'team' if not specified)
$chat_type = isset($_GET['type']) ? $_GET['type'] : 'team';

if ($chat_type === 'ai') {
    // 1. QUERY FOR AI ASSISTANT: Only messages from AI (ID 99) or current user's AI questions
    $query = "SELECT m.user_id, m.message_text as msg, m.created_at, u.full_name 
              FROM messages m 
              JOIN users u ON m.user_id = u.id 
              WHERE m.project_id = $project_id 
              AND (m.user_id = 99 OR m.user_id = " . (int)$_SESSION['user_id'] . ")
              ORDER BY m.created_at ASC";
} else {
    // 2. QUERY FOR TEAM CHAT: Only human project comments
    $query = "SELECT c.user_id, c.message as msg, c.created_at, u.full_name 
              FROM project_comments c 
              JOIN users u ON c.user_id = u.id 
              WHERE c.project_id = $project_id 
              ORDER BY c.created_at ASC";
}

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $isMe = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']);
        $isAI = ($row['user_id'] == 99);

        $containerClass = $isMe ? 'justify-end' : 'justify-start';
        
        if ($isAI) {
            $bubbleClass = 'bg-purple-600 text-white rounded-tl-none shadow-md border-b-4 border-purple-800';
            $nameColor = 'text-purple-200';
            $displayName = "✨ PROJECT AI";
        } else {
            $bubbleClass = $isMe ? 'bg-slate-900 text-white rounded-tr-none' : 'bg-slate-50 text-slate-700 border border-slate-100 rounded-tl-none';
            $nameColor = $isMe ? 'text-slate-300' : 'text-orange-600';
            $displayName = htmlspecialchars($row['full_name']);
        }

        echo '
        <div class="flex ' . $containerClass . ' mb-4">
            <div class="max-w-[85%] ' . $bubbleClass . ' p-4 rounded-2xl shadow-sm">
                ' . (!$isMe ? '<p class="text-[10px] font-black ' . $nameColor . ' uppercase mb-1">' . $displayName . '</p>' : '') . '
                <p class="text-sm leading-relaxed">' . formatMentions(htmlspecialchars($row['msg'])) . '</p>
                <p class="text-[8px] opacity-40 mt-2 text-right">' . date('H:i', strtotime($row['created_at'])) . '</p>
            </div>
        </div>';
    }
} else {
    $msg = ($chat_type === 'ai') ? "Ask the AI a question about your project!" : "No messages yet. Start the conversation!";
    echo '<p class="text-center text-slate-300 text-xs py-10">' . $msg . '</p>';
}
?>