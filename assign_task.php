<?php
// Capture project_id from the button click
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;
?>
<div class="p-10 bg-white rounded-[3rem] shadow-sm max-w-2xl mx-auto">
    <h2 class="text-2xl font-black mb-6 text-slate-800">Assign Task for Project #<?= $project_id ?></h2>
    
    <form action="save_task.php" method="POST" class="space-y-4">
        <input type="hidden" name="project_id" value="<?= $project_id ?>">
        
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Task Title</label>
            <input type="text" name="task_title" required class="w-full p-4 bg-slate-50 border-none rounded-2xl ring-1 ring-slate-100 focus:ring-2 focus:ring-orange-500 outline-none">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Assign To Member</label>
            <select name="assigned_to" required class="w-full p-4 bg-slate-50 border-none rounded-2xl ring-1 ring-slate-100 focus:ring-2 focus:ring-orange-500 outline-none">
                <?php
                $users = $conn->query("SELECT id, full_name FROM users");
                while($u = $users->fetch_assoc()) {
                    echo "<option value='{$u['id']}'>{$u['full_name']}</option>";
                }
                ?>
            </select>
        </div>

        <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold hover:bg-black transition-all">
            Assign & Send Notification
        </button>
    </form>
</div>