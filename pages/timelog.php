<?php
// "Real-time" fetch: Always gets the latest logs first
$logs_query = "SELECT tl.*, p.project_name, u.full_name 
               FROM time_logs tl
               LEFT JOIN projects p ON tl.project_id = p.id
               LEFT JOIN users u ON tl.user_id = u.id
               ORDER BY tl.log_date DESC, tl.id DESC"; // Added tl.id DESC for true latest entry
$logs = $conn->query($logs_query);
?>

<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Time Logs</h2>
        <p class="text-sm text-slate-500">Track hours spent on various projects</p>
    </div>
    <button class="bg-orange-500 text-white px-6 py-2 rounded-xl font-bold shadow-lg shadow-orange-500/20 hover:bg-orange-600 transition">
        + Log New Time
    </button>
</div>

<div class="bg-white p-8 rounded-[2rem] shadow-sm border border-orange-100 mb-10">
    <h3 class="text-lg font-bold text-slate-800 mb-4">Log Your Work</h3>
    <form action="process_timelog.php" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
    <div>
        <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block">Project</label>
        <select name="project_id" required class="w-full bg-slate-50 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 ring-orange-500">
            <?php 
            $p_list = $conn->query("SELECT id, project_name FROM projects");
            while($p = $p_list->fetch_assoc()) echo "<option value='{$p['id']}'>{$p['project_name']}</option>";
            ?>
        </select>
    </div>

    <div>
        <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block">Related Task</label>
        <select name="task_id" required class="w-full bg-slate-50 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 ring-orange-500">
            <?php 
            $t_list = $conn->query("SELECT id, task_title FROM tasks");
            while($t = $t_list->fetch_assoc()) echo "<option value='{$t['id']}'>{$t['task_title']}</option>";
            ?>
        </select>
    </div>

    <div>
        <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block">Hours</label>
        <input type="number" step="0.5" name="hours_spent" placeholder="0.0" required class="w-full bg-slate-50 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 ring-orange-500">
    </div>

    <div>
        <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block">Notes</label>
        <input type="text" name="task_description" placeholder="Notes..." required class="w-full bg-slate-50 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 ring-orange-500">
    </div>

    <button type="submit" class="bg-orange-500 text-white font-bold py-2 rounded-xl hover:bg-orange-600 transition shadow-lg shadow-orange-500/20">
        Save Log
    </button>
</form>
</div>
    <table class="w-full text-left">
        <thead>
            <tr class="text-slate-400 text-[11px] uppercase tracking-widest bg-slate-50">
                <th class="px-8 py-4">Date</th>
                <th class="px-8 py-4">User</th>
                <th class="px-8 py-4">Project</th>
                <th class="px-8 py-4">Task Description</th>
                <th class="px-8 py-4 text-center">Hours</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            <?php if ($logs && $logs->num_rows > 0): ?>
                <?php while($row = $logs->fetch_assoc()): ?>
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-8 py-5 text-sm font-medium text-slate-600">
                        <?= date('M d, Y', strtotime($row['log_date'])) ?>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-2">
                            <div class="h-7 w-7 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500">
                                <?= substr($row['full_name'], 0, 1) ?>
                            </div>
                            <span class="text-sm text-slate-700 font-semibold"><?= htmlspecialchars($row['full_name']) ?></span>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-xs font-bold text-orange-500 bg-orange-50 px-2 py-1 rounded-md">
                            <?= htmlspecialchars($row['project_name']) ?>
                        </span>
                    </td>
                    <td class="px-8 py-5 text-sm text-slate-500">
                        <?= htmlspecialchars($row['task_description']) ?>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <span class="font-bold text-slate-800"><?= $row['hours_spent'] ?></span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase">hrs</span>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="px-8 py-12 text-center text-slate-400 italic">
                        No time logs found. Start by logging some work!
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
        <script>
    // Simple auto-refresh for the timelog every 30 seconds
    setTimeout(function(){
       location.reload();
    }, 30000);
</script>
    </table>
</div>