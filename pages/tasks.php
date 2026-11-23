<?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
    <div class="mb-6 p-4 bg-green-500 text-white rounded-2xl font-bold text-center animate-bounce">
        ✨ Task updated and project progress synchronized!
    </div>
<?php endif; ?>

<div class="p-8">
    <h1 class="text-3xl font-black text-slate-800 mb-8">My Active Tasks</h1>
    
    <div class="grid gap-6">
        <?php 
        // Get user ID from session (Logged in as aarya, ID 11)
        $user_id = $_SESSION['user_id'] ?? 11;

        // Fetch tasks and project names
        $query = "SELECT t.*, p.project_name 
                  FROM tasks t 
                  JOIN projects p ON t.project_id = p.id 
                  WHERE t.assigned_to = '$user_id'
                  ORDER BY t.id DESC";
        
        $tasks = $conn->query($query);
        
        if ($tasks && $tasks->num_rows > 0):
            while($row = $tasks->fetch_assoc()): 
                $status = $row['status'] ?? 'In Progress';
        ?>
            <div class="bg-white p-8 rounded-[3rem] shadow-sm flex items-center justify-between border border-slate-50">
                <div class="flex items-center gap-6">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black py-1 px-3 bg-orange-100 text-orange-600 rounded-lg uppercase mb-2 self-start">
                            <?php echo htmlspecialchars($row['project_name']); ?>
                        </span>
                        <h3 class="text-2xl font-bold text-slate-800">
                            <?php echo htmlspecialchars($row['task_title']); ?>
                        </h3>
                        <p class="text-[10px] font-black text-slate-400 uppercase mt-1">
                            Current Status: <span class="text-blue-600"><?php echo htmlspecialchars($status); ?></span>
                        </p>
                    </div>
                </div>
                
                <form method="POST" action="update_task_status.php" class="flex items-center gap-6">
                    <input type="hidden" name="task_id" value="<?php echo $row['id']; ?>">
                    
                    <div class="flex flex-col items-end">
                        <label class="text-[9px] font-black text-slate-300 uppercase mb-1">Set Status</label>
                        <select name="status" class="p-3 bg-slate-50 border border-slate-100 rounded-2xl font-bold outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="In Progress" <?php if($status == 'In Progress') echo 'selected'; ?>>In Progress</option>
                            <option value="Completed" <?php if($status == 'Completed') echo 'selected'; ?>>Completed</option>
                            <option value="Pending" <?php if($status == 'Pending') echo 'selected'; ?>>Pending</option>
                        </select>
                    </div>

                    <button type="submit" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-sm hover:bg-orange-600 transition-all shadow-lg">
                        Update
                    </button>
                </form>
                </div>
        <?php endwhile; ?>
        <?php else: ?>
            <div class="py-24 text-center bg-white rounded-[4rem] border-2 border-dashed border-slate-100">
                <p class="text-slate-400 font-bold text-xl">No tasks assigned to you (User ID: <?php echo $user_id; ?>)</p>
            </div>
        <?php endif; ?>
    </div>
</div>