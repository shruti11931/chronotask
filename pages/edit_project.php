<?php
if (!isset($_GET['id'])) { echo "Project ID missing"; exit; }
$id = (int)$_GET['id'];
$project = $conn->query("SELECT * FROM projects WHERE id = $id")->fetch_assoc();

if (!$project) { echo "Project not found"; exit; }
?>

<div class="max-w-2xl mx-auto bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-black text-slate-800">Edit Project Timeline</h2>
        <a href="index.php?page=timeline" class="text-xs font-bold text-slate-400 hover:text-slate-900">Cancel</a>
    </div>
    
    <form action="update_project.php" method="POST" class="space-y-6">
        <input type="hidden" name="project_id" value="<?= $project['id'] ?>">

        <div>
            <label class="text-[10px] font-bold uppercase text-slate-400 tracking-widest ml-4">Project Name</label>
            <input type="text" name="project_name" value="<?= htmlspecialchars($project['project_name']) ?>" required class="w-full mt-2 px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 ring-orange-500 text-sm font-semibold">
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="text-[10px] font-bold uppercase text-slate-400 tracking-widest ml-4">Contract Start</label>
                <input type="date" name="start_date" value="<?= $project['start_date'] ?>" required class="w-full mt-2 px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 ring-orange-500 text-sm font-semibold">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase text-slate-400 tracking-widest ml-4">Contract Deadline</label>
                <input type="date" name="deadline" value="<?= $project['deadline'] ?>" required class="w-full mt-2 px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 ring-orange-500 text-sm font-semibold">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="text-[10px] font-bold uppercase text-slate-400 tracking-widest ml-4">Progress (%)</label>
                <input type="number" name="progress" min="0" max="100" value="<?= $project['progress'] ?>" class="w-full mt-2 px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 ring-orange-500 text-sm font-semibold">
            </div>
            <div>
                <label class="text-[10px] font-bold uppercase text-slate-400 tracking-widest ml-4">Status</label>
                <select name="status" class="w-full mt-2 px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 ring-orange-500 text-sm font-semibold">
                    <option value="Ongoing" <?= $project['status'] == 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
                    <option value="Completed" <?= $project['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>
        </div>

        <button type="submit" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-bold shadow-lg hover:bg-orange-600 transition-all">
            Update Roadmap Positioning
        </button>
    </form>
</div>