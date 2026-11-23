<?php
// Capture project_id from the URL button click
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;
?>

<div class="max-w-xl mx-auto bg-white p-8 rounded-3xl shadow-sm mt-10">
    <h2 class="text-2xl font-black text-slate-800 mb-6">Assign New Task</h2>
    
    <form action="save_task.php" method="POST" class="space-y-5">
        <input type="hidden" name="project_id" value="<?= $project_id ?>">

        <div>
            <label class="block text-sm font-bold text-slate-500 mb-2">Task Title</label>
            <input type="text" name="task_title" placeholder="What needs to be done?" required 
                   class="w-full p-4 bg-slate-50 border-none rounded-2xl focus:ring-2 ring-orange-500">
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-500 mb-2">Assign Team Member</label>
            <select name="assigned_to" required class="w-full p-4 bg-slate-50 border-none rounded-2xl focus:ring-2 ring-orange-500">
                <option value="">Select a member...</option>
                <?php
                $users = $conn->query("SELECT id, full_name FROM users");
                while($u = $users->fetch_assoc()) {
                    echo "<option value='{$u['id']}'>{$u['full_name']}</option>";
                }
                ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-500 mb-2">Deadline Date</label>
            <input type="datetime-local" name="deadline" required 
                   class="w-full p-4 bg-slate-50 border-none rounded-2xl focus:ring-2 ring-orange-500">
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold hover:bg-black transition-all">
                Send Task & Notification Email
            </button>
        </div>
    </form>
</div>