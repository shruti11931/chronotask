<?php $project_id = $_GET['project_id']; ?>
<div class="max-w-md mx-auto bg-white p-8 rounded-2xl shadow-sm">
    <h2 class="text-2xl font-black mb-6">Assign New Task</h2>
    <form action="save_task.php" method="POST" class="space-y-4">
        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
        
        <div>
            <label class="block text-sm font-bold mb-2">Task Title</label>
            <input type="text" name="task_title" required class="w-full p-3 border rounded-xl">
        </div>

        <div>
            <label class="block text-sm font-bold mb-2">Assign To Member</label>
            <select name="assigned_to" class="w-full p-3 border rounded-xl">
                <?php
                $members = $conn->query("SELECT id, full_name FROM users");
                while($row = $members->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['full_name']}</option>";
                }
                ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-bold mb-2">Deadline</label>
            <input type="date" name="deadline" required class="w-full p-3 border rounded-xl">
        </div>

        <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-xl font-bold">
            Send Task & Notification
        </button>
    </form>
</div>