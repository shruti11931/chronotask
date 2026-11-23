<?php
// Only Team Leaders should access this
if ($_SESSION['user_role'] !== 'Leader') {
    echo "Access Denied.";
    exit;
}
?>

<div class="max-w-2xl mx-auto bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100">
    <h2 class="text-2xl font-black text-slate-800 mb-6">Create New Project</h2>
    
    <form action="save_project.php" method="POST" class="space-y-6">
        <div>
            <label class="text-[10px] font-bold uppercase text-slate-400 tracking-widest ml-4">Project Name</label>
            <input type="text" name="project_name" required placeholder="Enter project title..." class="w-full mt-2 px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 ring-orange-500 text-sm font-semibold">
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="text-[10px] font-bold uppercase text-slate-400 tracking-widest ml-4">Contract Start</label>
                <input type="date" name="start_date" required class="w-full mt-2 px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 ring-orange-500 text-sm font-semibold">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase text-slate-400 tracking-widest ml-4">Contract Deadline</label>
                <input type="date" name="deadline" required class="w-full mt-2 px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 ring-orange-500 text-sm font-semibold">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="text-[10px] font-bold uppercase text-slate-400 tracking-widest ml-4">Internal Due Date</label>
                <input type="date" name="due_date" required class="w-full mt-2 px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 ring-orange-500 text-sm font-semibold">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase text-slate-400 tracking-widest ml-4">Initial Status</label>
                <select name="status" class="w-full mt-2 px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 ring-orange-500 text-sm font-semibold">
                    <option value="Ongoing">Ongoing</option>
                    <option value="On Hold">On Hold</option>
                    <option value="Resource">Resource Only</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="text-[10px] font-bold uppercase text-slate-400 tracking-widest ml-4">Assign Manager</label>
                <select name="manager_name" class="w-full mt-2 px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 ring-orange-500 text-sm font-semibold">
                    <?php
                    $users = $conn->query("SELECT full_name FROM users");
                    while($u = $users->fetch_assoc()) {
                        echo "<option value='".htmlspecialchars($u['full_name'])."'>".htmlspecialchars($u['full_name'])."</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase text-slate-400 tracking-widest ml-4">Initial Progress (%)</label>
                <input type="number" name="progress" min="0" max="100" value="0" class="w-full mt-2 px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 ring-orange-500 text-sm font-semibold">
            </div>
        </div>

        <button type="submit" class="w-full py-4 bg-orange-500 text-white rounded-2xl font-bold shadow-lg shadow-orange-500/30 hover:bg-orange-600 transition-all">
            Launch Project Roadmap
        </button>
    </form>
</div>