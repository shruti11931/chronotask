<?php
// 1. DATA FETCHING (Moved to top so variables exist before use)

// Energy Mapping: Initialize and Fetch
$energies = ['High' => 0, 'Medium' => 0, 'Low' => 0];
$energy_counts = $conn->query("SELECT energy_required, COUNT(*) as count FROM tasks WHERE status != 'Completed' GROUP BY energy_required");

if ($energy_counts) {
    while($row = $energy_counts->fetch_assoc()) { 
        if (isset($energies[$row['energy_required']])) {
            $energies[$row['energy_required']] = $row['count']; 
        }
    }
}

// Calculate total workload vs availability
$time_stats = $conn->query("SELECT 
    SUM(estimated_time) as total_planned, 
    (SELECT SUM(hours_spent) FROM time_logs) as total_actual 
    FROM tasks")->fetch_assoc();

$planned = $time_stats['total_planned'] ?? 0;
$actual = $time_stats['total_actual'] ?? 0;

// Fetch categorized resources for the Library
$resources = $conn->query("SELECT * FROM tasks WHERE (resource_links IS NOT NULL AND resource_links != '') OR status = 'Resource' ORDER BY id DESC");


// 2. INSIGHT LOGIC (Now it has data to work with)
$high_energy_count = $energies['High'];
$total_active = array_sum($energies);

// Determine the insight message dynamically
if ($high_energy_count >= 3) {
    $insight_msg = "🔥 Burnout Risk! You have $high_energy_count High-Focus tasks. Consider delegating.";
    $insight_class = "text-red-600 bg-red-50 border-red-100";
} elseif ($total_active > 0 && $high_energy_count == 0) {
    $insight_msg = "✅ Smooth Sailing. No high-energy bottlenecks detected for today.";
    $insight_class = "text-green-600 bg-green-50 border-green-100";
} else {
    $insight_msg = "💡 Peak Performance: Schedule your 'Medium' tasks between 2 PM - 4 PM.";
    $insight_class = "text-orange-600 bg-orange-50 border-orange-100";
}
?>


<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Resource Management</h2>
        <p class="text-sm text-slate-500 font-medium">Manage time, energy, and project documents in real-time.</p>
    </div>
    <button onclick="document.getElementById('uploadModal').style.display='block'" 
            class="bg-orange-500 text-white px-6 py-2 rounded-xl font-bold text-sm shadow-lg shadow-orange-200 hover:bg-orange-600 transition">
        + Upload Resource
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">⌛ Time Resource</h3>
        <div class="text-2xl font-black text-slate-800"><?= number_format($actual, 1) ?> / <?= number_format($planned, 1) ?> <span class="text-sm font-normal text-slate-400">hrs</span></div>
        <div class="w-full bg-slate-100 h-2 rounded-full mt-4 overflow-hidden">
            <div class="bg-orange-500 h-full transition-all duration-500" style="width: <?= ($planned > 0) ? min(($actual/$planned)*100, 100) : 0 ?>%"></div>
        </div>
        <?php if($actual > $planned && $planned > 0): ?>
            <p class="text-[10px] text-red-500 font-bold mt-2 uppercase italic">⚠️ Overload Warning</p>
        <?php endif; ?>
    </div>

    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">🔋 Energy Demand</h3>
        <div class="flex justify-between text-xs mb-2">
            <span class="text-slate-500 font-medium">High Focus Tasks:</span>
            <span class="font-bold text-orange-600"><?= $energies['High'] ?></span>
        </div>
        <div class="flex justify-between text-xs">
            <span class="text-slate-500 font-medium">Low Energy Tasks:</span>
            <span class="font-bold text-blue-500"><?= $energies['Low'] ?></span>
        </div>
    </div>

    <div class="bg-orange-50 p-6 rounded-[2rem] border border-orange-100 shadow-sm">
        <h3 class="text-[10px] font-bold text-orange-400 uppercase tracking-widest mb-2">💡 Chrono Insight</h3>
        <p class="text-xs text-slate-700 leading-relaxed">
            <?= ($energies['High'] > 3) ? "Burnout Risk! Delegate or reschedule High-Energy tasks." : "Workload looks healthy. Deep work recommended for 8-11 AM." ?>
        </p>
    </div>
</div>

<div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-50">
        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">📚 Study & Project Library</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-[10px] uppercase text-slate-400 font-black">
                <tr>
                    <th class="px-8 py-4">Resource & Version</th>
                    <th class="px-8 py-4">Category</th>
                    <th class="px-8 py-4">Status</th>
                    <th class="px-8 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php while($res = $resources->fetch_assoc()): ?>
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-8 py-5">
                        <div class="font-bold text-sm text-slate-800"><?= htmlspecialchars($res['task_title']) ?></div>
                        <div class="text-[10px] text-slate-400 uppercase">Added by <?= $res['uploaded_by'] ?? 'Admin' ?></div>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-[10px] bg-slate-100 px-2 py-1 rounded font-bold text-slate-500">
                            <?= $res['resource_category'] ?? 'General' ?>
                        </span>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-[10px] text-orange-500 font-black uppercase tracking-tighter"><?= $res['version_number'] ?? 'v1.0' ?></span>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <div class="flex justify-end gap-3">
                            <a href="<?= $res['resource_links'] ?>" target="_blank" class="text-blue-500 hover:scale-110 transition" title="Download/Open">📥</a>
                            <a href="delete_project.php?id=<?= $res['id'] ?>&type=res" onclick="return confirm('Delete resource?')" class="text-red-400 hover:scale-110 transition" title="Delete">🗑️</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="uploadModal" style="display:none;" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] p-8 w-full max-w-md shadow-2xl scale-in-center">
        <h3 class="text-xl font-bold mb-6 text-slate-800">Upload New Resource</h3>
        <form action="process_upload.php" method="POST" enctype="multipart/form-data">
            <div class="space-y-4">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase">Resource Name</label>
                    <input type="text" name="title" placeholder="e.g., SRS Document" required class="w-full bg-slate-50 border-none rounded-xl px-4 py-2 text-sm mt-1 focus:ring-2 ring-orange-500">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase">Category</label>
                    <select name="category" class="w-full bg-slate-50 border-none rounded-xl px-4 py-2 text-sm mt-1">
                        <option value="Document">📄 Official Document</option>
                        <option value="Link">🔗 Shared Link (GitHub/Figma)</option>
                        <option value="Code">💻 Tech Reference</option>
                        <option value="Knowledge">🧠 Knowledge Base</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase">Upload Image/File</label>
                    <input type="file" name="resource_file" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 mt-2">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase">Or Paste URL</label>
                    <input type="text" name="link" placeholder="https://..." class="w-full bg-slate-50 border-none rounded-xl px-4 py-2 text-sm mt-1">
                </div>
            </div>
            <div class="flex gap-3 mt-8">
                <button type="submit" class="flex-1 bg-orange-500 text-white font-bold py-3 rounded-xl hover:bg-orange-600 transition shadow-lg shadow-orange-200">Save Resource</button>
                <button type="button" onclick="document.getElementById('uploadModal').style.display='none'" class="flex-1 bg-slate-100 text-slate-500 font-bold py-3 rounded-xl hover:bg-slate-200 transition">Cancel</button>
            </div>
        </form>
    </div>
</div>